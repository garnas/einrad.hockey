<?php

namespace App\Model\Spielplan;

/**
 * Sammelt die im JgJ-Spielplan zu spielenden Penaltybegegnungen.
 *
 * Ersetzt das frühere Array ['gesamt' => [], 'ausstehend' => [], 'kontrolle' => []]
 * aus Spielplan_JgJ.
 */
final class Penaltyuebersicht
{
    /** @var int[] Alle Spiel-IDs, für die ein Penalty gespielt werden muss */
    public array $gesamt = [];

    /** @var int[] Spiel-IDs der Penaltys, deren Ergebnis noch nicht eingetragen ist */
    public array $ausstehend = [];

    /** @var int[] Spiel-IDs einer ggf. nötigen zweiten Penaltyrunde (out of scope) */
    public array $kontrolle = [];
}
