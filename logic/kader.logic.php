<?php

// Neuer Spieler eintragen
if (isset($_POST['neuer_eintrag'])) {
    $error = false;
    $vorname = $_POST['vorname'];
    $nachname = $_POST['nachname'];
    $jahrgang = $_POST['jahrgang'];
    $geschlecht = $_POST['geschlecht'];

    if (($_POST['dsgvo'] ?? '') != 'zugestimmt') {
        $error = true;
        Form::error("Den Datenschutz-Hiweisen muss zugestimmt werden, um in einem Ligateam spielen zu können");
    }
    if (empty($vorname) or empty($nachname) or empty($jahrgang) or empty($geschlecht)) {
        $error = true;
        Form::error("Bitte Felder ausfüllen");
    }
    if (1900 > $jahrgang or $jahrgang > date('Y', Config::time_offset())) {
        $error = true;
        Form::error("Ungültiger Jahrgang: Bitte als Jahreszahl ausschreiben.");
    }
    //Ist der Zeitraum richtig um Spieler hinzuzufügen?
    if (!Spieler::check_timing() && !$ligacenter) {
        Form::error("Spieler können nur bis zum Ende der Saison hinzugefügt werden.");
        return false;
    }

    if (!$error) {
        //Spieler Eintragen, wenn der Spieler schon existiert wird false zurückgegeben und eine Fehlermeldung
        if (Spieler::create_new_spieler($vorname, $nachname, $jahrgang, $geschlecht, $team_id)) {
            Form::affirm("Der Spieler wurde eingetragen");
            header('Location: ' . db::escape($_SERVER['PHP_SELF']) . '?team_id=' . $team_id);
            die ("Warten..");
        }
    }
}

//Spieler aus der Vorsaison übernehmen
if (isset($_POST['submit_takeover'])) {
    if (($_POST['dsgvo'] ?? '') != 'zugestimmt') {
        Form::error("Den Datenschutz-Hiweisen muss zugestimmt werden, um in einem Ligateam spielen zu können");
    } else {
        foreach (($_POST['takeover'] ?? []) as $spieler_id) {
            if (!empty($kader_vorsaison[$spieler_id])) { // Validation + Schutz gegen Html-Manipulation
                (new Spieler($spieler_id))->set_spieler_detail('letzte_saison', Config::SAISON);
                $changed = true;
            }
        }
        if ($changed ?? false) {
            Form::affirm("Spieler wurden in die neue Saison übernommen");
            header('Location: ' . db::escape($_SERVER['PHP_SELF']) . '?team_id=' . $team_id);
            die ();
        }
    }
}
