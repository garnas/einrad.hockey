<?php

if (!Helper::$teamcenter) {
    return;
}

$abstimmung = new Abstimmung($_SESSION['logins']['team']['id']);
$beginn = strtotime($abstimmung->get_begin());
$abschluss = strtotime($abstimmung->get_end());

$stimme = '';

// Team will seine Stimme einsehen
if (isset($_POST['stimme_einsehen'])) {
    if (password_verify($_POST['passwort'], $abstimmung->get_password_hash())) {
        $crypt = $abstimmung->teamid_to_crypt($_POST['passwort']);
        $einsicht = $abstimmung->get_stimme($crypt);
        Helper::log("abstimmung.log", $abstimmung->get_team_id() . " hat seine Stimme eingesehen");
    } else {
        Helper::log("abstimmung.log", $abstimmung->get_team_id() . " ungültiges Passwort (Stimme einsehen)");
        Html::error("Ungültiges Passwort.");
    }
}


// Team will abstimmen
if (isset($_POST['abgestimmt'])) {
    
    $error = false;
    $stimme = $_POST;
    unset($stimme["abgestimmt"]);
    unset($stimme["passwort"]);
    $stimme = json_encode($stimme);

    if (time() < $beginn || time() > $abschluss) {
        $error = true;
        Helper::log("abstimmung.log", $abstimmung->get_team_id() . " Falscher Zeitraum");
        Html::error("Die Abstimmung ist zurzeit nicht aktiv.");
    }

    if (!password_verify($_POST['passwort'], $abstimmung->get_password_hash())) {
        $error = true;
        Helper::log("abstimmung.log", $abstimmung->get_team_id() . " Ungültiges Passwort (Abstimmen)");
        Html::error("Ungültiges Passwort.");
    }
    
    if (!$error) {
        $crypt = $abstimmung->teamid_to_crypt($_POST['passwort']);
        $abstimmung->set_stimme($stimme, $crypt); // Log und Affirm in Funktion
        Helper::reload();
    }
}