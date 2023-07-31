<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../init.php';
// Turnier-ID

$teamliste = [
    1 => "Deutschland schwarz",
    2 => "TV Lilienthal Moorlichter",
    3 => "Deutschland rot",
    4 => "Dresdner Einradlöwen",
    5 => "Deutschland gold",
    6 => "TV Lilienthal Moorteufel",
    7 => "MJC Trier",
    8 => "Lucky Shots",
    9 => "Team Steyr Unicycling",
];
$schiri = [
    1 => "Sch",
    2 => "Mlr",
    3 => "Rot",
    4 => "Löw",
    5 => "Gol",
    6 => "Mtl",
    7 => "Tri",
    8 => "Lky",
    9 => "Sty",
];
$vorlage = "euhc_b";
$turnier_id = 0;
$startzeit = "12:30:00";
require_once Env::BASE_PATH . '/logic/spielplan_euhc.logic.php';
$spiele_backup = $spielplan->spiele;
$spiele_mittwoch = array_slice($spiele_backup, 0, 16, preserve_keys: true);
$spiele_donnerstag = array_slice($spiele_backup, 16, preserve_keys: true);
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "EUHC Spielplan";
//Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->get_ort()
//                    . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));
include Env::BASE_PATH . '/templates/header_euhc.tmp.php';?>
<h2 class="w3-text-secondary w3-bottombar w3-border-tertiary w3-margin-top"><?=Html::icon("group", tag:"h1") ?> B-Teams</h2>

<?php

include Env::BASE_PATH . '/templates/spielplan/spielplan_turniertabelle_euhc.tmp.php'; // Abschlusstabelle
include Env::BASE_PATH . '/templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
?>

<?php
$spielplan->spiele = $spiele_mittwoch;
?><h1 class="w3-text-secondary w3-bottombar	w3-border-tertiary"><?=Html::icon("event", tag:"h1") ?> Mittwoch, 02.08.23</h1><?php
include Env::BASE_PATH . '/templates/spielplan/spielplan_spiele_euhc.tmp.php'; // Spiele
$spielplan->spiele = $spiele_donnerstag;
?>
<div class="w3-margin-top"></div>
<h1 class="w3-text-secondary w3-bottombar	w3-border-tertiary"><?=Html::icon("event", tag:"h1") ?> Donnerstag, 03.08.23</h1><?php
include Env::BASE_PATH . '/templates/spielplan/spielplan_spiele_euhc.tmp.php'; // Spiele
$spielplan->spiele = $spiele_backup;

?>
<div class="w3-margin-top">
    <p><?= Html::link("https://einrad.hockey/euhc_b", "Direkter Link zum Spielplan", extern: true, icon: "launch") ?></p>
</div>
