<?php
//Turnier und $daten-Array erstellen
$turnier_id = (int) $_GET['turnier_id'];
$turnier = new Turnier($turnier_id);

//Existiert das Turnier?
if (empty($turnier->details)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location: ../liga/turniere.php');
    die();
}

//Besteht die Berechtigung das Turnier zu bearbeiten?
if ($teamcenter && ($_SESSION['team_id'] ?? '') != $turnier->details['ausrichter']){
    Form::error("Keine Berechtigung das Turnier zu bearbeiten");
    header('Location: ../liga/turniere.php');
    die();
}

//Turniere in der Vergangenheit können von Teams nicht mehr verändert werden
if ($teamcenter && strtotime($turnier->details['datum']) < Config::time_offset()){
    Form::error("Das Turnier liegt bereits in der Vergangenheit und kann nicht bearbeitet werden");
    header('Location: ../liga/turniere.php');
    die();
}