<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Turnier\Service\TurnierPermission;
use App\Turnier\Service\TurnierSnippets;

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

<?php if (TurnierPermission::isNurHinweisAenderbar($turnier)): ?>
<form method="post">
    <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
        <p>Abschlussturniere werden vom Ligaausschuss organisiert. Als Ausrichter könnt ihr die Hinweise zum Turnier ändern.</p>
    </div>
    <div class="w3-panel w3-card-4">
        <h3 class="w3-text-secondary">Hinweise</h3>
        <p>
            <label class="w3-text-primary" for="text">Weitere Informationen</label>
            <textarea class="w3-input w3-border w3-border-primary" onkeyup="woerter_zaehlen(1500);" maxlength="1500"
                      rows="6" id="text" name="hinweis"
            ><?= stripcslashes(e($turnier_hinweis ?? '')) ?></textarea>
        </p>
        <p id="counter"></p>
        <p>
            <input type="submit" value="Hinweise speichern" name="change_hinweis" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>
<?php else: ?>
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
<?php endif; ?>

<?php

include '../../templates/footer.tmp.php';
