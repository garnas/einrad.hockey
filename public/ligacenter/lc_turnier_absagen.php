<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Turnier\Service\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/turnier_bearbeiten_first.logic.php'; //Turnier und $daten-Array erstellen + Sanitizing + Berechtigung Prüfen + Existiert das Turnier?
require_once '../../logic/turnier_absagen.logic.php';

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

<h1 class="w3-text-primary">Turnier absagen (Ligaausschuss)</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<h2 class="w3-text-primary">
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
</h2>

<?php
$absage_gruende = ["Zu wenig spielberechtigte Ligateams", "Vom Ausrichter im Vorfeld abgesagt", "Spaßturnier"];
include '../../templates/turnier/turnier_absagen.tmp.php';
?>

<p>
    <a href='../liga/turnier_details.php?turnier_id=<?=$turnier->id()?>'><button class="w3-button w3-text-primary w3-border w3-border-primary no">Zu den Turnierdetails</button></a>
    <a href='../ligacenter/lc_turnierliste.php?turnier_id=<?=$turnier->id()?>'><button style='display: inline;' class="w3-button w3-right w3-border w3-border-primary w3-text-primary no">Turniere verwalten (Liste)</button></a>
</p>

<?php
include '../../templates/footer.tmp.php';
