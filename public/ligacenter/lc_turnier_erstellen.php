<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/la_team_waehlen.logic.php';

$error = true;
if (isset($_GET['team_id'])){
    $ausrichter_team_id = $_GET['team_id'];
    $ausrichter_name = Team::teamid_to_teamname($ausrichter_team_id);
    if(empty($ausrichter_name)){
        Form::error("Ungültige TeamID");
        header('Location: lc_turnier_erstellen.php');
        die();
    }else{
        if (Tabelle::get_team_block($ausrichter_team_id) == 'A'){
            $ausrichter_block = 'AB'; //Da A-Teams keine A-Turniere ausrichten dürfen, werden sie bei der Erstellung von Turnieren wie AB-Teams behandelt.
        }else{
            $ausrichter_block = Tabelle::get_team_block($ausrichter_team_id);
        }
        $error = false;
    }
}

//Formularauswertung
if (!$error){
    require_once '../../logic/turnier_erstellen.logic.php';
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<h2> Turnier erstellen (Ligaausschuss) </h2>

<?php
include '../../templates/la_team_waehlen.tmp.php';
if (!$error){include '../../templates/turnier_erstellen.tmp.php';}
include '../../templates/footer.tmp.php';