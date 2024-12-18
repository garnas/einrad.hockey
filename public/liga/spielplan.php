<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Spielplan | Einradhockey";
Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->get_ort()
                    . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));

include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_titel.tmp.php';
include '../../templates/spielplan/spielplan_teamliste.tmp.php'; // Teamliste
include '../../templates/spielplan/spielplan_spiele.tmp.php'; // Spiele
if ($spielplan->anzahl_teams != 3) {
    include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Abschlusstabelle
    include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
}
include '../../templates/footer.tmp.php';

