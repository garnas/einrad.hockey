<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
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
        <span class="w3-text-grey">Turnierdaten ändern</span>
        <br>
        <?= $turnier->details['ort']
        . ' (' . $turnier->details['tblock'] . ') '
        . date("d.m.Y", strtotime($turnier->details['datum'])) ?>
    </h2>

    <p>
        <?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->details['turnier_id'],
            'Alle Turnierdetails',
            icon: 'info') ?>
    </p>

<?php
Html::message('notice',
    "Bitte schreibt die Teams und den Ligaausschuss an, wenn wichtige Turnierdaten geändert werden.",
    '');
include '../../templates/turnier_bearbeiten_teams.tmp.php';
include '../../templates/footer.tmp.php';