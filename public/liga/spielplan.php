<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
#db::write("UPDATE `spiele` SET `tore_a` = ROUND(4*RAND(),0) ,`tore_b`= ROUND(4*RAND(),0)");
#db::write("UPDATE `spiele` SET `tore_a` = 1 ,`tore_b`= 1");
#db::write("UPDATE `spiele` SET `penalty_a` = NULL ,`penalty_b`= NULL");

#Spielplan::upload_spielplan(new Turnier ($_GET['turnier_id']));
$turnier_id = $_GET['turnier_id'];
#Spielplan::delete_spielplan(new Turnier ($_GET['turnier_id']));

require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation
db::debug($spielplan->ausstehende_penaltys);
db::debug($spielplan->gesamt_penaltys);
#db::debug($spielplan->get_penalty_begegnungen());
#db::debug($spielplan->penalty_begegnungen);
#db::debug($spielplan->ausstehende_penalty_begegnungen);
/*
$function = function ($mean, $sd){
    $x = mt_rand()/mt_getrandmax();
    $y = mt_rand()/mt_getrandmax();
    return abs(round(sqrt(-2*log($x))*cos(2*pi()*$y)*$mean + $sd));
};
foreach ((new Spielplan($turnier))->get_spiele() as $spiel){
    $spiel_id = $spiel['spiel_id'];
    #db::write("UPDATE spiele SET tore_a = ". $function(5,1) ." ,tore_b = ". $function(4,1) ." WHERE spiel_id = $spiel_id AND turnier_id = " . $_GET['turnier_id']);
}

$spielplan = new Spielplan($turnier_id);

$spielplan->direkter_vergleich($spielplan->get_toretabelle(), true, false);

$spielplan->check_tabelle_einblenden();

$spielplan->filter_ausstehende_penalty_begegnungen();
foreach ($spielplan->penalty_begegnungen as $key => $penalty_teams){
    if ($spielplan->penalty_warnung[$key]['unvermeidbar']) Form::attention("PENALTY PENALTY");
}

*/
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

