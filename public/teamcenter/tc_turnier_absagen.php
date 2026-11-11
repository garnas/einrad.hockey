<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Turnier\Service\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/turnier_bearbeiten_first.logic.php'; //Turnier laden + Ausrichter prüfen + Existiert das Turnier?
require_once '../../logic/turnier_absagen.logic.php'; // Nur Spaßturniere

$absage_gruende = ["Zu wenige Anmeldungen", "Halle nicht verfügbar", "Vom Ausrichter abgesagt"];

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Turnier absagen</h1>

<h2 class="w3-text-primary">
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
</h2>

<?php include '../../templates/turnier/turnier_absagen.tmp.php'; ?>

<p>
    <a href='../liga/turnier_details.php?turnier_id=<?= $turnier->id() ?>'><button class="w3-button w3-text-primary w3-border w3-border-primary no">Zu den Turnierdetails</button></a>
    <a href='../teamcenter/tc_turnierliste_verwalten.php'><button style='display: inline;' class="w3-button w3-right w3-border w3-border-primary w3-text-primary no">Eigene Turniere (Liste)</button></a>
</p>

<?php
include '../../templates/footer.tmp.php';
