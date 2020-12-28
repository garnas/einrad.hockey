<?php
$abstimmung = new Abstimmung();

$uhrzeit = strtotime(date('Y-m-d H:i:s'));
$abschluss = strtotime($abstimmung->ende_der_abstimmung);
$beginn = strtotime($abstimmung->beginn_der_abstimmung);

$ergebnisse = $abstimmung->get_ergebnisse();
$abgegebene_stimmen = $ergebnisse['Gesamt'];

$tabelle = array(
    "Winterpause" => $ergebnisse['winterpause'] ?? 0,
    "Sommerpause" => $ergebnisse['sommerpause'] ?? 0,
    "Enthaltung" => $ergebnisse['enthaltung'] ?? 0,
    "Gesamt" => $ergebnisse['Gesamt'] ?? 0
);

if(isset($_SESSION['team_id'])) {
    $team_id = $_SESSION["team_id"];
    $stimme_check = $abstimmung->get_team($team_id);
}

// Formularpfrüfung für eine abgegebene Stimme
if(isset($_POST['abstimmung']) && $teamcenter) {
    $value = $_POST['abstimmung'];
    $stimme = Abstimmung::add_stimme($value);
    $team = Abstimmung::add_team($team_id);

    if ($stimme && $team) {
        Form::affirm("Die Stimme wurde erfolgreich abgegeben!");
    } else {
        Form::error("Die Stimme konnte nicht eingetragen werden.");
    }

    header('Location: tc_abstimmung.php');
    die();
}