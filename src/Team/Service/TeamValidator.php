<?php

namespace App\Team\Service;

use App\Team\Entity\Freilosgrund;
use App\Team\Entity\Team;
use App\Turnier\Entity\Turnier;
use App\Turnier\Entity\Turnieranmeldung;
use App\Shared\DoctrineWrapper;
use App\Turnier\Service\TurnierService;
use Env;
use Html;

class TeamValidator
{
    public static function hasGenugSpieler(Team $team): bool
    {
        return TeamService::getAnzahlAktiveSpieler($team) >= 5;
    }

    public static function hasSchiriFreilosErhalten(Team $team): bool
    {
        $freilose = $team->getFreiloseBySaison();
        foreach ($freilose as $freilos) {
            if ($freilos->getGrund() == Freilosgrund::SCHIRI) {
                return true;
            }
        }
        return false;
    }

    /**
     * Ermittelt, ob das Team an gleichen Kalendertag auf einem anderen Turnier angemeldet ist
     *
     * @param Turnier $turnier
     * @param Team $team
     * @return bool
     */
    public static function isAmKalenderTagAufSetzliste(Turnier $turnier, Team $team): bool //TODO ins repo
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('l.listeId')
            ->from(Turnieranmeldung::class, 'l')
            ->innerJoin('l.turnier', 't')
            ->where('l.team = :team')
            ->andWhere('t.datum = :datum')
            ->andWhere("t.art = 'I' OR t.art = 'II'")
            ->andWhere("t.canceled = 0")
            ->andWhere("l.liste = 'setzliste'");

        // Beim Erstellen ist TurnierId null bis es in die DB geschrieben wird.
        if ($turnier->id() != null) {
            $query->andWhere("t.id != :turnierId")
                ->setParameter('turnierId', $turnier->id());
        }

        $query
            ->setParameter('team', $team)
            ->setParameter('datum', $turnier->getDatum());

        return \count($query->getQuery()->getResult()) > 0;
    }

    public static function isValidTurnierForAnmeldung(Team $team, Turnier $turnier, $showError = true): bool
    {
        $valid = true;
        $name = $team->getName();
        if (TeamService::isAufSetzliste($team, $turnier)) {
            $error[] = "Das Team $name ist bereits auf der Setzliste.";
            $valid = false;
        } elseif (self::isAmKalenderTagAufSetzliste($turnier, $team)) {
            $error[] = "Das Team $name ist bereits auf einem anderen Turnier auf der Setzliste.";
            $valid = false;
        }

        if ($turnier->isSpielplanPhase() || $turnier->isErgebnisPhase()) {
            $error[] = "Eine Anmeldung ist nicht mehr möglich, da der Spielplan schon erstellt wurde.";
            $valid = false;
        }

        if (!$turnier->isLigaturnier()) {
            $error[] = "Anmeldungen zu Turnieren dieser Art sind im Teamcenter nicht möglich.";
            $valid = false;
        }

        if ($showError && isset($error)) {
            Html::error(implode("<br>", $error));
        }

        return $valid;
    }

    public static function isValidRegularAnmeldung(Team $team, Turnier $turnier, $showError = true): bool
    {
        $valid = self::isValidTurnierForAnmeldung($team, $turnier, $showError);

        if (TeamService::isAufWarteliste($team, $turnier)) {
            $error[] = "Das Team " . $team->getName() . " ist bereits auf der Warteliste angemeldet.";
            $valid = false;
        }
        if ($showError && isset($error)) {
            Html::error(implode("<br>", $error));
        }
        return $valid;
    }

    public static function isValidAbmeldung(Team $team, Turnier $turnier): bool
    {
        $valid = true;

        if (
            TeamService::isAufSetzliste($team, $turnier)
            && TurnierService::getAbmeldeFristUnix($turnier) < time()
        ) {
            $valid = false;
            Html::error(
                "Abmeldungen von der Spielen-Liste sind nur bis Freitag 23:59 zwei Wochen vor dem 
                Turnier möglich. Bitte nehmt via Email Kontakt mit dem Ligaausschuss auf: "
                . Html::mailto(Env::LAMAIL) . "",
                esc: false,
            );
        }

        if (!TeamService::isAngemeldet($team, $turnier)) {
            $valid = false;
            Html::error("Abmeldung nicht möglich, da ihr nicht zum Turnier angemeldet seid.");
        }

        return $valid;
    }

    public static function isValidFreilos(Team $team, Turnier $turnier, $showError = true): bool
    {
        $valid = self::isValidTurnierForAnmeldung($team, $turnier, $showError);

        if (!TurnierService::hasFreieSetzPlaetze($turnier)) {
            $error[] = "Es gibt keine freien Plätze mehr auf der Setzliste.";
            $valid = false;
        } elseif ($turnier->isSetzPhase() && TurnierService::isSetzBerechtigt($turnier, $team)) {
            $error[] = "Dein Team würde auch ohne Freilos auf die Setzliste kommen.";
            $valid = false;
        }

        if (TurnierService::isMaximaleAnzahlFreiloseAufSetzliste($turnier)) {
            $error[] = "Es kann kein Freilos mehr für dieses Turnier gesetzt werden.";
            $valid = false;
        }

        if ($team->getAnzahlOffenerFreilose() == 0) {
            $error[] = "Dein Team hat keine Freilose zur Verfügung.";
            $valid = false;
        }

        if (!TurnierService::isSpielBerechtigtFreilos($turnier, $team)) {
            $error[] = "Turnierblock stimmt nicht. Freilose können nur für Turniere mit höheren oder passenden Block gesetzt werden.";
            $valid = false;
        }

        if ($showError && isset($error)) {
            Html::error(implode("<br>", $error), esc: false);
        }

        return $valid;
    }

    public static function isValidFinalMeldung(Team $teamEntity, Turnier $turnier): bool
    {
        if (!$turnier->isFinalTurnier()) {
            Html::error("Anmeldung fehlgeschlagen.");
            return false;
        }
        if (TeamService::isAngemeldet($teamEntity, $turnier)) {
            Html::error("Dein Team ist schon angemeldet.");
            return false;
        }
        return true;
    }

}
