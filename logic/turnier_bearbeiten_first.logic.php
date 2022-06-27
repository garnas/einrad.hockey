<?php
// Turnier und $daten-Array erstellen
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierService;

$teamId = $_SESSION['logins']['team']['id'] ?? 0;
$turnierId = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnierId);

//Existiert das Turnier?
if ($turnier === null){ // TODO CHECKEN
    Helper::not_found("Das Turnier konnte nicht gefunden werden.");
}

// Besteht die Berechtigung das Turnier zu bearbeiten?
if (Helper::$teamcenter && !TurnierService::isAusrichter($turnier, $teamId)){
    Html::error("Keine Berechtigung das Turnier zu bearbeiten");
    Helper::reload('/liga/turniere.php');
}

// Turniere in der Vergangenheit können von Teams nicht mehr verändert werden
if (Helper::$teamcenter && $turnier->getDatum()->getTimestamp() < time()){
    Html::error("Das Turnier liegt bereits in der Vergangenheit und kann nicht bearbeitet werden");
    Helper::reload('/liga/turniere.php');
}