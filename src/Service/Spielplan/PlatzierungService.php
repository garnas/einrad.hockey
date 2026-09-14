<?php

namespace App\Service\Spielplan;

use App\Entity\Turnier\Spiel;
use App\Enum\SpielplanVergleich;
use App\Model\Spielplan\Penaltyuebersicht;
use App\Model\Spielplan\Platzierung;
use App\Model\Spielplan\Platzierungsergebnis;
use App\Model\Spielplan\Turniertabellenzeile;

/**
 * Ermittelt die Abschlussplatzierung eines JgJ-Turniers inkl. rekursivem direktem Vergleich
 * und Penaltybegegnungen.
 *
 * 1:1-Portierung des Algorithmus aus Spielplan_JgJ (set_platzierungen / direkter_vergleich /
 * penalty_vergleich / get_gleichplatzierte_teams / check_ergebnis_fix).
 */
final class PlatzierungService
{
    /** @var array<int, Spiel> keyed by Spiel-ID */
    private array $spiele;

    /** @var array<int, array{team_id: int, teamname: string, wertigkeit: ?int, ...}> keyed by Team-ID */
    private array $teamliste;

    private int $anzahlTeams;

    /** Anzahl Spiele, die ein Team im JgJ absolvieren muss */
    private int $anzahlSpiele;

    /** @var array<int, Turniertabellenzeile> vollständige, sortierte Turniertabelle */
    private array $turniertabelle;

    /** @var array<int, Platzierung> */
    private array $platzierungstabelle = [];

    /** @var array<int, array<int, Turniertabellenzeile>> */
    private array $direkterVergleichTabellen = [];

    /** @var array<int, array<int, Turniertabellenzeile>> */
    private array $penaltyTabellen = [];

    private Penaltyuebersicht $penaltys;

    private bool $outOfScope = false;

    /**
     * @param array<int, Spiel> $spiele keyed by Spiel-ID
     * @param array<int, array> $teamliste keyed by Team-ID ({@see \App\Service\Turnier\TurnierService::getSpielenliste()})
     */
    public static function berechne(array $spiele, array $teamliste): Platzierungsergebnis
    {
        return (new self($spiele, $teamliste))->ergebnis();
    }

    /**
     * @param array<int, Spiel> $spiele
     * @param array<int, array> $teamliste
     */
    private function __construct(array $spiele, array $teamliste)
    {
        $this->spiele = $spiele;
        $this->teamliste = $teamliste;
        $this->anzahlTeams = count($teamliste);
        $this->anzahlSpiele = $this->anzahlTeams - 1;
        $this->penaltys = new Penaltyuebersicht();

        $torematrix = TurniertabellenService::getTorematrix($spiele, mitPenaltys: true);
        $this->turniertabelle = TurniertabellenService::getSortierteTurniertabelle($torematrix);

        $this->setPlatzierungen($torematrix);

        if (!empty($this->penaltys->kontrolle) && $this->istTurnierBeendet()) {
            $this->outOfScope = true;
        }
    }

    private function ergebnis(): Platzierungsergebnis
    {
        return new Platzierungsergebnis(
            platzierungstabelle: $this->platzierungstabelle,
            direkterVergleichTabellen: $this->direkterVergleichTabellen,
            penaltyTabellen: $this->penaltyTabellen,
            penaltys: $this->penaltys,
            outOfScope: $this->outOfScope,
            turniertabelle: $this->turniertabelle,
        );
    }

    /**
     * Sortiert die Turniertabelle und wendet ggf. den direkten Vergleich an.
     *
     * @param array<int, array<int, list<array>>> $torematrix
     */
    private function setPlatzierungen(array $torematrix): void
    {
        $tabelle = TurniertabellenService::getSortierteTurniertabelle($torematrix);
        $erstesTeam = array_key_first($tabelle);
        $gleiche = $this->gleichplatzierteTeams($tabelle, $erstesTeam, SpielplanVergleich::PUNKTE);

        // Fall 1: erstes Team eindeutig platzierbar
        if (count($gleiche) === 1) {
            $this->setzePlatzierung($erstesTeam);
            self::removeTeamIds($torematrix, [$erstesTeam]);
            if (count($torematrix) !== 0) {
                $this->setPlatzierungen($torematrix);
            }
        } else {
            // Direkter Vergleich der punktgleichen Teams
            $this->direkterVergleich(self::filterTeamIds($torematrix, $gleiche), true);
            self::removeTeamIds($torematrix, $gleiche);
            if (count($torematrix) !== 0) {
                $this->setPlatzierungen($torematrix);
            }
        }

        // Zuletzt die noch zu spielenden Penaltys ermitteln
        if (count($torematrix) === 0) {
            foreach ($this->penaltys->gesamt as $spielId) {
                if (
                    $this->spiele[$spielId]->getPenaltyA() === null
                    || $this->spiele[$spielId]->getPenaltyB() === null
                ) {
                    $this->penaltys->ausstehend[] = $spielId;
                }
            }
        }
    }

    /**
     * @param array<int, array<int, list<array>>> $torematrix
     */
    private function direkterVergleich(array $torematrix, bool $print = false): void
    {
        // Fall 0: nur ein Team verblieben
        if (count($torematrix) === 1) {
            $this->setzePlatzierung(array_key_first($torematrix));
            return;
        }

        $tabelle = TurniertabellenService::getSortierteTurniertabelle($torematrix);
        if ($print && $this->ergebnisFix(array_keys($torematrix))) {
            $this->direkterVergleichTabellen[] = $tabelle;
        }

        $erstesTeam = array_key_first($tabelle);
        $gleiche = $this->gleichplatzierteTeams($tabelle, $erstesTeam, SpielplanVergleich::PUNKTE);

        // Fall 1: erstes Team eindeutig platzierbar
        if (count($gleiche) === 1) {
            $this->setzePlatzierung($erstesTeam);
            self::removeTeamIds($torematrix, [$erstesTeam]);
            if (count($torematrix) !== 0) {
                $this->direkterVergleich($torematrix);
            }
            return;
        }

        // Fall 2: neuer direkter Vergleich mit Untertabelle nötig
        if (count($gleiche) < count($tabelle)) {
            $this->direkterVergleich(self::filterTeamIds($torematrix, $gleiche), true);
            self::removeTeamIds($torematrix, $gleiche);
            if (count($torematrix) !== 0) {
                $this->direkterVergleich($torematrix);
            }
            return;
        }

        // Fall 3: alle Teams punktgleich -> Tordifferenz, sonst Penalty
        $inPenalty = [];
        foreach (array_keys($tabelle) as $teamId) {
            $gleicheDiff = $this->gleichplatzierteTeams($tabelle, $teamId, SpielplanVergleich::TORDIFFERENZ);
            if (count($gleicheDiff) === 1) {
                $this->setzePlatzierung($teamId);
                self::removeTeamIds($torematrix, [$teamId]);
            } elseif (!in_array($teamId, $inPenalty)) {
                $inPenalty += $gleicheDiff;
                if ($this->ergebnisFix($gleicheDiff)) {
                    $this->penaltys->gesamt = array_merge($this->penaltys->gesamt, $this->spielIds($gleicheDiff));
                }
                $this->penaltyVergleich($torematrix, true);
            }
        }
    }

    /**
     * Direkter Vergleich der Penaltybegegnungen.
     *
     * @param array<int, array<int, list<array>>> $torematrix
     */
    private function penaltyVergleich(array $torematrix, bool $print = false): void
    {
        // Fall 0: nur ein Team verblieben
        if (count($torematrix) === 1) {
            $this->setzePlatzierung(array_key_first($torematrix));
            return;
        }

        $tabelle = TurniertabellenService::getSortierteTurniertabelle($torematrix);
        if ($print && $this->ergebnisFix(array_keys($tabelle))) {
            $this->penaltyTabellen[] = $tabelle;
        }

        $erstesTeam = array_key_first($tabelle);
        $gleiche = $this->gleichplatzierteTeams($tabelle, $erstesTeam, SpielplanVergleich::PENALTY_PUNKTE);

        if (count($gleiche) === 1) {
            $this->setzePlatzierung($erstesTeam);
            self::removeTeamIds($torematrix, [$erstesTeam]);
            if (count($torematrix) !== 0) {
                $this->penaltyVergleich($torematrix);
            }
            return;
        }

        if (count($gleiche) < count($tabelle)) {
            $this->penaltyVergleich(self::filterTeamIds($torematrix, $gleiche), true);
            self::removeTeamIds($torematrix, $gleiche);
            if (count($torematrix) !== 0) {
                $this->penaltyVergleich($torematrix);
            }
            return;
        }

        // Fall 3: alle penaltypunktgleich -> Penalty-Tordifferenz, sonst zweite Runde (out of scope)
        $zweitesPenalty = [];
        foreach (array_keys($tabelle) as $teamId) {
            $gleicheDiff = $this->gleichplatzierteTeams($tabelle, $teamId, SpielplanVergleich::PENALTY_DIFFERENZ);
            if (count($gleicheDiff) === 1) {
                $this->setzePlatzierung($teamId);
                self::removeTeamIds($torematrix, [$teamId]);
            } elseif (!in_array($teamId, $zweitesPenalty)) {
                $zweitesPenalty += $gleicheDiff;
                $this->penaltys->kontrolle = $this->spielIds($gleicheDiff);
                // Ab hier out of scope: Teams ohne echte Platzierung eintragen
                foreach ($gleicheDiff as $teamIdOhnePlatzierung) {
                    $this->setzePlatzierung($teamIdOhnePlatzierung);
                }
            }
        }
    }

    /**
     * Team-IDs, die mit $teamId hinsichtlich der Vergleichsart gleichplatziert sind.
     * Gibt genau [$teamId] zurück, wenn das Team eindeutig platzierbar ist.
     *
     * @param array<int, Turniertabellenzeile> $tabelle
     * @return int[]
     */
    private function gleichplatzierteTeams(array $tabelle, int $teamId, SpielplanVergleich $art): array
    {
        $referenz = $tabelle[$teamId]->wert($art);

        $treffer = [];
        foreach ($tabelle as $tid => $zeile) {
            if ($zeile->wert($art) === $referenz) {
                $treffer[] = $tid;
            }
        }
        return $treffer;
    }

    /**
     * Platziert ein Team an der nächsten freien Position.
     */
    private function setzePlatzierung(int $teamId): void
    {
        $this->platzierungstabelle[$teamId] = new Platzierung(
            teamId: $teamId,
            platz: count($this->platzierungstabelle) + 1,
            teamname: $this->teamliste[$teamId]['teamname'] ?? (string) $teamId,
            statistik: $this->turniertabelle[$teamId] ?? new Turniertabellenzeile($teamId),
        );
    }

    /**
     * Entfernt Teams aus der obersten Ebene der Torematrix (Begegnungen bleiben erhalten).
     *
     * @param array<int, array<int, list<array>>> $torematrix
     * @param int[] $teamIds
     */
    private static function removeTeamIds(array &$torematrix, array $teamIds): void
    {
        foreach ($teamIds as $teamId) {
            unset($torematrix[$teamId]);
        }
    }

    /**
     * Untertabelle der Torematrix mit nur den übergebenen Teams und deren Begegnungen untereinander.
     *
     * @param array<int, array<int, list<array>>> $torematrix
     * @param int[] $teamIds
     * @return array<int, array<int, list<array>>>
     */
    private static function filterTeamIds(array $torematrix, array $teamIds): array
    {
        $behalten = static fn($teamId) => in_array($teamId, $teamIds);

        foreach ($torematrix as &$begegnungen) {
            $begegnungen = array_filter($begegnungen, $behalten, \ARRAY_FILTER_USE_KEY);
        }
        unset($begegnungen);

        return array_filter($torematrix, $behalten, \ARRAY_FILTER_USE_KEY);
    }

    /**
     * Ist die Penaltybegegnung unvermeidbar (steht das Ergebnis rechnerisch fest)?
     *
     * @param int[] $teamIds
     */
    private function ergebnisFix(array $teamIds): bool
    {
        $vergleich = function (int $teamId): array {
            $min = $this->turniertabelle[$teamId]->punkte ?? 0;
            $max = $min + ($this->anzahlSpiele - $this->turniertabelle[$teamId]->spiele) * 3;
            return ['min' => $min, 'max' => $max, 'nicht_erreichbar' => $max - 1];
        };

        foreach ($teamIds as $teamId) {
            if ($this->turniertabelle[$teamId]->spiele < $this->anzahlSpiele) {
                return false; // Team hat noch nicht alle Spiele absolviert -> vermeidbar
            }
            $punktePenaltyTeam = $this->turniertabelle[$teamId]->punkte ?? 0;

            foreach (array_keys($this->turniertabelle) as $vergleichTeamId) {
                if (in_array($vergleichTeamId, $teamIds, true)) {
                    continue;
                }
                $v = $vergleich($vergleichTeamId);
                if (
                    ($v['max'] !== $v['min'] && $v['max'] === $punktePenaltyTeam)
                    && $punktePenaltyTeam <= $v['max']
                    && $punktePenaltyTeam >= $v['min']
                    && $punktePenaltyTeam != $v['nicht_erreichbar']
                ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Spiel-IDs, in denen ausschließlich die übergebenen Teams gegeneinander spielen.
     *
     * @param int[] $teamIds
     * @return int[]
     */
    private function spielIds(array $teamIds): array
    {
        $result = [];
        foreach ($this->spiele as $spielId => $spiel) {
            if (
                in_array($spiel->getTeamA()->id(), $teamIds)
                && in_array($spiel->getTeamB()->id(), $teamIds)
            ) {
                $result[] = $spielId;
            }
        }
        return $result;
    }

    private function istTurnierBeendet(): bool
    {
        if (!empty($this->penaltys->ausstehend)) {
            return false;
        }
        $minSpiele = min(array_map(
            static fn(Turniertabellenzeile $zeile) => $zeile->spiele,
            $this->turniertabelle,
        ));
        return $this->anzahlSpiele === $minSpiele;
    }
}
