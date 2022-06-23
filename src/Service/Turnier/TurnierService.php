<?php

namespace App\Service\Turnier;

use App\Entity\Team\Kontakt;
use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Event\Turnier\TurnierEventMailBot;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Team\TeamService;
use Config;
use Doctrine\Common\Collections\Collection;
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

    public static function getSetzListe(Turnier $turnier): Collection
    {
        $filter = static function(TurniereListe $anmeldung) {
            return $anmeldung->getListe() === 'setzliste';
        };

        return $turnier->getListe()->filter($filter);
    }

    public static function getWarteliste(Turnier $turnier): Collection
    {
        $filter = static function(TurniereListe $anmeldung) {
            return $anmeldung->getListe() === 'warteliste';
        };

        return $turnier->getListe()->filter($filter);
    }

    public static function getTurnierEintrageFristUnix(Turnier $turnier): int
    {
        return self::getLosDatumUnix($turnier);
    }

    public static function getAbmeldeFristUnix(Turnier $turnier): int
    {
        return self::getLosDatumUnix($turnier) + 2 * 7 * 24 * 60 * 60;
    }

    public static function getAbmeldeFrist(Turnier $turnier): string
    {
        $unixTime = self::getAbmeldeFristUnix($turnier) - 1;
        Date::setLocale('de');
        return Date::createFromTimestamp($unixTime)->format("l, d.m.Y - H:i") . " Uhr";
    }

    public static function getLosDatumUnix(Turnier $turnier): int
    {
        $unix = $turnier->getDatum()->getTimestamp();
        $tag = date("N", $unix); // Numerische Zahl des Wochentages 1-7
        // Faktor 3.93 und strtotime(date("d-M-Y"..)) -> Reset von 12 Uhr Mittags auf Null Uhr, um Winter <-> Sommerzeit korrekt handzuhaben
        if ($tag >= 3) {
            return strtotime(date("d-M-Y", $unix - 3.93 * 7 * 24 * 60 * 60 + (6 - $tag) * 24 * 60 * 60));
        }
        return strtotime(date("d-M-Y", $unix - 3.93 * 7 * 24 * 60 * 60 - $tag * 24 * 60 * 60));
    }

    public static function getLosDatum(Turnier $turnier): string
    {
        $unixTime = self::getLosDatumUnix($turnier) - 1;
        Date::setLocale('de');
        return Date::createFromTimestamp($unixTime)->format("l, d.m.Y - H:i") . " Uhr";
    }

    public static function isSpielBerechtigt(Turnier $turnier, nTeam $team): bool
    {
        // NL Team geht immer
        if (!$team->isLigaTeam()) {
            return true;
        }

        $datum = $turnier->getDatum();

        return BlockService::isBlockPassend($turnier, $team)
            && !TeamService::isAmKalenderTagAufSetzliste($datum, $team);
    }

    public static function aufSetzListe(Turnier $turnier, nTeam $team): void
    {
        $anmeldung = new TurniereListe();
        $anmeldung->setTeam($team)
            ->setListe('setzliste')
            ->setTurnier($turnier)
            ->setFreilosGesetzt('Nein');
         $turnier->getListe()->add($anmeldung);
         $turnier->getLogService()->addLog("Auf Setzliste: " . $team->getName() . " (" . $team->getBlock() . ")");
    }

    public static function aufWarteListe(Turnier $turnier, nTeam $team): void
    {
        $positionWarteliste = self::getAnzahlWartelisteTeams($turnier) + 1;
        $anmeldung = new TurniereListe();
        $anmeldung->setTeam($team)
            ->setListe('warteliste')
            ->setTurnier($turnier)
            ->setFreilosGesetzt('Nein')
            ->setPositionWarteliste($positionWarteliste);
        $turnier->getListe()->add($anmeldung);
        $turnier->getLogService()->addLog(
            "Auf Warteliste:" . " $positionWarteliste ". $team->getName() . " (" . $team->getBlock() . ")"
        );
    }

    public static function teamAbmelden(Turnier $turnier, nTeam $team): void
    {
        $anmeldung = TurnierRepository::get()->liste->findOneBy(['team' => $team]);
        $turnier->getListe()->removeElement($anmeldung);
        $liste = TurnierSnippets::translate($anmeldung->getListe());
        $name = $team->getName();
        $turnier->getLogService()->addLog("Abmeldung: $name von der $liste");
    }

    public static function neueWartelistePositionen(Turnier $turnier): void
    {
        $warteliste = self::getWarteListe($turnier);
        $i = 1;
        foreach ($warteliste as $anmeldung) {
            $anmeldung->setPositionWarteliste($i++);
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
        return self::getWarteliste($turnier)->count();
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
        $freie_plaetze = self::getFreieSetzPlaetze($turnier);

        if ($turnier->isSetzPhase() && self::getFreieSetzPlaetze($turnier) > 0) {

            $liste = self::getWarteListe($turnier);

            foreach ($liste as $anmeldung) {
                if ($freie_plaetze > 0) {
                    $team = $anmeldung->getTeam();
                    if (self::isSpielBerechtigt($turnier, $team)) {
                            self::aufSetzListe($turnier, $team);
                            if ($send_mail) {
                                TurnierEventMailBot::mailWarteZuSetzliste($turnier, $team); // TODO
                            }
                            --$freie_plaetze;
                        }
                    }
                }
            }

        self::neueWartelistePositionen($turnier);

    }

}