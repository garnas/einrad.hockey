<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/la_team_waehlen.logic.php';

use App\Repository\Team\TeamRepository;

$saison = (isset($_GET['saison'])) ? (int) $_GET['saison'] : Config::SAISON;

$show_form = false;
if (isset($_GET['team_id'])) {

    $ausrichter_team_id = (int) $_GET['team_id'];
    if (Team::is_ligateam($ausrichter_team_id)) {
        $ausrichter = TeamRepository::get()->team($ausrichter_team_id);
        $show_form = true;
        $ausrichter_name = $ausrichter->getName();
        $ausrichter_block = $ausrichter->getBlock();
        require_once '../../logic/turnier_erstellen.logic.php';

    } else {
        Html::error("Ungültige Team-ID");
        Helper::reload(); // Beendet dieses Skript
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<h1 class="w3-text-primary">Turnier erstellen (Ligaausschuss)</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<?php include '../../templates/la_team_waehlen.tmp.php'; ?>


<?php if ($show_form): ?>

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
    <?php endif; ?>

<?php include '../../templates/footer.tmp.php';
