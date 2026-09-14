<?php

namespace App\Service\Spielplan;

use App\Entity\Turnier\Spiel;
use App\Model\Spielplan\Turniertabellenzeile;

/**
 * Berechnet aus den Spielergebnissen eines JgJ-Turniers die Torematrix und die Turniertabelle.
 *
 * Portiert aus Spielplan::get_torematrix() / Spielplan::get_turniertabelle() /
 * Spielplan_JgJ::get_sorted_turniertabelle().
 */
final class TurniertabellenService
{
    /**
     * Matrix aller Begegnungen: matrix[teamId][gegnerId][] = einzelnes Spielergebnis
     * aus Sicht von teamId.
     *
     * @param Spiel[] $spiele
     * @return array<int, array<int, list<array{tore: ?int, gegentore: ?int, penalty_tore: ?int, penalty_gegentore: ?int}>>>
     */
    public static function getTorematrix(array $spiele, bool $mitPenaltys = true): array
    {
        $matrix = [];
        foreach ($spiele as $spiel) {
            $teamA = $spiel->getTeamA()->id();
            $teamB = $spiel->getTeamB()->id();
            $penaltyA = $mitPenaltys ? $spiel->getPenaltyA() : null;
            $penaltyB = $mitPenaltys ? $spiel->getPenaltyB() : null;

            $matrix[$teamA][$teamB][] = [
                'tore' => $spiel->getToreA(),
                'gegentore' => $spiel->getToreB(),
                'penalty_tore' => $penaltyA,
                'penalty_gegentore' => $penaltyB,
            ];
            $matrix[$teamB][$teamA][] = [
                'tore' => $spiel->getToreB(),
                'gegentore' => $spiel->getToreA(),
                'penalty_tore' => $penaltyB,
                'penalty_gegentore' => $penaltyA,
            ];
        }
        return $matrix;
    }

    /**
     * Aggregiert die Torematrix zu einer (unsortierten) Turniertabelle.
     *
     * @param array<int, array<int, list<array>>> $torematrix
     * @return array<int, Turniertabellenzeile> keyed by Team-ID
     */
    public static function getTurniertabelle(array $torematrix): array
    {
        $tabelle = [];
        foreach ($torematrix as $teamId => $gegner) {
            $punkte = $tordifferenz = $tore = $gegentore = null;
            $penaltyPunkte = $penaltyDiff = $penaltyTore = $penaltyGegentore = null;
            $spiele = 0;
            $penaltySpiele = 0;

            foreach ($gegner as $begegnungen) {
                foreach ($begegnungen as $spiel) {
                    if ($spiel['tore'] === null || $spiel['gegentore'] === null) {
                        continue;
                    }

                    $punkte += $spiel['tore'] > $spiel['gegentore'] ? 3 : 0;
                    $punkte += $spiel['tore'] === $spiel['gegentore'] ? 1 : 0;
                    $tordifferenz += $spiel['tore'] - $spiel['gegentore'];
                    $tore += $spiel['tore'];
                    $gegentore += $spiel['gegentore'];
                    $spiele++;

                    if ($spiel['penalty_tore'] === null || $spiel['penalty_gegentore'] === null) {
                        continue;
                    }

                    $penaltyPunkte += $spiel['penalty_tore'] > $spiel['penalty_gegentore'] ? 3 : 0;
                    $penaltyPunkte += $spiel['penalty_tore'] === $spiel['penalty_gegentore'] ? 1 : 0;
                    $penaltyDiff += $spiel['penalty_tore'] - $spiel['penalty_gegentore'];
                    $penaltyTore += $spiel['penalty_tore'];
                    $penaltyGegentore += $spiel['penalty_gegentore'];
                    $penaltySpiele++;
                }
            }

            $tabelle[$teamId] = new Turniertabellenzeile(
                teamId: $teamId,
                spiele: $spiele,
                punkte: $punkte,
                tordifferenz: $tordifferenz,
                tore: $tore,
                gegentore: $gegentore,
                penaltySpiele: $penaltySpiele,
                penaltyPunkte: $penaltyPunkte,
                penaltyDiff: $penaltyDiff,
                penaltyTore: $penaltyTore,
                penaltyGegentore: $penaltyGegentore,
            );
        }
        return $tabelle;
    }

    /**
     * @param array<int, array<int, list<array>>> $torematrix
     * @return array<int, Turniertabellenzeile> sortiert, keyed by Team-ID
     */
    public static function getSortierteTurniertabelle(array $torematrix): array
    {
        return (new Turniertabellensortierung())->sortiere(self::getTurniertabelle($torematrix));
    }
}
