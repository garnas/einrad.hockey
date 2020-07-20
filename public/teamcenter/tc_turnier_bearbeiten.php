<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/team_session.logic.php'; //Auth

$turnier_id = $_GET['turnier_id'];
$akt_turnier = new Turnier($turnier_id);
$daten = $akt_turnier->daten;

//Turnier und $daten-Array erstellen +
//Sanitizing + Berechtigung Prüfen + Existiert das Turnier?
require_once '../../logic/turnier_bearbeiten_first.logic.php';

//Formularauswertung
require_once '../../logic/turnier_bearbeiten_teams.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h2 class="w3-text-primary">
    <span class="w3-text-grey">Turnierdaten ändern</span><br>
    <?=$daten['ort']?> (<?=$daten['tblock']?>), <?=date("d.m.Y", strtotime($daten['datum']))?>
</h2>

<p><a class="w3-text-hover-secondary w3-text-blue no" href='../liga/turnier_details.php?turnier_id=<?=$daten['turnier_id']?>'>Zu den Turnierdetails</a></p>

<?php
include '../../templates/turnier_bearbeiten_teams.tmp.php';
include '../../templates/footer.tmp.php';