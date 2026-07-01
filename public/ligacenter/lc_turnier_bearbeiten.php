<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/turnier_bearbeiten_first.logic.php'; //Turnier und $daten-Array erstellen + Sanitizing + Berechtigung Prüfen + Existiert das Turnier?
require_once '../../logic/turnier_bearbeiten.logic.php'; //Formularauswertung für Turnierdetails

$ausrichter = $turnier->getAusrichter();
$ausrichter_name = $ausrichter->getName();
$ausrichter_team_id = $ausrichter->id();
$ausrichter_block = $ausrichter->getBlock();

$saison = (isset($_GET['saison'])) ? (int) $_GET['saison'] : Config::SAISON;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Turnierdaten ändern (Ligaausschuss)</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<h2 class="w3-text-primary">
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
</h2>

<form method="post">   
    <?php
        include "../../templates/turnier/form_ausrichter.tmp.php";
        include "../../templates/turnier/form_daten.tmp.php";
        include "../../templates/turnier/form_block.tmp.php";
        include "../../templates/turnier/form_phase.tmp.php";
        include "../../templates/turnier/form_plaetze.tmp.php";
        include "../../templates/turnier/form_anfahrt.tmp.php";
        include "../../templates/turnier/form_details.tmp.php";
        include "../../templates/turnier/form_orga.tmp.php";
    ?>

    <div class="w3-panel w3-card-4">
        <p>
            <input type="submit" value="Turnier bearbeiten" name="change_turnier" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>

<p>
    <a href='../liga/turnier_details.php?turnier_id=<?=$turnier->id()?>'><button class="w3-button w3-text-primary w3-border w3-border-primary no">Zu den Turnierdetails</button></a>
    <a href='../ligacenter/lc_turnierliste.php?turnier_id=<?=$turnier->id()?>'><button style='display: inline;' class="w3-button w3-right w3-border w3-border-primary w3-text-primary no">Turniere verwalten (Liste)</button></a>
</p>

<?php
include '../../templates/footer.tmp.php';
