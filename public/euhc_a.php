<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../init.php';
// Turnier-ID
$teamliste = [
    1 => "Swiss Team",
    2 => "Deutschland 1",
    3 => "Deutschland 2",
    4 => "Swiss Team 2",
    5 => "Deutschland 3",
    6 => "Aussie Deutsch United",
    7 => "Deutschland 4",
    8 => "B&B"
];
$schiri = [
    1 => "Sw1",
    2 => "De1",
    3 => "De2",
    4 => "Sw2",
    5 => "De3",
    6 => "AuD",
    7 => "De4",
    8 => "B&B",
];
$vorlage = "euhc_a";
$turnier_id = 1;
$startzeit = "10:00:00";
require_once Env::BASE_PATH . '/logic/spielplan_euhc.logic.php';
$spiele_backup = $spielplan->spiele;
$spiele_freitag = array_slice($spiele_backup, 0, 20, preserve_keys: true);
$spiele_samstag = array_slice($spiele_backup, 20, preserve_keys: true);
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "EUHC Spielplan";
//Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->get_ort()
//                    . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));
include Env::BASE_PATH . '/templates/header_euhc.tmp.php';?>
<h2 class="w3-text-secondary w3-bottombar w3-border-tertiary w3-margin-top"><?=Html::icon("group", tag:"h1") ?> A-Teams</h2>

<?php

include Env::BASE_PATH . '/templates/spielplan/spielplan_turniertabelle_euhc.tmp.php'; // Abschlusstabelle
include Env::BASE_PATH . '/templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
?>

<?php
$spielplan->spiele = $spiele_freitag;
?><h1 class="w3-text-secondary w3-bottombar	w3-border-tertiary"><?=Html::icon("event", tag:"h1") ?> Freitag, 04.08.2023</h1><?php
include Env::BASE_PATH . '/templates/spielplan/spielplan_spiele_euhc.tmp.php'; // Spiele
?>
<div class="w3-margin-top"></div>
<h1 class="w3-text-secondary w3-bottombar	w3-border-tertiary"><?=Html::icon("event", tag:"h1") ?> Samstag, 05.08.2023</h1><?php
$spielplan->spiele = $spiele_samstag;
include Env::BASE_PATH . '/templates/spielplan/spielplan_spiele_euhc.tmp.php'; // Spiele
$spielplan->spiele = $spiele_backup;

?>
<div class="w3-margin-top">
    <p><?= Html::link("https://einrad.hockey/euhc_a", "Direkter Link zum Spielplan", extern: true, icon: "launch") ?></p>
</div>
