<?php

if (isset($_POST['change_la']) && Config::$ligacenter) {
    $error = false;
    $neuer_teamname = $_POST['teamname'];
    $freilose = (int) $_POST['freilose'];
    $passwort = $_POST['passwort'];

    if ($neuer_teamname === $team->details['teamname']
        && empty ($passwort)
        && $freilose === $team->details['freilose']
    ) {
        Form::error("Es wurden keine Daten verändert");
        $error = true;
    }
    if (
        !empty(Team::name_to_id($neuer_teamname))
        && $neuer_teamname != htmlspecialchars_decode($team->details['teamname'])
    ) {
        Form::error("Der Teamname existiert bereits.");
        $error = true;
    }
    if (
        empty($neuer_teamname)
        or $freilose < 0
    ) {
        Form::error("Felder dürfen nicht leer sein");
        $error = true;
    }

    if (!$error) {
        if ($neuer_teamname != $team->details['teamname']) {
            $team->set_name($neuer_teamname);
            Form::info("Der Teamname wurde geändert");
        }
        if ($freilose != $team->details['freilose']) {
            $team->set_freilose($freilose);
            Form::info("Anzahl der Freilose wurde geändert");
        }
        if (!empty($passwort)) {
            $team->set_passwort($passwort, 'Nein');
            Form::info("Passwort wurde geändert");
        }
    }
    header('Location: lc_teamdaten_aendern.php?team_id=' . $team->id);
    die();
}
