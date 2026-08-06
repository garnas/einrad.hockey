<?php

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

// Check ob das Team über fünf Spieler verfügt
if (count(nSpieler::get_kader($_SESSION['logins']['team']['id'])) < 5) {
    Html::info('Bitte trag deinen Teamkader ein, um Turniere zu erstellen.');
    header('Location: ../teamcenter/tc_kader.php');
    die();
}

$ausrichter_team_id = $_SESSION['logins']['team']['id'];
$ausrichter = TeamRepository::get()->team($ausrichter_team_id);
$ausrichter_name = $ausrichter->getName();
$ausrichter_block = $ausrichter->getBlock();

$saison = (isset($_GET['saison'])) ? (int) $_GET['saison'] : Config::SAISON;

// Formularauswertung
require_once '../../logic/turnier_erstellen.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Turnier erstellen</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

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
            <input type="submit" value="Turnier eintragen" name="create_turnier" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>

<?php
include '../../templates/footer.tmp.php';
