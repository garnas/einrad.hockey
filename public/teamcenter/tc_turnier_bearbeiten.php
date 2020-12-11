<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth

$turnier_id = $_GET['turnier_id'];
$akt_turnier = new Turnier($turnier_id);
$daten = $akt_turnier->details;

//Turnier und $daten-Array erstellen +
//Sanitizing + Berechtigung Prüfen + Existiert das Turnier?
require_once '../../logic/turnier_bearbeiten_first.logic.php';

//Formularauswertung
require_once '../../logic/turnier_bearbeiten_teams.logic.php';

Form::attention("Achtung - die Teams und der Ligaausschuss müssen angeschrieben werden, sollten wichtige Turnierdaten geändert werden.");
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h2 class="w3-text-primary">
    <span class="w3-text-grey">Turnierdaten ändern</span><br>
    <?=$daten['ort']?> (<?=$daten['tblock']?>), <?=date("d.m.Y", strtotime($daten['datum']))?>
</h2>

<p><?=Form::link('../liga/turnier_details.php?turnier_id='. $daten['turnier_id'], '<i class="material-icons">info</i> Alle Turnierdetails')?></p>

<?php
include '../../templates/turnier_bearbeiten_teams.tmp.php';
include '../../templates/footer.tmp.php';