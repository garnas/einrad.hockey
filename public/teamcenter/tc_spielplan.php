<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation
require_once '../../logic/spielplan_form.logic.php'; //Wertet Formular aus

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Ergebnisse eintragen | Teamcenter";
$content = "Der Spielplan für das Einradhockey-Turnier in ". $spielplan->turnier->details['ort'] . " am " . date("d.m.Y", strtotime($spielplan->turnier->details['datum']));
include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_teamliste.tmp.php'; //Teamliste
include '../../templates/spielplan/spielplan_spieleTabelleForm.tmp.php'; //Spielplan
include '../../templates/spielplan/spielplan_spieleTabelleForm_mobile.tmp.php'; //Spielplan Mobil
include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; //Turniertabelle
include '../../templates/spielplan/spielplan_ergebnisTabelle_mobile.tmp.php'; //Turniertabelle Mobil
include '../../templates/spielplan/spielplan_ergebnis_senden.tmp.php'; //Ergebnis senden
include '../../templates/footer.tmp.php';