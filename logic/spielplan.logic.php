<?php

use App\Repository\Turnier\TurnierRepository;

$turnier_id = (int) @$_GET['turnier_id'];

$turnier = TurnierRepository::get()->turnier($turnier_id);
if (!Spielplan::check_exist($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

// Spielplan laden
$saison = $turnier->getSaison();
if ($saison <= 30) {
    $spielplan = new Archiv_Spielplan_JgJ($turnier);
} else {
    $spielplan = new Spielplan_JgJ($turnier);
}