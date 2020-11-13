<?php
$challenge = new Challenge();

$teamliste = $challenge->get_teams();
$spielerliste = $challenge->get_spieler();
$start = date("Y-m-d", strtotime($challenge->challenge_start));
$end = date("Y-m-d", strtotime($challenge->challenge_end));

if (isset($_POST['put_challenge'])) {
    $error = false;
    
    $spieler = $_POST["spieler"];
    $distanz = $_POST["kilometer"];
    $datum = $_POST["datum"];
    $radgroesse = $_POST["radgroesse"];

    if ($datum < $start || $datum > $end) {
        $error = true;
        Form::error("Das ausgewählt Datum liegt nicht im Bereich.");
    } elseif ($spieler == 0) {
        $error = true;
        Form::error("Es wurde kein Spieler ausgewählt.");
    }

    if (!$error) {
        if(Challenge::set_data($spieler, $distanz, $radgroesse, $datum)) {
            Form::affirm("Die Strecke wurde erfolgreich eingetragen!");
            header('Location: tc_challenge.php');//Setzt den Html-Header zu einer direkten Weiterleitung, somit wird die Seite neu geladen mit den aktuellen Daten
            die(); //Trotz gesetzten Header würde das Skript noch zu ende ausgeführt werden. Deswegen wird es hier beendet.
        }
    }

    if ($error) {
        Form::error("Die Strecke konnte nicht eingetragen werden!");
    }

}