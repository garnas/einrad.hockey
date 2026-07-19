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
require_once '../../logic/turnier_bearbeiten.logic.php';

$saison = (isset($_GET['saison'])) ? (int) $_GET['saison'] : Config::SAISON;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Turnierdaten ändern</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<h2 class="w3-text-primary">
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
</h2>

<form method="post">   
    <?php include "../../templates/turnier/form_ausrichter.tmp.php"; ?>
    <?php include "../../templates/turnier/form_daten.tmp.php"; ?>
    <?php include "../../templates/turnier/form_block.tmp.php"; ?>
    <?php include "../../templates/turnier/form_plaetze.tmp.php"; ?>
    <?php include "../../templates/turnier/form_anfahrt.tmp.php"; ?>
    <?php include "../../templates/turnier/form_details.tmp.php"; ?>
    <?php include "../../templates/turnier/form_orga.tmp.php"; ?>

    <div class="w3-panel w3-card-4">
        <p>
            <input type="submit" value="Turnier bearbeiten" name="change_turnier" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>

<?php

include '../../templates/footer.tmp.php';
