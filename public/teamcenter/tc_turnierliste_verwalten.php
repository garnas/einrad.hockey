<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth

$heute = date("Y-m-d");
//wird dem template übergeben
$turniere = Turnier::get_eigene_turniere($_SESSION['team_id']);

if (empty($turniere)){
    Form::affirm('Dein Team richtet zurzeit kein Turnier aus - Erstelle ein Turnier, um es verwalten zu können');
    header('Location: tc_turnier_erstellen.php');
    die();
}

//Füge Links zum Weiterverarbeiten der ausgewählten Turniere hinzu
//diese werden dem Teamplate übergeben

foreach ($turniere as $turnier_id => $turnier){
    //Links
    $turniere[$turnier_id]['links'] = 
        array(
            Form::link("tc_turnier_bearbeiten.php?turnier_id=".$turnier_id, '<i class="material-icons">create</i> Turnier bearbeiten'), 
            Form::link("../liga/turnier_details.php?turnier_id=".$turnier_id, '<i class="material-icons">info</i> Details')
        );
    if ($turnier['art'] == 'spass'){
        array_push($turniere[$turnier_id]['links'], Form::link('../teamcenter/tc_spassturnier_anmeldung.php?turnier_id=' . $turnier['turnier_id'],'<i class="material-icons">how_to_reg</i> Teams manuell anmelden'));
    }
    if ($turnier['phase'] == 'spielplan'){
        array_push($turniere[$turnier_id]['links'], '<b>' . Form::link('../teamcenter/tc_spielplan.php?turnier_id=' . $turnier['turnier_id'],'<i class="material-icons">reorder</i> Ergebnisse eintragen') . '</b>');
        array_push($turniere[$turnier_id]['links'], '<b>' . Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier['turnier_id'],'<i class="material-icons">article</i> Turnierreport eintragen') . '</b>');
        $turniere[$turnier_id]['row_color'] = 'w3-pale-yellow';
    }
    if ($turnier['phase'] == 'ergebnis'){
        array_push($turniere[$turnier_id]['links'], Form::link('../teamcenter/tc_spielplan.php?turnier_id=' . $turnier['turnier_id'],'<i class="material-icons">reorder</i> Ergebnisse verändern'));
        array_push($turniere[$turnier_id]['links'], Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier['turnier_id'],'<i class="material-icons">article</i> Turnierreport verändern'));
        $turniere[$turnier_id]['row_color'] = 'w3-pale-green';
    }
}

include '../../logic/turnierliste.logic.php'; //Auth

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<h3 class="w3-text-primary">Eigene Turniere verwalten</h3>

<?php 
include '../../templates/turnierliste.tmp.php';
include '../../templates/footer.tmp.php';