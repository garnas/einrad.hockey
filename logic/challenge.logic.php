<?php
$challenge = new Challenge();

// Einträge für das Teamcenter-Dashboard. Diese werden nur "gefüllt", wenn eine TC-Session vorhanden ist.
if(isset($_SESSION['team_id'])) {
    $team_id = $_SESSION["team_id"];
    $kader = Spieler::get_teamkader($team_id);
    $team_spielerliste = $challenge->get_team_spieler($team_id);
    $team_eintraege = $challenge->get_team_eintraege($team_id); 
}

// Einträge für das Dashboard auf der Public-Seite
$teamliste = $challenge->get_teams();
$alle_spielerliste = $challenge->get_spieler();
$alle_eintraege = $challenge->get_eintraege();
$jung = $challenge->get_alter_jung();
$alt = $challenge->get_alter_alt();
$einradhockey = $challenge->get_einradhockey_rad();
$start = date("Y-m-d", strtotime($challenge->challenge_start));
$end = date("Y-m-d", strtotime($challenge->challenge_end));

foreach ($teamliste as $key => $team){
    //Platz teilen, bei gleichem Kilometerstand
    if($team['kilometer'] == ($teamliste[$key - 1]['kilometer'] ?? 0)){
        $teamliste[$key]['platz'] = $teamliste[$key - 1]['platz'];
    }
}

foreach ($alle_spielerliste as $key => $spieler){
    // Ab zweiten Vornamen abkürzen mit erster Buchstabe und .
    $vorname_array = explode(' ', $spieler['vorname']);
    if (isset($vorname_array[1])){
        $alle_spielerliste[$key]['vorname'] = $vorname_array[0] . ' ' . $vorname_array[1][0] . '.';
    }
    // Platz teilen, bei gleichem Kilometerstand
    if($spieler['kilometer'] == ($alle_spielerliste[$key - 1]['kilometer'] ?? 0)){
        $alle_spielerliste[$key]['platz'] = $alle_spielerliste[$key - 1]['platz'] ?? '';
    }
}
// Überprüfung, ob ein neuer Eintrag plausibel / vollständig ist
if (isset($_POST['put_challenge']) && $teamcenter) {
    $error = false;
    $distanz = $_POST["kilometer"];
    $datum = $_POST["datum"];
    $radgroesse = $_POST["radgroesse"];
    $spieler_id = $_POST["spieler"];
    $spieler = new Spieler($spieler_id); //Für Überprüfung der Teamzugehörigkeit
    // Validierung des Datums
    $datum = date("Y-m-d",strtotime($datum)); // Es kam zu seltsamen Datumsformaten, die uns übermittelt worden sind.
    // Datums vergleich, Startdatum, Enddatum, liegt in der Zunkunft 
    if (strtotime($datum) < strtotime($start) || strtotime($datum) > strtotime($end) || strtotime($datum) > strtotime(date('Y-m-d'))) {
        $error = true;
        Form::error("Das ausgewählt Datum liegt nicht im Bereich.");
    } elseif (empty($spieler_id)) {
        $error = true;
        Form::error("Es wurde kein Spieler ausgewählt.");
    } elseif ($spieler->get_spieler_details()['team_id'] !== $_SESSION['team_id']){ //Spielt die übergebene Spieler_id auch für das eingeloggte Team?
        $error = true; 
        Form::error("Eintragen nicht möglich.");
    }

    if (!$error) {
        if(Challenge::set_data($spieler_id, $distanz, $radgroesse, $datum)) {
            Form::affirm("Die Strecke wurde erfolgreich eingetragen!");
            header('Location: tc_challenge_eintraege.php');// Setzt den Html-Header zu einer direkten Weiterleitung, somit wird die Seite neu geladen mit den aktuellen Daten
            die(); // Trotz gesetzten Header würde das Skript noch zu ende ausgeführt werden. Deswegen wird es hier beendet.
        }
    }

    if ($error) {
        Form::error("Die Strecke konnte nicht eingetragen werden!");
    }

}

// "Löscht" den ausgewählten Eintrag
if (isset($_POST['update_challenge']) && $teamcenter) {
    $eintrag_id = $_POST["eintrag_id"];

    // Überprüfung ob die Mittels Post übergebene Eintrag-ID auch wirklich zu diesem Team gehört
    foreach ($team_eintraege as $eintrag){
        if($eintrag['id'] == $eintrag_id){
            Challenge::update_data($eintrag_id);
            Form::affirm('Dein Eintrag wurde erfolgreich entfernt.');
            header('Location: tc_challenge_eintraege.php');
            die();
        }
    }
    Form::error("Eintrag konnte nicht entfernt werden");
}

// Lädt die Urkunde für den ausgewählten Spieler
if (isset($_POST['open_spieler_urkunde']) && $teamcenter) {
    $spieler_id = $_POST['urkunde_spieler_id'];

    header('Location: tc_challenge_spieler_urkunde.php?spieler_id=' . $spieler_id);
    die();

    // Überprüfung, ob die SpielerID auch zum Team gehört
    foreach ($team_spielerliste as $eintrag) {
        	if ($eintrag['spieler_id'] == $spieler_id) {
                header('Location: tc_challenge_spieler_urkunde.php?spieler_id=' . $spieler_id);
                die();
        }
    }
}

// Breite für die ProgressBar
$akt_kilometerstand = $challenge->get_stand();
$percent = round($akt_kilometerstand / $challenge->ziel_kilometer * 100);