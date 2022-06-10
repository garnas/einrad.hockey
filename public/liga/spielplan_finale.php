<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

// Turnier-ID
$turnier_id = (int) @$_GET['turnier_id'];

//// Gibt es einen Spielplan zu diesem Turnier?
//if (!Spielplan::check_exist($turnier_id)) {
//    Helper::not_found("Spielplan wurde nicht gefunden");
//}

// Spielplan laden
$turnier = nTurnier::get($turnier_id);

$spielplan = (new spielplan_final($turnier))->get_spielplan();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Spielplan | Einradhockey";
Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->get_ort()
    . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));

include '../../templates/header.tmp.php';
if ($turnier->get_phase() == "ergebnis") {
    Html::set_confetti();
}

include '../../templates/spielplan/spielplan_titel.tmp.php';
include '../../templates/spielplan/spielplan_teamliste.tmp.php'; // Teamliste
if (Env::ACTIVE_FINAL_DISCORD && $turnier->is_finalturnier()) {
    include '../../templates/spielplan/spielplan_discord_read.tmp.php'; // Spiele
}
include '../../templates/spielplan/spielplan_spiele.tmp.php'; // Spiele
include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Abschlusstabelle
include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
include '../../templates/footer.tmp.php';

