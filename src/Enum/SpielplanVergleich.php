<?php

namespace App\Enum;

/**
 * Vergleichsarten für die Gleichplatzierung von Teams im JgJ-Spielplan.
 *
 * Ersetzt die früheren String-Konstanten ("nur_punkte", "nur_tordifferenz",
 * "nur_punkte_penalty", "nur_tordifferenz_penalty") aus Spielplan_JgJ::get_gleichplatzierte_teams().
 */
enum SpielplanVergleich
{
    case PUNKTE;
    case TORDIFFERENZ;
    case PENALTY_PUNKTE;
    case PENALTY_DIFFERENZ;
}
