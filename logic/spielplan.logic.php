<?php

use App\Repository\Turnier\TurnierRepository;
use App\Service\Team\FreilosService;

$turnier_id = (int) @$_GET['turnier_id'];

Spielplan_Final::routeToFinalSpielplan($turnier_id); // Todo, allgemeiner Router für spezialspielpläne?

$turnier = nTurnier::get($turnier_id);
// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

$turnierEntity = TurnierRepository::get()->turnier($turnier_id);
if (
    FreilosService::isAusrichterFreilosBerechtigt($turnierEntity)
) {
    if (FreilosService::hasAusrichterFreilosForAusgerichtetesTurnier($turnierEntity)) {
        HTML::info("Für dieses Turnier habt ihr mit Ergebniseintragung ein Freilos erhalten.");
    } else {
        HTML::notice("Für dieses Turnier erhaltet ihr mit Ergebniseintragung ein Freilos.");
    }
}

// Spielplan laden
$spielplan = new Spielplan_JgJ($turnier);