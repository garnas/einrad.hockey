<?php

namespace App\Spielplan\Model;

/**
 * Ergebnis der Platzierungsberechnung eines JgJ-Spielplans
 * ({@see \App\Spielplan\Service\PlatzierungService}).
 */
final class Platzierungsergebnis
{
    /**
     * @param array<int, Platzierung> $platzierungstabelle keyed by Team-ID, in Platzierungsreihenfolge
     * @param array<int, array<int, Turniertabellenzeile>> $direkterVergleichTabellen Zwischentabellen des direkten Vergleichs
     * @param array<int, array<int, Turniertabellenzeile>> $penaltyTabellen Zwischentabellen des Penaltyvergleichs
     * @param array<int, Turniertabellenzeile> $turniertabelle vollständige, sortierte Turniertabelle (keyed by Team-ID)
     */
    public function __construct(
        public array $platzierungstabelle,
        public array $direkterVergleichTabellen,
        public array $penaltyTabellen,
        public Penaltyuebersicht $penaltys,
        public bool $outOfScope,
        public array $turniertabelle,
    ) {}
}
