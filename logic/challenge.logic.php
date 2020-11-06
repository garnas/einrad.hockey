<?php
$challenge = new Challenge;

$teamliste = $challenge->get_teams();
$spielerliste = $challenge->get_spieler();



if (isset($_POST['put_challenge'])) {
    $error = false;
    
    $spieler = $_POST["spieler"];
    $distanz = $_POST["kilometer"];
    $datum = $_POST["datum"];

    if ($datum<"2020-11-01") {
        $error = true;
        Form::error("Das ausgewählt Datum liegt vor dem Beginn.");
    } elseif ($spieler == 0) {
        $error = true;
        Form::error("Es wurde kein Spieler ausgewählt.");
    }

    if (!$error) {
        if(Challenge::set_data($spieler, $distanz, $datum)) {
            Form::affirm("Die Strecke wurde erfolgreich eingetragen!");
        }
    }

    if ($error) {
        Form::error("Die Strecke konnte nicht eingetragen werden!");
    }

}