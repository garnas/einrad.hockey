<?php

namespace App\Service\Spielplan;

use App\Model\Spielplan\Turniertabellenzeile;

/**
 * Kapselt die Reihenfolge der Kriterien, nach denen die JgJ-Turniertabelle sortiert wird.
 *
 * Ersetzt die früher ausgeschriebene, ~40-zeilige if-Kette in
 * Spielplan_JgJ::get_sorted_turniertabelle(): die Kriterien liegen jetzt als Datenliste vor.
 */
final class Turniertabellensortierung
{
    /**
     * Vergleichskriterien in absteigender Priorität (Property-Namen der {@see Turniertabellenzeile}).
     * Höherer Wert = besserer Platz; {@see null} zählt wie 0.
     *
     * @var string[]
     */
    private const KRITERIEN = [
        'punkte',
        'tordifferenz',
        'tore',
        'penaltyPunkte',
        'penaltyDiff',
        'penaltyTore',
    ];

    /**
     * @param array<int, Turniertabellenzeile> $zeilen keyed by Team-ID
     * @return array<int, Turniertabellenzeile> stabil sortiert, Keys erhalten
     */
    public function sortiere(array $zeilen): array
    {
        uasort($zeilen, [$this, 'vergleiche']);
        return $zeilen;
    }

    public function vergleiche(Turniertabellenzeile $a, Turniertabellenzeile $b): int
    {
        foreach (self::KRITERIEN as $feld) {
            $cmp = ($b->{$feld} ?? 0) <=> ($a->{$feld} ?? 0);
            if ($cmp !== 0) {
                return $cmp;
            }
        }
        return 0;
    }
}
