<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$turnier_id = $_GET['turnier_id'];
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$titel = "Spielplan | Einradhockey";
Config::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->details['ort']
                    . " am " . date("d.m.Y", strtotime($spielplan->turnier->details['datum']));

include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_titel.tmp.php';
include '../../templates/spielplan/spielplan_teamliste.tmp.php'; // Teamliste
include '../../templates/spielplan/spielplan_spiele.tmp.php'; // Spiele
include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Abschlusstabelle
include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
include '../../templates/footer.tmp.php';

