<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Spielplan | Einradhockey";
$content = "Der Spielplan für das Einradhockey-Turnier in ". $spielplan->akt_turnier->details['ort'] . " am " . date("d.m.Y", strtotime($spielplan->akt_turnier->details['datum']));
include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_vorTurnierTabelle.tmp.php'; //Teamliste
include '../../templates/spielplan/spielplan_paarungen.tmp.php'; //Spiele
include '../../templates/spielplan/spielplan_paarungen_mobile.tmp.php'; //Spiele Mobil
include '../../templates/spielplan/spielplan_ergebnisTabelle_mobile.tmp.php'; //Abschlusstabelle
include '../../templates/spielplan/spielplan_ergebnisTabelle.tmp.php'; //Abschlusstabelle
include '../../templates/footer.tmp.php';