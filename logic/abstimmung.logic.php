<?php
$abstimmung = new Abstimmung();

$uhrzeit = strtotime(date('Y-m-d H:i:s'));
$abschluss = strtotime($abstimmung->ende_der_abstimmung);
$beginn = strtotime($abstimmung->beginn_der_abstimmung);


if(isset($_SESSION['la_id'])) {
    $ergebnisse = $abstimmung->get_ergebnisse();
    $abgegebene_stimmen = $ergebnisse['gesamt'];
    $tabelle = array(
        "Winterpause" => array(
            "antwort" => "Winterpause",
            "stimmen" => $ergebnisse['winterpause'] ?? 0,
            "prozent" => isset($ergebnisse['wintepauser']) && $ergebnisse['winterpause'] != 0 ? round($ergebnisse['winterpauser'] / $abgegebene_stimmen * 100) . '%' : '-'
        ),
        "Sommerpause" => array(
            "antwort" => "Sommerpause",
            "stimmen" => $ergebnisse['sommerpause'] ?? 0,
            "prozent" => isset($ergebnisse['sommerpause']) && $ergebnisse['sommerpause'] != 0 ? round($ergebnisse['sommerpause'] / $abgegebene_stimmen * 100) . '%' : '-'
        ),
        "Enthaltung" => array(
            "antwort" => "Enthaltung",
            "stimmen" => $ergebnisse['enthaltung'] ?? 0,
            "prozent" => isset($ergebnisse['enthaltung']) && $ergebnisse['enthaltung'] != 0 ? round($ergebnisse['enthaltung'] / $abgegebene_stimmen * 100) . '%' : '-'
        ),
        "Gesamt" => array(
            "antwort" => "Gesamt",
            "stimmen" => $ergebnisse['gesamt'] ?? 0,
            "prozent" => ($ergebnisse['gesamt']) != 0 ? round($ergebnisse['gesamt'] / $abgegebene_stimmen * 100) . '%' : '-'
        )
    );
}

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