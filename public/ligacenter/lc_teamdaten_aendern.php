<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/la_session.logic.php'; //Auth

$team_id = $_GET['team_id'];
$akt_team = new Team ($team_id);
$akt_team_kontakte = new Kontakt ($team_id);

//Werden an teamdaten_aendern.tmp.php und an die .logic Dateien übergeben
$daten = $akt_team ->daten();
if (empty($daten)) {
  Form::error("Team wurde nicht gefunden");
}

$emails = $akt_team_kontakte->get_all_emails();

$change = false; // Wenn sich in teamdaten_aendern.logic etwas ändert, wird $change auf true gesetzt
require_once '../../logic/teamdaten_aendern.logic.php'; //Formularverarbeitung

//$daten wird ebenfalls an teamdaten_aendern_la.tmp.php übergeben
//Nur Ligaausschuss
require_once '../../logic/teamdaten_aendern_la.logic.php'; //Formularverarbeitung

//Damit aktuelle Änderungen dargestellt werden.
if ($change){
  $daten = $akt_team ->daten();
  $emails = $akt_team_kontakte->get_all_emails();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$page_width = "800px";
include '../../templates/header.tmp.php';

if (!empty($daten)){
  include '../../templates/teamdaten_aendern_la.tmp.php';
  include '../../templates/teamdaten_aendern.tmp.php';
}

include '../../templates/footer.tmp.php';