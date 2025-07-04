<?php

namespace App\Service\Turnier;

use App\Entity\Team\Kontakt;
use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Event\Turnier\TurnierEventMailBot;
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
        $tag = (int)$datum_warte_zu_setzphase->format('N'); // Numerische Zahl des Wochentages 1-7

        # Findet das Turnier am Mittwoch oder später statt, wird es dem nächsten Wochenende zugeordnet
        # Mi == 3 -> hochrechnen auf nächsten Samstag also +(6-3) Tage
        if ($tag >= 3) {
            $delta = (string)(6 - $tag);
            return (int)$datum_warte_zu_setzphase->modify("+$delta days")->format("U");
        }
        # Findet das Turnier am Montag oder Dienstag statt, wird es dem vorherigen Wochenende zugeordnet
        # Di == 2 -> herunterrechnen auf letzten Samstag also -(2+1) Tage
        $delta = (string)(1 + $tag);
        return (int)$datum_warte_zu_setzphase->modify("-$delta days")->format("U");
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
        $emails = [];
        $teams = self::getTeams($turnier);
        foreach ($teams as $team) {
            $emails += $team->getEmails()->toArray();
        }
        return $emails;
    }

    public static function getAnzahlGesetzteTeams(Turnier $turnier): int
    {
        return self::getSetzListe($turnier)->count();
    }

    public static function getAnzahlWartelisteTeams(Turnier $turnier): int
    {
        return count(self::getWarteliste($turnier));
    }

    public static function erweitereBlockHoch(Turnier $turnier): void
    {
        $hoehererBlock = BlockService::hoehererTurnierBlock($turnier);
        $turnier
            ->setBlockErweitertHoch(true)
            ->setBlock($hoehererBlock);
    }

    public static function erweitereBlockRunter(Turnier $turnier): void
    {
        $turnier->setBlockErweitertRunter(true);
        $turnier->setBlock(BlockService::niedrigererTurnierBlock($turnier));
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

}