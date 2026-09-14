<?php

use App\Repository\Turnier\TurnierRepository;
use App\Service\Spielplan\SpielplanFactory;
use App\Service\Spielplan\SpielplanService;

$turnier_id = (int) @$_GET['turnier_id'];

$turnier = TurnierRepository::get()->turnier($turnier_id);

if ($turnier === null) {
    Helper::not_found("Turnier wurde nicht gefunden");
}

if ($turnier->getSpielplanDatei()) {
    Helper::reload($turnier->getSpielplanDatei());
}

// Gibt es einen Spielplan zu diesem Turnier?
if (!SpielplanService::hatSpielplan($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

// Spielplan laden
$spielplan = SpielplanFactory::ausTurnier($turnier);

// Ergebnis laden - falls vorhanden - und über die berechneten Ligapunkte legen
foreach ($turnier->getErgebnis() as $ergebnis) {
    $team_id = $ergebnis->getTeam()->id();
    if (isset($spielplan->getPlatzierungstabelle()[$team_id])) {
        $spielplan->getPlatzierungstabelle()[$team_id]->ligapunkte = $ergebnis->getErgebnis();
    }
}
