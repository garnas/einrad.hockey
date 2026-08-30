<?php

namespace App\Model\Spielplan;

use App\Enum\SpielplanVergleich;

/**
 * Typisierte Zeile der JgJ-Turniertabelle für genau ein Team.
 *
 * Ersetzt das früher genutzte assoziative Array aus Spielplan::get_turniertabelle()
 * mit den Schlüsseln spiele/punkte/tordifferenz/tore/gegentore/penalty_*.
 *
 * Punkt-, Tor- und Penaltywerte sind {@see null}, solange das Team noch kein
 * (Penalty-)Spiel absolviert hat – das ist für die Platzierungslogik relevant.
 */
final class Turniertabellenzeile
{
    public function __construct(
        public readonly int $teamId,
        public readonly int $spiele = 0,
        public readonly ?int $punkte = null,
        public readonly ?int $tordifferenz = null,
        public readonly ?int $tore = null,
        public readonly ?int $gegentore = null,
        public readonly int $penaltySpiele = 0,
        public readonly ?int $penaltyPunkte = null,
        public readonly ?int $penaltyDiff = null,
        public readonly ?int $penaltyTore = null,
        public readonly ?int $penaltyGegentore = null,
    ) {}

    /**
     * Rohwert (inkl. {@see null}) des für die Vergleichsart maßgeblichen Feldes.
     * Wird für die Gruppierung gleichplatzierter Teams verwendet.
     */
    public function wert(SpielplanVergleich $vergleich): ?int
    {
        return match ($vergleich) {
            SpielplanVergleich::PUNKTE => $this->punkte,
            SpielplanVergleich::TORDIFFERENZ => $this->tordifferenz,
            SpielplanVergleich::PENALTY_PUNKTE => $this->penaltyPunkte,
            SpielplanVergleich::PENALTY_DIFFERENZ => $this->penaltyDiff,
        };
    }
}
