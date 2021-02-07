<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth

// Sanitizing + Berechtigung Prüfen + Existiert das Turnier?
require_once '../../logic/turnier_bearbeiten_first.logic.php';

// Formularauswertung
require_once '../../logic/turnier_bearbeiten_teams.logic.php';

Form::attention("Achtung - die Teams und der Ligaausschuss müssen angeschrieben werden, sollten wichtige Turnierdaten geändert werden.");
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h2 class="w3-text-primary">
    <span class="w3-text-grey">Turnierdaten ändern</span><br>
    <?=$turnier->details['ort']?> (<?=$turnier->details['tblock']?>), <?=date("d.m.Y", strtotime($turnier->details['datum']))?>
</h2>

<p><?=Form::link('../liga/turnier_details.php?turnier_id='. $turnier->details['turnier_id'], '<i class="material-icons">info</i> Alle Turnierdetails')?></p>

<?php
include '../../templates/turnier_bearbeiten_teams.tmp.php';
include '../../templates/footer.tmp.php';