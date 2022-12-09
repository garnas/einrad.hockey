<?php

namespace App\Service\Turnier;

use App\Entity\Team\Kontakt;
use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Event\Turnier\TurnierEventMailBot;
use Config;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Jenssegers\Date\Date;
use DB;
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

    public static function isLosen(Turnier $turnier): bool
    {
        if (!$turnier->isWartePhase()){
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
            ->andWhere((Criteria::expr())->eq('liste', 'warteliste'));
        $sort = static function(TurniereListe $eintrag, TurniereListe $vergleich) {
            return $eintrag->getPositionWarteliste() <=> $vergleich->getPositionWarteliste();
        };
        $liste = $turnier->getListe()->matching($criteria);
        $listeAsArray = $liste->toArray();
        uasort($listeAsArray, $sort);
        return new ArrayCollection($listeAsArray);
    }

    public static function getTurnierEintrageFristUnix(Turnier $turnier): int
    {
        return self::warteToSetzUnix($turnier);
    }

    public static function getAbmeldeFristUnix(Turnier $turnier): int
    {
        return self::warteToSetzUnix($turnier) + 2 * 7 * 24 * 60 * 60;
    }

    public static function getAbmeldeFrist(Turnier $turnier): string
    {
        $unixTime = self::getAbmeldeFristUnix($turnier) - 1;
        Date::setLocale('de');
        return Date::createFromTimestamp($unixTime)->format("l, d.m.Y - H:i") . " Uhr";
    }

    public static function warteToSetzUnix(Turnier $turnier): int
    {
        $unix = $turnier->getDatum()->getTimestamp();
        $tag = $turnier->getDatum()->format('N'); // Numerische Zahl des Wochentages 1-7
        // Faktor 3.93 und strtotime(date("d-M-Y"..)) -> Reset von 12 Uhr Mittags auf Null Uhr, um Winter <-> Sommerzeit korrekt handzuhaben
        if ($tag >= 3) {
            return strtotime(date("d-M-Y", $unix - 3.93 * 7 * 24 * 60 * 60 + (6 - $tag) * 24 * 60 * 60));
        }
        return strtotime(date("d-M-Y", $unix - 3.93 * 7 * 24 * 60 * 60 - $tag * 24 * 60 * 60));
    }

    public static function getLosDatum(Turnier $turnier): string
    {
        $unixTime = self::warteToSetzUnix($turnier) - 1;
        Date::setLocale('de');
        return Date::createFromTimestamp($unixTime)->format("l, d.m.Y - H:i") . " Uhr";
    }

    public static function isSetzBerechtigt(Turnier $turnier, nTeam $team): bool
    {
        //TODO check am Kalendertag schon auf Setzliste
        // NL Team geht immer
        if (!$team->isLigaTeam()) {
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
            if($nlTeam->isLigaTeam()) {
                trigger_error("Ligateam soll als NL-Team angemeldet werden", E_USER_ERROR);
            }
            if ($liste === "warteliste") {
                self::addToWarteListe($turnier, $nlTeam);
            } elseif ($liste === "setzliste") {
                self::addToSetzListe($turnier, $nlTeam);
            } else {
                trigger_error("Falsche Liste", E_USER_ERROR);
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
            .  ($positionWarteliste ? $positionWarteliste . ". " : "")
            . $team->getName()
            . " " . BlockService::toString($team)
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
        $teams = self::getTeams($turnier);
        foreach ($teams as $team) {
            $emails[] = $team->getEmails();
        }
        return $emails ?? [];
    }

    public static function getAnzahlGesetzteTeams(Turnier $turnier): int
    {
        return self::getSetzListe($turnier)->count();
    }

    public static function getAnzahlWartelisteTeams(Turnier $turnier): int
    {
        return count(self::getWarteliste($turnier));
    }

    public static function blockhochErweitern(Turnier $turnier): void
    {
        $turnier->setBlock(BlockService::nextTurnierBlock($turnier));
    }

    public static function blockOeffnen(Turnier $turnier): void
    {
        $turnier->setBlock(Config::BLOCK_ALL[0]);
    }

    public static function getFreieSetzPlaetze(Turnier $turnier)
    {
        $plaetze = $turnier->getDetails()->getPlaetze();
        $aufSetzListe = self::getAnzahlGesetzteTeams($turnier);
        return max(0,  $plaetze - $aufSetzListe);
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
        if ($turnier->isSetzPhase() && self::getFreieSetzPlaetze($turnier) > 0) {

            $liste = self::getWarteListe($turnier);

            foreach ($liste as $anmeldung) {
                if (self::getFreieSetzPlaetze($turnier) > 0) {
                    $team = $anmeldung->getTeam();
                    if (self::isSetzBerechtigt($turnier, $team)) {
                        $anmeldung->setListe('setzliste');
                        $turnier->getLogService()->addLog("Von Warteliste auf Setzliste: " . $team->getName());
                        if ($send_mail) {
                            TurnierEventMailBot::mailWarteZuSetzliste($turnier, $team);
                        }
                    }
                }
            }
        }
    }

}