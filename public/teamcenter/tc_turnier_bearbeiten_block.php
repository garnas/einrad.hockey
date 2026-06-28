<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

// Sanitizing + Berechtigung Prüfen + Existiert das Turnier?
require_once '../../logic/turnier_bearbeiten_first.logic.php';

// Formularauswertung
require_once '../../logic/turnier_bearbeiten_teams.logic.php';

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

<?php

include '../../templates/turnier_bearbeiten_block_teams.tmp.php';
include '../../templates/footer.tmp.php';
