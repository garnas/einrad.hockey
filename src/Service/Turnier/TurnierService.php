<?php

namespace App\Service\Turnier;

use App\Entity\Team\Kontakt;
use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Entity\Turnier\TurnierErgebnis;
use App\Event\Turnier\TurnierEventMailBot;
use App\Repository\Team\TeamRepository;
use App\Service\Team\NLTeamValidator;
use Config;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Jenssegers\Date\Date;

class TurnierService
{
    public static function isAusrichter(Turnier $turnier, int $teamId): bool
    {
        return $turnier->getAusrichter()->id() === $teamId;
    }

    public static function hasFreieSetzPlaetze(Turnier $turnier): bool
    {
        return self::getFreieSetzPlaetze($turnier) > 0;
    }

    public static function hasNlTeamErgebnis(Turnier $turnier): bool
    {
        $ergebnisse = $turnier->getErgebnis();
        foreach ($ergebnisse as $ergebnis) {
            if (!$ergebnis->getTeam()->isLigaTeam()) {
                return true;
            }
        }
        return false;
    }

    public static function isLosen(Turnier $turnier): bool
    {
        if (!$turnier->isWartePhase()) {
            return false;
        }
        return $turnier->getDetails()->getPlaetze() < self::getAnzahlAngemeldeteTeams($turnier);
    }

    public static function getAnzahlAngemeldeteTeams(Turnier $turnier): int
    {
        return $turnier->getListe()->count();
    }

    /**
     * @param Turnier $turnier
     * @return Collection|TurniereListe[]
     */
    public static function getSetzListe(Turnier $turnier): Collection|array
    {
        $criteria = Criteria::create()
            ->andWhere((Criteria::expr())->eq('liste', 'setzliste'));

        return $turnier->getListe()->matching($criteria);
    }

    /**
     * @param Turnier $turnier
     * @return Collection|TurniereListe[]
     */
    public static function getWarteliste(Turnier $turnier): Collection|array
    {
        $criteria = Criteria::create()
            ->andWhere((Criteria::expr())->eq('liste', 'warteliste'))
            ->orderBy(["positionWarteliste" => Criteria::ASC]);
        return $turnier->getListe()->matching($criteria);
    }

    public static function getTurnierEintrageFristUnix(Turnier $turnier): int
    {
        return self::warteToSetzUnix($turnier);
    }

    public static function getAbmeldeFristUnix(Turnier $turnier): int
    {
        $warte_zu_setz = new DateTimeImmutable('@' . ((string) self::warteToSetzUnix($turnier)));
        return (int) $warte_zu_setz->modify("+2 weeks")->format('U');
    }

    public static function getAbmeldeFrist(Turnier $turnier): string
    {
        $unixTime = self::getAbmeldeFristUnix($turnier) - 1;
        Date::setLocale('de');
        return Date::createFromTimestamp($unixTime)->format("l, d.m.Y - H:i") . " Uhr";
    }

    public static function warteToSetzUnix(Turnier $turnier): int
    {
        $turnier_datum = DateTimeImmutable::createFromMutable($turnier->getDatum());

        $datum_warte_zu_setzphase = $turnier_datum->modify('-4 weeks');
        $tag = (int) $datum_warte_zu_setzphase->format('N'); // Numerische Zahl des Wochentages 1-7

        # Findet das Turnier am Mittwoch oder später statt, wird es dem nächsten Wochenende zugeordnet
        # Mi == 3 -> hochrechnen auf nächsten Samstag also +(6-3) Tage
        if ($tag >= 3) {
            $delta = (string) (6 - $tag);
            return (int) $datum_warte_zu_setzphase->modify("+$delta days")->format("U");
        }
        # Findet das Turnier am Montag oder Dienstag statt, wird es dem vorherigen Wochenende zugeordnet
        # Di == 2 -> herunterrechnen auf letzten Samstag also -(2+1) Tage
        $delta = (string) (1 + $tag);
        return (int) $datum_warte_zu_setzphase->modify("-$delta days")->format("U");
    }

    public static function getLosDatum(Turnier $turnier): string
    {
        $unixTime = self::warteToSetzUnix($turnier) - 1;
        Date::setLocale('de');
        return Date::createFromTimestamp($unixTime)->format("l, d.m.Y - H:i") . " Uhr";
    }

    public static function isSetzBerechtigt(Turnier $turnier, nTeam $team): bool
    {
        if (!$team->isLigaTeam() && NLTeamValidator::isValidNLAnmeldungListe($turnier, "setzliste")) {
            return true;
        }
        return BlockService::isBlockPassend($turnier, $team);
    }

    /**
     * Ermittelt, ob ein Team bei diesem Turnier ein Freilos setzten könnte
     * @param Turnier $turnier
     * @param nTeam $team
     * @return bool
     */
    public static function isSpielBerechtigtFreilos(Turnier $turnier, nTeam $team): bool
    {
        if (self::isSetzBerechtigt($turnier, $team)) {
            return true;
        }

        return BlockService::isTurnierBlockHigher($turnier, $team);
    }

    public static function addToSetzListe(Turnier $turnier, nTeam $team): void
    {
        $anmeldung = new TurniereListe();
        $anmeldung->setTeam($team)
            ->setListe('setzliste')
            ->setTurnier($turnier)
            ->setFreilosGesetzt('Nein');
        $turnier->getListe()->add($anmeldung);
        $turnier->getLogService()->addLog("Auf Setzliste: " . $team->getName() . " " . BlockService::toString($team));
    }

    public static function nlAnmelden(Turnier $turnier, nTeam $nlTeam, string $liste): void
    {
        if ($nlTeam->isLigaTeam()) {
            trigger_error("Ligateam soll als NL-Team angemeldet werden", \E_USER_ERROR);
        }
        if ($liste === "warteliste") {
            self::addToWarteListe($turnier, $nlTeam);
        } elseif ($liste === "setzliste") {
            self::addToSetzListe($turnier, $nlTeam);
        } else {
            trigger_error("Falsche Liste", \E_USER_ERROR);
        }
    }

    public static function addToWarteListe(Turnier $turnier, nTeam $team): void
    {
        $positionWarteliste = $turnier->isWartePhase() ? null : self::getAnzahlWartelisteTeams($turnier) + 1;

        $anmeldung = new TurniereListe();
        $anmeldung->setTeam($team)
            ->setListe('warteliste')
            ->setTurnier($turnier)
            ->setFreilosGesetzt('Nein')
            ->setPositionWarteliste($positionWarteliste);

        $turnier->getListe()->add($anmeldung);
        $turnier->getLogService()->addLog(
            "Auf Warteliste: "
            . ($positionWarteliste ? $positionWarteliste . ". " : "")
            . $team->getName()
            . " " . BlockService::toString($team),
        );
    }

    public static function neueWartelistePositionen(Turnier $turnier): void
    {
        $warteliste = self::getWarteListe($turnier);
        $pos = 0;
        foreach ($warteliste as $anmeldung) {
            $anmeldung->setPositionWarteliste(++$pos);
            $name = $anmeldung->getTeam()->getName();
            $turnier->getLogService()->addLog("Warteliste: $pos. $name");
        }
    }

    public static function cancel(Turnier $turnier, string $grund): void
    {
        $turnier->setCanceledGrund($grund);
        $turnier->setCanceled(true);
        $turnier->getLogService()->addLog("Turnier wurde abgesagt: $grund");
    }

    /**
     * @param Turnier $turnier
     * @return nTeam[]
     */
    public static function getTeams(Turnier $turnier): array
    {

        $liste = $turnier->getListe();

        foreach ($liste as $anmeldung) {
            $teams[] = $anmeldung->getTeam();
        }

        return $teams ?? [];

    }

    /**
     * @param Turnier $turnier
     * @return Kontakt[]
     */
    public static function getEmails(Turnier $turnier): array
    {
        $emails = [];
        $teams = self::getTeams($turnier);
        foreach ($teams as $team) {
            $emails += $team->getEmails()->toArray();
        }
        return $emails;
    }

    public static function getAnzahlGesetzteFreilose(Turnier $turnier): int
    {
        return $turnier->getGesetzteFreilose()->count();
    }

    public static function isMaximaleAnzahlFreiloseAufSetzliste(Turnier $turnier): bool
    {
        $setzliste = self::getSetzListe($turnier);
        $anzahlFreilose = 0;
        foreach ($setzliste as $anmeldung) {
            if ($anmeldung->hasFreilosGesetzt()) {
                $anzahlFreilose++;
            }
        }
        return ($anzahlFreilose >= 2);
    }

    public static function getAnzahlGesetzteTeams(Turnier $turnier): int
    {
        return self::getSetzListe($turnier)->count();
    }

    public static function getAnzahlWartelisteTeams(Turnier $turnier): int
    {
        return \count(self::getWarteliste($turnier));
    }

    public static function isSofortOeffnen(Turnier $turnier): bool
    {
        return $turnier->isSofortOeffnenFrei() || $turnier->isSofortOeffnenHoch() || $turnier->isSofortOeffnenRunter();
    }

    public static function isErweitertBlock(Turnier $turnier): bool
    {
        return $turnier->isBlockErweitertHoch() || $turnier->isBlockErweitertRunter() || $turnier->isBlockErweitertFrei();
    }

    /**
     * @param $turnier Turnier
     *
     * Nimmt die notwendigen Änderungen am Entity für eine Erweiterung des Turniers einen Block nach oben vor.
     * Wichtig: Das Enitry wird nicht persistiert.
     * Wichtig: Es findet keine Überprüfung statt, ob das Turnier überhaupt geöffnet werden kann.
     */
    public static function erweitereBlockHoch(Turnier $turnier): void
    {
        $hoehererBlock = BlockService::hoehererTurnierBlock($turnier);
        $turnier->setBlockErweitertHoch(true);
        $turnier->setBlock($hoehererBlock);
    }

    /**
     * @param $turnier Turnier
     *
     * Nimmt die notwendigen Änderungen am Entity für eine Erweiterung des Turniers einen Block nach unten vor.
     * Wichtig: Das Enitry wird nicht persistiert.
     * Wichtig: Es findet keine Überprüfung statt, ob das Turnier überhaupt geöffnet werden kann.
     */
    public static function erweitereBlockRunter(Turnier $turnier): void
    {
        $niedrigererBlock = BlockService::niedrigererTurnierBlock($turnier);
        $turnier->setBlockErweitertRunter(true);
        $turnier->setBlock($niedrigererBlock);
    }

    /**
     * @param $turnier Turnier
     *
     * Nimmt die notwendigen Änderungen am Entity für eine Erweiterung des Turniers um alle Blöcke vor.
     * Wichtig: Das Enitry wird nicht persistiert.
     * Wichtig: Es findet keine Überprüfung statt, ob das Turnier überhaupt geöffnet werden kann.
     */
    public static function erweitereBlockFrei(Turnier $turnier): void
    {
        $blockfrei = Config::BLOCK_ALL[0];
        $turnier->setBlockErweitertFrei(true);
        $turnier->setBlock($blockfrei);
    }

    public static function isBlockfrei(Turnier $turnier): bool
    {
        return $turnier->getBlock() === Config::BLOCK_ALL[0];
    }

    public static function getFreieSetzPlaetze(Turnier $turnier)
    {
        $plaetze = $turnier->getDetails()->getPlaetze();
        $aufSetzListe = self::getAnzahlGesetzteTeams($turnier);
        return max(0, $plaetze - $aufSetzListe);
    }


    /**
     * Füllt freie Plätze auf der Spielen-Liste von der Warteliste aus wieder auf,
     * wenn der Teamblock des Wartelisteneintrags zum Turnier passt,
     * wenn das Turnier nicht in der offenen Phase ist,
     * wenn das Turnier noch freie Plätze hat.
     *
     * @param Turnier $turnier
     * @param bool $send_mail
     */
    public static function setzListeAuffuellen(Turnier $turnier, bool $send_mail = true): void
    {
        $freie_plaetze = self::getFreieSetzPlaetze($turnier);
        if ($turnier->isSetzPhase() && $freie_plaetze > 0) {

            $liste = self::getWarteListe($turnier);

            foreach ($liste as $anmeldung) {
                if ($freie_plaetze > 0) {
                    $team = $anmeldung->getTeam();
                    if (self::isSetzBerechtigt($turnier, $team)) {
                        # Immer vom Parent aus verändern, sonst kann es hier zu Problemem kommen.
                        $turnier->getListe()->get($anmeldung->getTeam()->id())->setListe('setzliste');
                        $freie_plaetze--;
                        $turnier->getLogService()->addLog("Von Warteliste auf Setzliste: " . $team->getName());
                        if ($send_mail) {
                            TurnierEventMailBot::mailWarteZuSetzliste($turnier, $team);
                        }
                    }
                }
            }
        }
    }

    /**
     * Liefert die Teams der Setzliste (nach Wertigkeit bzw. bei A-Block-Finalturnieren nach Meisterschaftstabelle
     * sortiert) inklusive Wertigkeit, Teamblock und Trikotfarben — für die Spielplanerstellung.
     *
     * @param Turnier $turnier
     * @return array
     */
    public static function getSpielenliste(Turnier $turnier): array
    {
        $spielenliste = [];
        foreach (self::getSetzListe($turnier) as $anmeldung) {
            $team = $anmeldung->getTeam();
            $teamId = $team->id();

            $spielenliste[$teamId] = [
                'team_id' => $teamId,
                'teamname' => $team->getName($turnier->getSaison()),
                'wertigkeit' => TabelleService::getTeamWertigkeit($teamId, $turnier->getSpieltag() - 1, $turnier->getSaison()),
                'tblock' => TabelleService::getTeamBlock($teamId, $turnier->getSpieltag() - 1),
                'freilos_gesetzt' => $anmeldung->getFreilosGesetzt(),
                'details' => [
                    'ligateam' => $team->getLigateam(),
                    'ligavertreter' => $team->getDetails()?->getLigavertreter(),
                    'trikot_farbe_1' => $team->getDetails()?->getTrikotFarbe1(),
                    'trikot_farbe_2' => $team->getDetails()?->getTrikotFarbe2(),
                ],
            ];
        }

        // Rangtabelle sortieren
        uasort($spielenliste, static function ($teamA, $teamB) {
            return ((int) $teamB['wertigkeit'] <=> (int) $teamA['wertigkeit']);
        });

        return $spielenliste;
    }

    /**
     * Trägt ein Turnierergebnis ein. Setzt das Turnier in die Ergebnisphase.
     * Persistiert nicht selbst, der Aufrufer muss speichern.
     *
     * @param Turnier $turnier
     * @param array $platzierungstabelle team_id => ['ligapunkte' => int, 'platz' => int]
     */
    public static function setErgebnisse(Turnier $turnier, array $platzierungstabelle): void
    {
        self::deleteErgebnisse($turnier);

        foreach ($platzierungstabelle as $teamId => $eintrag) {
            $ergebnis = $turnier->isLigaturnier() ? $eintrag['ligapunkte'] : null;

            $turnierErgebnis = (new TurnierErgebnis())
                ->setTeam(TeamRepository::get()->team((int) $teamId))
                ->setTurnier($turnier)
                ->setErgebnis($ergebnis)
                ->setPlatz($eintrag['platz'])
                ->setSaisonUebernahmeVerhindern(false);

            $turnier->getErgebnis()->add($turnierErgebnis);
        }

        $turnier->setPhase('ergebnis');
        $turnier->getLogService()->addLog("Turnierergebnis wurde in die Datenbank eingetragen");
    }

    /**
     * Löscht die eingetragenen Turnierergebnisse. Persistiert nicht selbst, der Aufrufer muss speichern.
     *
     * @param Turnier $turnier
     */
    public static function deleteErgebnisse(Turnier $turnier): void
    {
        if ($turnier->getErgebnis()->isEmpty()) {
            return;
        }
        $turnier->getErgebnis()->clear();
        $turnier->getLogService()->addLog("Turnierergebnisse wurden gelöscht.");
    }

    /**
     * Liefert die Turnierergebnisse, keyed by Platz.
     *
     * @param Turnier $turnier
     * @return TurnierErgebnis[]
     */
    public static function getErgebnisByPlatz(Turnier $turnier): array
    {
        $criteria = Criteria::create()->orderBy(['platz' => Criteria::ASC]);

        $ergebnisse = [];
        foreach ($turnier->getErgebnis()->matching($criteria) as $ergebnis) {
            $ergebnisse[$ergebnis->getPlatz()] = $ergebnis;
        }
        return $ergebnisse;
    }

    /**
     * Hinterlegt zu einem Turnier einen Link zu einem manuell hochgeladenen Spielplan bzw. Ergebnis.
     * Persistiert nicht selbst, der Aufrufer muss speichern.
     *
     * @param Turnier $turnier
     * @param string $link
     * @param string $phase
     */
    public static function uploadSpielplan(Turnier $turnier, string $link, string $phase): void
    {
        $turnier->setSpielplanDatei($link);
        $turnier->setPhase($phase);
        $turnier->getLogService()->addLog("Manuelle Spielplan- oder Ergebnisdatei wurde hochgeladen.");
    }

}
