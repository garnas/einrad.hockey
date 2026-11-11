<?php

// Turnier und $daten-Array erstellen
use App\Turnier\Repository\TurnierRepository;
use App\Turnier\Service\TurnierPermission;
use App\Turnier\Service\TurnierService;

$teamId = $_SESSION['logins']['team']['id'] ?? 0;
$turnierId = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnierId);

//Existiert das Turnier?
if ($turnier === null) { // TODO CHECKEN
    Helper::not_found("Das Turnier konnte nicht gefunden werden.");
}

// Besteht die Berechtigung das Turnier zu bearbeiten?
if (Helper::$teamcenter && !TurnierService::isAusrichter($turnier, $teamId)) {
    Html::error("Keine Berechtigung das Turnier zu bearbeiten");
    Helper::reload('/liga/turniere.php');
}

// Besteht die Berechtigung das Turnier zu bearbeiten?
if (Helper::$teamcenter && $turnier->isCanceled()) {
    Html::error("Turnier wurde abgesagt.");
    Helper::reload('/teamcenter/tc_turnierliste_verwalten.php');
}

// In der Ergebnisphase können Teams das Turnier nicht mehr verändert werden (nur noch den Turnierreport)
if (Helper::$teamcenter && !TurnierPermission::canChange($turnier)) {
    Html::error("Das Turnier ist bereits in der Ergebnisphase und kann nicht mehr bearbeitet werden");
    Helper::reload('/teamcenter/tc_turnierliste_verwalten.php');
}
