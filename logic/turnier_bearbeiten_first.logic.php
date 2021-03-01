<?php
// Turnier und $daten-Array erstellen
$turnier = new Turnier((int) @$_GET['turnier_id']);

//Existiert das Turnier?
if (empty($turnier->details)){
    Handler::not_found("Das Turnier konnte nicht gefunden werden.");
}

//Besteht die Berechtigung das Turnier zu bearbeiten?
if (Config::$teamcenter && ($_SESSION['logins']['team']['id'] ?? 0) != $turnier->details['ausrichter']){
    Form::error("Keine Berechtigung das Turnier zu bearbeiten");
    header('Location: ../liga/turniere.php');
    die();
}

//Turniere in der Vergangenheit können von Teams nicht mehr verändert werden
if (Config::$teamcenter && strtotime($turnier->details['datum']) < time()){
    Form::error("Das Turnier liegt bereits in der Vergangenheit und kann nicht bearbeitet werden");
    header('Location: ../liga/turniere.php');
    die();
}