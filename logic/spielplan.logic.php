<?php

use App\Repository\Turnier\TurnierRepository;

$turnier_id = (int) @$_GET['turnier_id'];

$turnier = TurnierRepository::get()->turnier($turnier_id);

if ($turnier->getSpielplanDatei()) {
    Helper::reload($turnier->getSpielplanDatei());
}

// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

// Spielplan laden
$spielplan = new Spielplan_JgJ($turnier);

// Ergebnis laden - falls vorhanden
$ergebnisse = $turnier->getErgebnis();

foreach ($ergebnisse as $ergebnis_id => $ergebnis) {
    $team_id = $ergebnis->getTeam()->id();
    $spielplan->platzierungstabelle[$team_id]['ligapunkte'] = $ergebnis->getErgebnis();
}
