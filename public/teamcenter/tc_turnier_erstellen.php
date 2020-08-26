<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth

//Check ob das Team über fünf Spieler verfügt
if (count(Spieler::get_teamkader($_SESSION['team_id'])) < 5){
    Form::affirm('Bitte trag deinen Teamkader ein, um Turniere zu erstellen.');
    header('Location: ../teamcenter/tc_kader.php');
    die();
}

$ausrichter_team_id = $_SESSION['team_id'];
$ausrichter_name = $_SESSION['teamname'];
$ausrichter_block = $_SESSION['teamblock'];

//Formularauswertung
require_once '../../logic/turnier_erstellen.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/turnier_erstellen.tmp.php';
include '../../templates/footer.tmp.php';