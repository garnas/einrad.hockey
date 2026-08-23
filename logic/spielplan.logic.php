<?php

use App\Repository\Turnier\TurnierRepository;

$turnier_id = (int) @$_GET['turnier_id'];

$turnier = nTurnier::get($turnier_id);
// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

// Spielplan laden
$spielplan = new Spielplan_JgJ($turnier);

// Ergebnis laden - falls vorhanden
$turnier_entity = TurnierRepository::get()->turnier($turnier_id);
$ergebnisse = $turnier_entity->getErgebnis();

foreach ($ergebnisse as $ergebnis_id => $ergebnis) {
    $team_id = $ergebnis->getTeam()->id();
    $spielplan->platzierungstabelle[$team_id]['ligapunkte'] = $ergebnis->getErgebnis();
}
