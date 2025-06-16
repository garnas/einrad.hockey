<?php
$turnier = new nTurnier(skip_init: true);
$turnier->turnier_id = $turnier_id;
$turnier->set_spielplan_vorlage_object($vorlage);
$turnier->set_startzeit($startzeit);
$turnier->set_besprechung("Nein");
$turnier->set_phase("spielplan");
$turnier->set_art("final");
$turnier->set_spielenliste_euhc($teamliste);


// Spielplan laden
$spielplan = new Spielplan_JgJ($turnier, skip_init: true);
foreach ($spielplan->spiele as $id => $spiel) {
    $spielplan->spiele[$id]["teamname_a"] = $teamliste[$spiel["team_id_a"]];
    $spielplan->spiele[$id]["teamname_b"] = $teamliste[$spiel["team_id_b"]];
}
$turnier->reset_log();
$schiri_ids = array_flip($teamliste);