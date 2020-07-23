<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
$no_redirect = true;
require_once '../../logic/session_team.logic.php'; //Auth

$akt_team = new Team ($_SESSION['team_id']);
$akt_team_kontakte = new Kontakt ($_SESSION['team_id']);

//Werden an teamdaten.tmp.php übergeben
$emails = $akt_team_kontakte->get_all_emails();
$daten = $akt_team ->daten();

$change = false; // Wenn sich in teamdaten_aendern.logic etwas ändert, wird $change auf true gesetzt
require_once '../../logic/teamdaten_aendern.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/teamdaten_aendern.tmp.php';
?>

<p>
    <a class="no w3-text-primary w3-hover-text-secondary" href="tc_teamdaten.php"><i class="material-icons">chevron_left</i>Zurück<i class="material-icons" style="visibility: hidden">chevron_right</i></a>
</p>

<?php include '../../templates/footer.tmp.php';