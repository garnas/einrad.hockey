<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

//Check ob das Team über fünf Spieler verfügt
if (count(Spieler::get_teamkader($_SESSION['logins']['team']['id'])) < 5){
    Html::info('Bitte trag deinen Teamkader ein, um Turniere zu erstellen.');
    header('Location: ../teamcenter/tc_kader.php');
    die();
}

$ausrichter_team_id = $_SESSION['logins']['team']['id'];
$ausrichter_name = $_SESSION['logins']['team']['name'];
$ausrichter_block = $_SESSION['logins']['team']['block'];

// Formularauswertung
require_once '../../logic/turnier_erstellen.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/turnier_erstellen.tmp.php';
include '../../templates/footer.tmp.php';