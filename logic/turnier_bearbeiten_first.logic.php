<?php
// Turnier und $daten-Array erstellen
$team_id = $_SESSION['logins']['team']['id'] ?? 0;
$turnier = nTurnier::get((int) @$_GET['turnier_id']);

//Existiert das Turnier?
if (empty($turnier->get_turnier_id())){
    Helper::not_found("Das Turnier konnte nicht gefunden werden.");
}

//Besteht die Berechtigung das Turnier zu bearbeiten?
if (Helper::$teamcenter && !$turnier->is_ausrichter($team_id)){
    Html::error("Keine Berechtigung das Turnier zu bearbeiten");
    header('Location: ../liga/turniere.php');
    die();
}

//Turniere in der Vergangenheit können von Teams nicht mehr verändert werden
if (Helper::$teamcenter && strtotime($turnier->get_datum()) < time()){
    Html::error("Das Turnier liegt bereits in der Vergangenheit und kann nicht bearbeitet werden");
    header('Location: ../liga/turniere.php');
    die();
}