<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

$team_id = $_SESSION['logins']['team']['id'];

$kader = nSpieler::get_kader($team_id);
$kader_vorsaison = nSpieler::get_kader($team_id, Config::SAISON - 1);

//Formularauswertung neuer Spieler
require_once '../../logic/kader.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/kader.tmp.php';
include '../../templates/footer.tmp.php';

