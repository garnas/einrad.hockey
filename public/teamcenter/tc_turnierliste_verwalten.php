<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth

$heute = date("Y-m-d", Config::time_offset());
$anmeldungen = Turnier::get_all_anmeldungen();
$akt_team = new Team($_SESSION['team_id']);
$anz_freilose = $akt_team->get_freilose();
//wird dem template übergeben
$turniere = Turnier::get_all_turniere("WHERE turniere_liga.datum > '$heute' AND turniere_liga.ausrichter = '".$_SESSION['team_id']."'");

if (empty($turniere)){
    Form::affirm('Dein Team richtet zurzeit kein Turnier aus - Erstelle ein Turnier, um es verwalten zu können');
    header('Location: tc_turnier_erstellen.php');
    die();
}

//Füge Links zum Weiterverarbeiten der ausgewählten Turniere hinzu
//diese werden dem Teamplate übergeben
foreach ($turniere as $key => $turnier){
    $turniere[$key]['link_bearbeiten'] = "tc_turnier_bearbeiten.php?turnier_id=". $turnier['turnier_id'];
    if ($turnier['plaetze'] > count($anmeldungen[$turnier['turnier_id']]['spiele'] ?? array())){
        $turniere[$key]['freivoll'] = '<span class="w3-text-green">frei</span>';
    }else{
        $turniere[$key]['freivoll'] = '<span class="w3-text-red">voll</span>';
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<h3 class="w3-text-primary">Eigene Turniere verwalten</h3>

<?php 
include '../../templates/turnierliste.tmp.php';
include '../../templates/footer.tmp.php';