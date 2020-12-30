<?php
$abstimmung = new Abstimmung();

$uhrzeit = strtotime(date('Y-m-d H:i:s'));
$abschluss = strtotime($abstimmung->ende_der_abstimmung);
$beginn = strtotime($abstimmung->beginn_der_abstimmung);

// Berechnungen für das Ligacenter
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

// Berechnungen für das Teamcenter
if(isset($_SESSION['team_id'])) {

    // Variablen für Verschlüsselung
    $teamname = $daten['teamname'];
    $key = $daten['passwort'];
    $iv = $daten['team_id'];
    $crypt = $abstimmung->get_crypt($key, $teamname, $iv);

    $stimme_check = $abstimmung->get_team($crypt);
    $vorauswahl['sommerpause'] = False;
    $vorauswahl['winterpause'] = False;
    $vorauswahl['enthaltung'] = False;
    if (isset($stimme_check)) {
        $vorauswahl[$stimme_check['value']] = True;
    }

    // Formularpfrüfung für eine abgegebene Stimme
    if(isset($_POST['abstimmung'])) {
        $value = $_POST['abstimmung'];
        
        if(empty($stimme_check)) {
            $stimme = Abstimmung::add_stimme($value, $crypt);
        } else {
            $stimme = Abstimmung::update_stimme($value, $crypt);
        }
        

        if ($stimme) {
            Form::affirm("Die Stimme wurde erfolgreich abgegeben!");
        } else {
            Form::error("Die Stimme konnte nicht eingetragen werden.");
        }

        header('Location: tc_abstimmung.php');
        die();
    }
}