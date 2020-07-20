<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/team_session.logic.php'; //Auth

$akt_team = new Team ($_SESSION['team_id']);
$akt_team_kontakte = new Kontakt ($_SESSION['team_id']);

//Werden an teamdaten.tmp.php übergeben
$emails = $akt_team_kontakte->get_all_emails();
$daten = $akt_team ->daten();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$page_width = "800px";
include '../../templates/header.tmp.php';
include '../../templates/teamdaten.tmp.php';
?>

<!--Navigation-->
<p>
  <a class="no w3-text-primary w3-hover-text-secondary" href='tc_teamdaten_aendern.php'><i class="material-icons">create</i> Teamdaten ändern</a>
</p>

<?php include '../../templates/footer.tmp.php';