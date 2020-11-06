<?php
$challenge = new Challenge;

$teamliste = $challenge->get_teams();
$spielerliste = $challenge->get_spieler();



if (isset($_POST['put_challenge'])) {
    $error = false;
    
    $spieler = $_POST["spieler"];
    $distanz = $_POST["kilometer"];
    $datum = $_POST["datum"];

    if (!$error) {
        if(Challenge::set_data($spieler, $distanz, $datum)) {
            Form::affirm("Die Strecke wurde erfolgreich eingetragen!");
        }
    }

    if ($error) {
        Form::error("Die Strecke konnte nicht eingetragen werden!");
    }

}