<?php
$turnier = new nTurnier(skip_init: true);
$turnier->set_spielplan_vorlage_object("euhc_b");
$turnier->set_startzeit("12:30:00");
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
$spiele_backup = $spielplan->spiele;
$spiele_mittwoch = array_slice($spiele_backup, 0, 16, preserve_keys: true);
$spiele_donnerstag = array_slice($spiele_backup, 17, preserve_keys: true);
$turnier->reset_log();
$schiri_ids = array_flip($teamliste);