<?php

use App\Repository\Turnier\TurnierRepository;

$turnier_id = (int) @$_GET['turnier_id'];

Spielplan_Final::routeToFinalSpielplan($turnier_id); // Todo, allgemeiner Router für spezialspielpläne?

$turnier = nTurnier::get($turnier_id);
// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

// Spielplan laden
$saison = $turnier->get_saison();
if ($saison <= 30) {
    $spielplan = new Archiv_Spielplan_JgJ($turnier);
} else {
    $spielplan = new Spielplan_JgJ($turnier);
}

// Ergebnis laden - falls vorhanden
$turnier_entity = TurnierRepository::get()->turnier($turnier_id);
$ergebnisse = $turnier_entity->getErgebnis();

foreach ($ergebnisse as $ergebnis_id => $ergebnis) {
    $team_id = $ergebnis->getTeam()->id();
    $spielplan->platzierungstabelle[$team_id]['ligapunkte'] = $ergebnis->getErgebnis();
}
