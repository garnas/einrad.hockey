<?php

$abschluss = strtotime(Abstimmung::ENDE);
$beginn = strtotime(Abstimmung::BEGINN);

$ergebnisse = Abstimmung::get_ergebnisse();
$abgegebene_stimmen = $ergebnisse['gesamt'];
$anzahl_teams = count(Team::get_liste_ids());
$wahlbeteiligung = round(100 * $abgegebene_stimmen / $anzahl_teams) . '%';

$display_ergebnisse = [
    "winterpause" => [
        "formulierung" =>"Der bisherige Rhythmus mit einer Saison, die sich am <b>Kalenderjahr</b> orientiert, wird beibehalten.",
        "farbe" => "w3-indigo",
        "stimmen" => $ergebnisse['winterpause'] ?? 0,
        "prozent" => (isset($ergebnisse['winterpause']) && $ergebnisse['winterpause'] != 0) ? round($ergebnisse['winterpause'] / $abgegebene_stimmen * 100) . '%' : '0%'
    ],
    "sommerpause" => [
        "formulierung" => "Der Ligaausschuss arbeitet auf einen Saisonrhythmus <b>Sommer-Sommer</b> hin. Der Saisonwechsel würde so in die Sommerferien fallen, mit Abschlussturnieren etwa im Mai/Juni.",
        "farbe" => "w3-green",
        "stimmen" => $ergebnisse['sommerpause'] ?? 0,
        "prozent" => (isset($ergebnisse['sommerpause']) && $ergebnisse['sommerpause'] != 0) ? round($ergebnisse['sommerpause'] / $abgegebene_stimmen * 100) . '%' : '0%'
    ],
    "enthaltung" => [
        "formulierung" => "Wir <b>enthalten</b> uns.",
        "farbe" => "w3-gray",
        "stimmen" => $ergebnisse['enthaltung'] ?? 0,
        "prozent" => (isset($ergebnisse['enthaltung']) && $ergebnisse['enthaltung'] != 0) ? round($ergebnisse['enthaltung'] / $abgegebene_stimmen * 100) . '%' : '0%'
    ]
];

// Höchstes Abstimmungsergebnis oben anzeigen lassen.
$sort_function = function ($value1, $value2) {
    return $value1['stimmen'] < $value2['stimmen'];
};
uasort($display_ergebnisse, $sort_function);

// Nur für das Teamcenter und im Zeitraum der Abstimmung
if ($teamcenter) {
    $abstimmung = new Abstimmung($_SESSION['team_id']);
    $stimme = '';

    // Team will seine Stimme einsehen
    if (isset($_POST['stimme_einsehen'])) {
        if (!password_verify($_POST['passwort'], $abstimmung->passwort_hash)) {
            Form::log("abstimmung.log", "$abstimmung->team_id Ungültiges Passwort (Stimme einsehen)");
            Form::error("Ungültiges Passwort.");
        } else {
            $crypt = $abstimmung->teamid_to_crypt($_POST['passwort']);
            $einsicht = $abstimmung->get_stimme($crypt);
            Form::log("abstimmung.log", "$abstimmung->team_id hat seine Stimme eingesehen");
            // Keinen Header einbauen, da $stimme sonst verloren geht.
        }
    }

    // Team will abstimmen
    if (isset($_POST['abstimmung'])) {
        $error = false;
        $stimme = $_POST['abstimmung'];
        if (time() < $beginn or time() > $abschluss) {
            $error = true;
            Form::log("abstimmung.log", "$abstimmung->team_id Falscher Zeitraum");
            Form::error("Die Abstimmung ist zurzeit nicht aktiv.");
        }
        if (!in_array($stimme, ['winterpause', 'sommerpause', 'enthaltung'])) {
            $error = true;
            Form::log("abstimmung.log", "$abstimmung->team_id HTML-Manipulation");
            Form::error("Ungültige Formularübermittlung");
        }
        if (!password_verify($_POST['passwort'], $abstimmung->passwort_hash)) {
            $error = true;
            Form::log("abstimmung.log", "$abstimmung->team_id Ungültiges Passwort (Abstimmen)");
            Form::error("Ungültiges Passwort.");
        }
        if (!$error) {
            $crypt = $abstimmung->teamid_to_crypt($_POST['passwort']);
            $abstimmung->set_stimme($stimme, $crypt); // Log und Affirm in Funktion
            header('Location: tc_abstimmung.php');
            die();
        }
    }
}