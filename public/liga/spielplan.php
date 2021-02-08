<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
//db::writedb("UPDATE `spiele` SET `tore_a` = ROUND(4*RAND(),0) ,`tore_b`= ROUND(4*RAND(),0)");
//db::writedb("UPDATE `spiele` SET `tore_a` = 1 ,`tore_b`= 1");
//db::writedb("UPDATE `spiele` SET `penalty_a` = NULL ,`penalty_b`= NULL");
//db::writedb("UPDATE `spiele` SET `tore_a` = NULL ,`tore_b`= NULL");

$turnier_id = $_GET['turnier_id'];
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation


/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Spielplan | Einradhockey";
$content = "Der Spielplan für das Einradhockey-Turnier in ". $spielplan->turnier->details['ort'] . " am " . date("d.m.Y", strtotime($spielplan->turnier->details['datum']));
include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_titel.tmp.php';
include '../../templates/spielplan/spielplan_teamliste.tmp.php'; //Teamliste
include '../../templates/spielplan/spielplan_spiele.tmp.php'; //Spiele
include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; //Abschlusstabelle
include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; //Abschlusstabelle
include '../../templates/footer.tmp.php';

