<?php

namespace App\Service\Spielplan;

use App\Entity\Turnier\Spiel;
use Html;

/**
 * Findet für ein Spiel die kontrastreichste Trikotfarbenkombination der beiden Teams.
 *
 * Portiert aus Spielplan::get_trikot_colors(). Die Delta-E-Berechnung entspricht der
 * (vereinfachten) "low-cost approximation" nach https://www.compuphase.com/cmetric.htm
 */
final class TrikotfarbenService
{
    /** Ab diesem Delta-E gilt Trikotfarbe 1 als ausreichend kontrastreich. */
    private const DELTA_E_SCHWELLE = 400;

    /**
     * @return array<int, string> Team-ID => Farbe (Hex bzw. HTML-Punkt), nur für Teams mit hinterlegter Farbe
     */
    public static function ermittle(Spiel $spiel, bool $html = true): array
    {
        $teamIdA = $spiel->getTeamA()->id();
        $teamIdB = $spiel->getTeamB()->id();

        $farben = [
            $teamIdA => array_filter([
                $spiel->getTeamA()->getDetails()?->getTrikotFarbe1(),
                $spiel->getTeamA()->getDetails()?->getTrikotFarbe2(),
            ]),
            $teamIdB => array_filter([
                $spiel->getTeamB()->getDetails()?->getTrikotFarbe1(),
                $spiel->getTeamB()->getDetails()?->getTrikotFarbe2(),
            ]),
        ];

        $ausgabe = static fn(string $farbe): string => $html ? Html::trikot_punkt($farbe) : $farbe;

        $return = [];
        $maxDeltaE = 0;
        foreach ($farben[$teamIdA] as $farbeA) {
            foreach ($farben[$teamIdB] as $farbeB) {
                $deltaE = self::deltaE($farbeA, $farbeB);
                if ($deltaE > $maxDeltaE) {
                    if ($maxDeltaE > self::DELTA_E_SCHWELLE) {
                        continue;
                    }
                    $maxDeltaE = $deltaE;
                    $return[$teamIdA] = $ausgabe($farbeA);
                    $return[$teamIdB] = $ausgabe($farbeB);
                }
            }
        }

        // Nur ein Team hat eine Farbe hinterlegt
        if (!$farben[$teamIdA] && $farben[$teamIdB]) {
            $return[$teamIdB] = $ausgabe(array_values($farben[$teamIdB])[0]);
        }
        if ($farben[$teamIdA] && !$farben[$teamIdB]) {
            $return[$teamIdA] = $ausgabe(array_values($farben[$teamIdA])[0]);
        }

        return $return;
    }

    private static function deltaE(string $hexColor1, string $hexColor2): float
    {
        [$r1, $g1, $b1] = sscanf($hexColor1, "#%02x%02x%02x");
        [$r2, $g2, $b2] = sscanf($hexColor2, "#%02x%02x%02x");
        $rMittel = ($r1 + $r2) / 2;
        $rDelta = $r1 - $r2;
        $gDelta = $g1 - $g2;
        $bDelta = $b1 - $b2;

        return (
            (2 + $rMittel / 256) * $rDelta ** 2
            + 4 * $gDelta ** 2
            + (2 + (255 - $rMittel) / 256) * $bDelta ** 2
        ) ** 0.5;
    }
}
