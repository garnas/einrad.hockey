<?php
$challenge = new Challenge();

// Einträge für das Teamcenter-Dashboard. Diese werden nur "gefüllt", wenn eine TC-Session vorhanden ist.
if(isset($_SESSION['team_id'])) {
    $team_id = $_SESSION["team_id"];
    $kader = Spieler::get_teamkader($team_id);
    $team_spielerliste = $challenge->get_team_spieler($team_id);
    $team_eintraege = $challenge->get_team_eintraege($team_id); 
}

// Einträge für das Dashboard in auf der Public-Seite
$teamliste = $challenge->get_teams();
$alle_spielerliste = $challenge->get_spieler();
$alle_eintraege = $challenge->get_eintraege();
$jung = $challenge->get_alter_jung();
$alt = $challenge->get_alter_alt();
$einradhockey = $challenge->get_einradhockey_rad();
$start = date("Y-m-d", strtotime($challenge->challenge_start));
$end = date("Y-m-d", strtotime($challenge->challenge_end));

// Überprüfung, ob ein neuer Eintrag plausibel / vollständig ist
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
            header('Location: tc_challenge_eintraege.php');//Setzt den Html-Header zu einer direkten Weiterleitung, somit wird die Seite neu geladen mit den aktuellen Daten
            die(); //Trotz gesetzten Header würde das Skript noch zu ende ausgeführt werden. Deswegen wird es hier beendet.
        }
    }

    if ($error) {
        Form::error("Die Strecke konnte nicht eingetragen werden!");
    }

}

// "Löscht" den ausgewählten Eintrag
if (isset($_POST['update_challenge'])) {
    $eintrag = $_POST["eintrag_id"];

    Challenge::update_data($eintrag);
    
    header('Location: tc_challenge_eintraege.php');
    die();
}

// Breiten für die ProgressBar
$stand = $challenge->get_stand();
$value = $stand['kilometer'];
$values = [$value, 3500.0, 6400.0, 11000.0, 16098.4];
sort($values);

if ($values[0] == $value) {
    $class = ['w3-tertiary', '', '', '', ''];
    $text = ['Stand', 'Distanz 1', 'Distanz 2', 'Distanz 3', 'Sydney'];
} elseif ($values[1] == $value) {
    $class = ['w3-tertiary', 'w3-tertiary', '', '', ''];
    $text = ['Distanz 1', 'Stand', 'Distanz 2', 'Distanz 3', 'Sydney'];
} elseif ($values[2] == $value) {
    $class = ['w3-tertiary', 'w3-tertiary', 'w3-tertiary', '', ''];
    $text = ['Distanz 1', 'Distanz 2', 'Stand', 'Distanz 3', 'Sydney'];   
} elseif ($values[3] == $value) {
    $class = ['w3-tertiary', 'w3-tertiary', 'w3-tertiary', 'w3-tertiary', ''];
    $text = ['Distanz 1', 'Distanz 2', 'Distanz 3', 'Stand', 'Sydney'];     
} else {
    $class = ['w3-tertiary', 'w3-tertiary', 'w3-tertiary', 'w3-tertiary', 'w3-tertiary'];
    $text = ['Distanz 1', 'Distanz 2', 'Distanz 3', 'Sydney', 'Stand'];  
}

$width_1 = round($values[0] / $values[4] * 100);
$width_2 = round($values[1] / $values[4] * 100) - $width_1;
$width_3 = round($values[2] / $values[4] * 100) - $width_1 - $width_2;
$width_4 = round($values[3] / $values[4] * 100) - $width_1 - $width_2 - $width_3;
$width_5 = 99 - $width_1 - $width_2 - $width_3 - $width_4 + 1;

