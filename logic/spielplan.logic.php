<?php

$turnier_id = (int) @$_GET['turnier_id'];

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