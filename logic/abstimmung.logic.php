<?php
$abstimmung = new Abstimmung();

$uhrzeit = strtotime(date('Y-m-d H:i:s'));
$abschluss = strtotime($abstimmung->ende_der_abstimmung);
$beginn = strtotime($abstimmung->beginn_der_abstimmung);

$ergebnisse = $abstimmung->get_ergebnisse();
$abgegebene_stimmen = $ergebnisse['gesamt'];

$tabelle = array(
    "Winterpause" => array(
        "antwort" => "Winterpause",
        "stimmen" => $ergebnisse['winterpause'] ?? 0,
        "prozent" => round($ergebnisse['winterpause'] / $abgegebene_stimmen * 100, 2) . '%'
    ),
    "Sommerpause" => array(
        "antwort" => "Sommerpause",
        "stimmen" => $ergebnisse['sommerpause'] ?? 0,
        "prozent" => round($ergebnisse['sommerpause'] / $abgegebene_stimmen * 100, 2) . '%'
    ),
    "Enthaltung" => array(
        "antwort" => "Enthaltung",
        "stimmen" => $ergebnisse['enthaltung'] ?? 0,
        "prozent" => round($ergebnisse['enthaltung'] / $abgegebene_stimmen * 100, 2) . '%'
    ),
    "Gesamt" => array(
        "antwort" => "Gesamt",
        "stimmen" => $ergebnisse['gesamt'] ?? 0,
        "prozent" => round($ergebnisse['gesamt'] / $abgegebene_stimmen * 100, 2) . '%'
    )
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