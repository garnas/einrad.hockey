<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/turnier_bearbeiten_first.logic.php'; //Turnier und $daten-Array erstellen + Sanitizing + Berechtigung Prüfen + Existiert das Turnier?
require_once '../../logic/turnier_bearbeiten_la.logic.php'; //Formularauswertung für Ligaausschuss
require_once '../../logic/turnier_bearbeiten_teams.logic.php'; //Formularauswertung für Turnierdetails

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h2 class="w3-text-primary">
    <span class="w3-text-grey">Turnierblock ändern</span>
    <br>
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
</h2>

<p>
    <a href='../liga/turnier_details.php?turnier_id=<?=$turnier->id()?>'><button class="w3-button w3-text-primary w3-border w3-border-primary no">Zu den Turnierdetails</button></a>
    <a href='../ligacenter/lc_turnierliste.php?turnier_id=<?=$turnier->id()?>'><button style='display: inline;' class="w3-button w3-right w3-border w3-border-primary w3-text-primary no">Turniere verwalten (Liste)</button></a>
</p>


<?php include '../../templates/turnier_bearbeiten_block_la.tmp.php';?>

<?php

include '../../templates/footer.tmp.php';
