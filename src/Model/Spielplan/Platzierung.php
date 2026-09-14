<?php

namespace App\Model\Spielplan;

/**
 * Ein platziertes Team in der Abschlusstabelle eines JgJ-Spielplans.
 *
 * Ersetzt das frühere Array
 * ['platz' => …, 'teamname' => …, 'ligapunkte' => …, 'statistik' => …, 'wertigkeit' => …].
 */
final class Platzierung
{
    public ?int $wertigkeit = null;

    public int|float $ligapunkte = 0;

    public function __construct(
        public readonly int $teamId,
        public readonly int $platz,
        public readonly string $teamname,
        public readonly Turniertabellenzeile $statistik,
    ) {}
}
