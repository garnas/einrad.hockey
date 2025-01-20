<?php

namespace App\Event\Turnier;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Repository\DoctrineWrapper;
use App\Service\Team\TeamService;
use App\Service\Turnier\BlockService;
use App\Service\Turnier\TurnierService;
use Config;
use db;
use Discord;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\Mapping\MappingException;
use Html;

/**
 *
 */
class nLigaBot
{

    /** @var Turnier[] $gelosteTurniere */
    private static array $gelosteTurniere = [];

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws MappingException
     */
    public static function ligaBot(): void
    {
        $_SESSION['logins']['ligabot'] = 'Ligabot'; // Wird in den logs als Autor verwendet
        db::sql_backup();

        self::setSpieltage();
//        self::blockWechsel(); # Der Blockwechsel wird Moduskonform 23/24 nur noch mit Ende eines Spieltages vollzogen
        self::phasenWechsel();

        DoctrineWrapper::manager()->flush();


        unset($_SESSION['logins']['ligabot']);
        Html::info("Ligabot erfolgreich ausgeführt");
    }

    /**
     * Turnierblock wandern lassen
     */
    public static function blockWechsel(): void
    {
        /** @var Turnier[] $turniere */
        $turniere = DoctrineWrapper::manager()->createQueryBuilder()
            ->select('t', 'l','ausrichter')
            ->from(Turnier::class, 't')
            ->join('t.liste', 'l')
            ->join('t.ausrichter', 'ausrichter')
            ->where('t.canceled = 0')
            ->andWhere("t.art = 'I' OR t.art = 'II'")
            ->andWhere("t.phase = 'warte'")
            ->andWhere("t.saison = :saison")
            ->setParameter("saison", Config::SAISON)
            ->getQuery()
            ->execute()
        ;

        foreach ($turniere as $turnier) { // Schleife durch alle Turniere

            $ausrichterBlock = $turnier->getAusrichter()->getBlock();
            $turnierBlock = $turnier->getBlock();

            // Position des Ausrichters in einem Array aller Blöcke in der Klasse Config, um Blockhöhere und erweiterte
            // Turniere erkennen zu können
            $rangAusrichterBlock = array_search($ausrichterBlock, Config::BLOCK);
            $rangTurnierBlock = array_search($turnierBlock, Config::BLOCK);

            if ($rangAusrichterBlock === false || $rangTurnierBlock === false) {
                Html::error("Ligabot konnte nicht die Differenz der Turnierblöcke ermitteln.");
                trigger_error("Ligabot konnte nicht die Differenz der Turnierblöcke ermitteln.", E_USER_WARNING);
            }

            if (
                $turnier->getArt() === 'I'
                && $turnier->isWartePhase()
                && $turnierBlock != $ausrichterBlock
            ) {
                $turnier->setBlock($ausrichterBlock);
            }
            if (
                $turnier->getArt() === 'II'
                && $turnier->isWartePhase()
                && $rangAusrichterBlock < $rangTurnierBlock
            ) {
                $turnier->setBlock($ausrichterBlock);
            }
            DoctrineWrapper::manager()->persist($turnier);
        }
        DoctrineWrapper::manager()->flush();
    }

    /**
     * @return void
     * @throws ORMException
     * @throws MappingException
     */
    public static function setSpieltage(): void
    {
        /** @var Turniere[] $turniere */
        $turniere = DoctrineWrapper::manager()->createQueryBuilder()
            ->select('t')
            ->from(Turnier::class, 't')
            ->where("t.art = 'I' OR t.art = 'II'")
            ->andWhere("t.canceled = 0")
            ->andWhere("t.saison = :saison")
            ->orderBy('t.datum', 'asc')
            ->setParameter('saison', Config::SAISON)
            ->getQuery()
            ->execute();

        // Initierung
        $spieltag = 0;
        $kw_vgl = 0; // Kalenderwoche als Hilfsmittel um Spieltage zu bestimmen

        foreach ($turniere as $turnier) { // Schleife durch alle Turniere
            $wochentag = $turnier->getDatum()->format('N'); // Wochentage nummeriert 1 - 7
            $kw = $turnier->getDatum()->format('W'); // Kalenderwoche nummeriert
            // Man muss auch Turniere berücksichtigen, welche nicht am Wochende sind:
            if ($kw_vgl != $kw) {
                ++$spieltag;
            }
            // Turniere die Mo oder Di stattfinden werden dem vorherigen Spieltag zugeordnet
            if (($wochentag < 3) && $kw - $kw_vgl <= 1) {
                $spieltag = ($spieltag === 0) ? 1 : $spieltag - 1; // Ansonsten würde Spieltag 0 vergeben, wenn das erste Turnier nicht an einem WE stattfindet
            }
            $turnier->setSpieltag($spieltag);
            $kw_vgl = $kw; // Kalenderwoche übernehmen für die nächste Iteration
            DoctrineWrapper::manager()->persist($turnier);
        }
        DoctrineWrapper::manager()->flush();
        DoctrineWrapper::manager()->clear();
    }

    /**
     * @throws ORMException
     */
    public static function phasenWechsel(): void
    {
        /** @var Turnier[] $turniere */
        $turniere = DoctrineWrapper::manager()->createQueryBuilder()
            ->select('t', 'l', 'team', 'd', 'teamDetails', 'teamEmails')
            ->from(Turnier::class, 't')
            ->leftJoin('t.liste', 'l')
            ->join('t.details', 'd')
            ->join('l.team', 'team')
            ->leftJoin('team.details', 'teamDetails')
            ->leftJoin('team.emails', 'teamEmails')
            ->where('t.canceled = 0')
            ->andWhere("t.art = 'I' OR t.art = 'II'")
            ->andWhere("t.phase = 'warte'")
            ->andWhere("t.saison = :saison")
            ->setParameter("saison", Config::SAISON)
            ->getQuery()
            ->execute()
        ;

        shuffle($turniere);

        foreach ($turniere as $turnier) {
            if (
                $turnier->isWartePhase()
                && TurnierService::warteToSetzUnix($turnier) <= time()
            ) {
                $turnier->setPhase('setz');

                // Losen setzt alle Teams in richtiger Reihenfolge auf die Warteliste.
                self::losen($turnier);
                self::$gelosteTurniere[] = $turnier;

                if($turnier->isSofortOeffnen()) {
                    TurnierService::blockOeffnen($turnier);
                    TurnierService::setzListeAuffuellen($turnier, false);
                }

                // Info-Mails versenden.
                TurnierEventMailBot::mailGelost($turnier);
                if (TurnierService::hasFreieSetzPlaetze($turnier)) {
                    TurnierEventMailBot::mailPlaetzeFrei($turnier);
                }

            }
            DoctrineWrapper::manager()->persist($turnier);
        }
        DoctrineWrapper::manager()->flush();
        DoctrineWrapper::manager()->clear();
    }

    /**
     * Regelt den Übergang von offen zu melden bezüglich der Teamlisten.
     * setzt die Teams in geloster Reihenfolge auf die Warteliste, also danach: Spielen-Liste auffuellen!
     * @param Turnier $turnier
     *
     */
    public static function losen(Turnier $turnier): void
    {

        // Falsche Freilosanmeldungen beim Übergang in die Meldephase abmelden
        $setzListe = TurnierService::getSetzListe($turnier);
        foreach ($setzListe as $anmeldung) {
            // Das Team hat ein Freilos gesetzt, aber den falschen Freilosblock. Nicht mehr im Modus vorhanden,
            // aber interessant zu wissen wie oft es vorkommt
            $team = $anmeldung->getTeam();
            if ($anmeldung->hasFreilosGesetzt() && !TurnierService::isSpielBerechtigtFreilos($turnier, $team)) {
                Discord::send("'Falsches' Freilos eingetreten. "
                    . $team->getName() . " in " . $turnier->getDetails()->getOrt()
                );
            }
        }

        if (TurnierService::hasFreieSetzPlaetze($turnier)) {
            $turnier->getLogService()->addLog("Turnierplätze werden verlost.");
        }

        // 3 Lostöpfe für Nichtligateams, Teams mit richtigem Block und Teams mit falschem Block
        $lostopNLTeams = $losttopRichtigerBlock = $lostopfFalscherBlock = [];
        $warteliste = TurnierService::getWarteliste($turnier);
        foreach ($warteliste as $anmeldung) {
            $team = $anmeldung->getTeam();
            if (!$team->isLigaTeam()) {
                $lostopNLTeams[] = $anmeldung;
            } elseif (BlockService::isBlockPassend($turnier, $team)) {
                $losttopRichtigerBlock[] = $anmeldung;
            } else {
                $lostopfFalscherBlock[] = $anmeldung;
            }
        }

        // Losen durch "mischen" der Losttöpfe
        shuffle($losttopRichtigerBlock);
        shuffle($lostopfFalscherBlock);
        shuffle($lostopNLTeams);

        // Zusammenstellen der neuen Warteliste
        /* @var $reihenfolgeAnmeldungen TurniereListe[] */
        $reihenfolgeAnmeldungen = array_merge($losttopRichtigerBlock, $lostopNLTeams, $lostopfFalscherBlock);

        $turnier->getLogService()->addLog("Geloste Reihenfolge:");
        $turnier->getLogService()->addLog("-----");
        foreach($reihenfolgeAnmeldungen as $anmeldung) {
            $turnier->getLogService()->addLog($anmeldung->getTeam()->getName());
        }
        $turnier->getLogService()->addLog("-----");

        $pos = 1;
        foreach ($reihenfolgeAnmeldungen as $anmeldung) {
            $team = $anmeldung->getTeam();
            $name = $team->getName();
            $blockString = BlockService::toString($team->getBlock());
            // Check ob das Team am Kalendertag des Turnieres schon auf einer Spiele-Liste steht
            if (self::isBereitsAufSetzlisteGelostAmGleichenKalendertag($turnier, $team)) {
                $turnier->getLogService()->addLog(
                    "$name war schon auf einer Setzliste eines anderen Turnieres und wurde daher abgemeldet."
                );
                $turnier->getListe()->removeElement($anmeldung);
                TurnierEventMailBot::mailDoppelAnmeldung($turnier, $team);
            } else if (
                TurnierService::hasFreieSetzPlaetze($turnier)
                && TurnierService::isSetzBerechtigt($turnier, $team)
            ) {
                $anmeldung->setListe('setzliste');
                $turnier->getLogService()->addLog("Gesetzt: $name $blockString");
            } else {
                $anmeldung->setListe('warteliste');
                $anmeldung->setPositionWarteliste($pos);
                $turnier->getLogService()->addLog("Wartend: $pos. $name $blockString");
                $pos++;
            }
        }
    }

    public static function isBereitsAufSetzlisteGelostAmGleichenKalendertag(Turnier $turnier, nTeam $team): bool {
        foreach (self::$gelosteTurniere as $vglTurnier) {
            if (
                $turnier->getDatum() == $vglTurnier->getDatum()
                && TeamService::isAufSetzliste($team, $vglTurnier)
            ) {
                return true;
            }
        }
        return false;
    }

}