<?php

namespace App\Service\Spielplan;

use App\Entity\Spielplan\SpielplanDetails;
use App\Model\Spielplan\Platzierungsergebnis;

/**
 * Berechnet Teamwertigkeiten und Ligapunkte für die Abschlusstabelle eines JgJ-Turniers
 * und schreibt sie in die {@see \App\Model\Spielplan\Platzierung}-Objekte.
 *
 * Portiert aus Spielplan::set_wertigkeiten() / Spielplan::set_ligapunkte().
 */
final class LigapunkteService
{
    /** Faktoren für Spielpläne mit <= 3 Plätzen (1./2./3. Platz) */
    private const FAKTOREN_KLEINE_TURNIERE = [1.0, 0.75, 0.5];

    /** Anhebung der Gesamtwertung bei <= 3 Plätzen */
    private const GESAMTWERTUNG_ANHEBUNG = 1.5;

    /**
     * @param array<int, array> $teamliste keyed by Team-ID ({@see \App\Service\Turnier\TurnierService::getSpielenliste()})
     */
    public static function anwenden(Platzierungsergebnis $ergebnis, ?SpielplanDetails $details, array $teamliste): void
    {
        if ($details === null) {
            return;
        }
        self::setWertigkeiten($ergebnis, $teamliste);
        self::setLigapunkte($ergebnis, $details);
    }

    /**
     * @param array<int, array> $teamliste
     */
    private static function setWertigkeiten(Platzierungsergebnis $ergebnis, array $teamliste): void
    {
        $reverse = array_reverse($ergebnis->platzierungstabelle, true);

        // Wertung des schlechtplatziertesten Ligateams
        $letztesLigateam = static function () use ($reverse, $teamliste): ?int {
            foreach (array_keys($reverse) as $teamId) {
                if (($teamliste[$teamId]['wertigkeit'] ?? null) !== null) {
                    return $teamliste[$teamId]['wertigkeit'];
                }
            }
            return null;
        };

        $bisherige = null;
        foreach (array_keys($reverse) as $teamId) {
            if (($teamliste[$teamId]['wertigkeit'] ?? null) === null) {
                // Nichtligateam: max(bisherige) + 1, bzw. abgeleitet aus dem letzten Ligateam
                $wert = max($bisherige ?? [round($letztesLigateam() / 2 - 1), 14]) + 1;
            } else {
                $wert = $teamliste[$teamId]['wertigkeit'];
            }
            $bisherige[] = $wert;
            $ergebnis->platzierungstabelle[$teamId]->wertigkeit = $wert;
        }
    }

    private static function setLigapunkte(Platzierungsergebnis $ergebnis, SpielplanDetails $details): void
    {
        $plaetze = $details->getPlaetze();
        $reverse = array_reverse($ergebnis->platzierungstabelle, true);

        if ($plaetze > 3) {
            $ligapunkte = 0;
            foreach (array_keys($reverse) as $teamId) {
                $ligapunkte += $ergebnis->platzierungstabelle[$teamId]->wertigkeit;
                $ergebnis->platzierungstabelle[$teamId]->ligapunkte = round($ligapunkte * (float) $details->getFaktor());
            }
            return;
        }

        $gesamtwertung = 0;
        foreach (array_keys($reverse) as $teamId) {
            $gesamtwertung += $ergebnis->platzierungstabelle[$teamId]->wertigkeit;
        }
        $gesamtwertung *= self::GESAMTWERTUNG_ANHEBUNG;

        $counter = 3;
        foreach (array_keys($reverse) as $teamId) {
            $faktor = self::FAKTOREN_KLEINE_TURNIERE[$counter - 1] ?? 0;
            $ergebnis->platzierungstabelle[$teamId]->ligapunkte = round($gesamtwertung * $faktor);
            $counter--;
        }
    }
}
