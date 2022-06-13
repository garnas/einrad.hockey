<?php

// Turnier-ID
$turnier_id = (int) @$_GET['turnier_id'];

Spielplan_Final::routeToFinalSpielplan($turnier_id); // Todo, allgemeiner Router für spezialspielpläne?

$turnier = nTurnier::get($turnier_id);
// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

// Spielplan laden
$spielplan = new Spielplan_JgJ($turnier);