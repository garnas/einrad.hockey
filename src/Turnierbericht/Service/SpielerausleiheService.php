<?php

namespace App\Turnierbericht\Service;

use App\Spieler\Entity\Spieler;
use App\Spieler\Repository\SpielerRepository;
use App\Tabelle\Service\TabelleService;
use App\Team\Entity\Team;
use App\Team\Service\TeamService;
use App\Turnier\Entity\Turnier;

class SpielerausleiheService
{
    /**
     * Das abgebende Team muss schwächer sein und zwischen beiden Teams müssen mindestens vier andere Teams liegen.
     */
    public const MIN_RANG_ABSTAND = 5;

    /**
     * Rang eines Teams in der Rangtabelle vor dem Spieltag des Turniers. Nichtligateams haben den Rang null.
     */
    public static function getRang(Team $team, Turnier $turnier): ?int
    {
        if (!$team->isLigaTeam()) {
            return null;
        }
        $spieltag = $turnier->getSpieltag() ? $turnier->getSpieltag() - 1 : null;
        return TabelleService::getTeamRang($team->id(), $spieltag, $turnier->getSaison());
    }

    public static function isRangAbstandErlaubt(Team $teamAuf, Team $teamAb, Turnier $turnier): bool
    {
        $rangAuf = self::getRang($teamAuf, $turnier);
        $rangAb = self::getRang($teamAb, $turnier);
        if ($rangAuf === null || $rangAb === null) {
            return false;
        }
        // Nur ein schwächeres Team (höherer Rang) darf an ein stärkeres Team verleihen
        return $rangAb - $rangAuf >= self::MIN_RANG_ABSTAND;
    }

    /**
     * Alle in der Liga gemeldeten Spieler:innen, gruppiert nach Team-ID.
     *
     * @return array<int, array{team: Team, spieler: Spieler[]}>
     */
    public static function getLeihbareSpielerNachTeam(Turnier $turnier): array
    {
        $gruppen = [];
        foreach (SpielerRepository::get()->findGemeldeteLigaSpieler($turnier->getSaison()) as $spieler) {
            $team = $spieler->getTeam();
            $gruppen[$team->id()]['team'] ??= $team;
            $gruppen[$team->id()]['spieler'][] = $spieler;
        }
        return $gruppen;
    }

    /**
     * Prüft eine Ausleihe einer gemeldeten Spieler:in.
     *
     * @return string|null Fehlermeldung oder null, wenn die Ausleihe erlaubt ist
     */
    public static function pruefeAusleihe(Turnier $turnier, Team $teamAuf, Spieler $spieler): ?string
    {
        if (!TeamService::isAufSetzliste($teamAuf, $turnier)) {
            return "Das aufnehmende Team nimmt nicht am Turnier teil.";
        }

        $teamAb = $spieler->getTeam();
        if (
            $teamAb === null
            || !$teamAb->isLigaTeam()
            || !$teamAb->isAktiv()
            || $spieler->getLetzteSaison() !== $turnier->getSaison()
        ) {
            return "Die Spieler:in ist nicht in der Liga gemeldet.";
        }

        if ($teamAb->id() === $teamAuf->id()) {
            return "Die Spieler:in ist bereits im Kader des aufnehmenden Teams.";
        }

        // Nichtligateams dürfen jede:n Spieler:in leihen
        if (!$teamAuf->isLigaTeam()) {
            return null;
        }

        if (!self::isRangAbstandErlaubt($teamAuf, $teamAb, $turnier)) {
            return "Das abgebende Team muss in der Rangtabelle mindestens fünf Plätze hinter dem aufnehmenden Team liegen.";
        }

        return null;
    }

    /**
     * Prüft eine Ausleihe einer nicht in der Liga gemeldeten Person (nur für Nichtligateams).
     *
     * @return string|null Fehlermeldung oder null, wenn die Ausleihe erlaubt ist
     */
    public static function pruefeFreieAusleihe(Turnier $turnier, Team $teamAuf, string $name): ?string
    {
        if (!TeamService::isAufSetzliste($teamAuf, $turnier)) {
            return "Das aufnehmende Team nimmt nicht am Turnier teil.";
        }
        if ($teamAuf->isLigaTeam()) {
            return "Nicht gemeldete Spieler:innen können nur von Nichtligateams geliehen werden.";
        }
        if (trim($name) === '') {
            return "Bitte gib den Namen der Spieler:in an.";
        }
        return null;
    }
}
