<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once Env::BASE_PATH . '/logic/session_team.logic.php'; //Auth

$liga_team_id = $_SESSION['logins']['team']['id'];

$team = new Team ($liga_team_id);
$kontakte = new Kontakt ($liga_team_id);

//Werden an terminseite_erstellen.tmp.php übergeben
$emails = $kontakte->get_emails();
$daten = $team->details;

require_once '../../logic/terminseite_erstellen.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/terminseite_erstellen.tmp.php';
include '../../templates/footer.tmp.php';
