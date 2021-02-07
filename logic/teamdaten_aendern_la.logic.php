<?php
if (isset($_POST['change_la'])) {
    $error = false;
    $neuer_teamname = $_POST['teamname'];
    $freilose = $_POST['freilose'];
    $passwort = $_POST['passwort'];

    if (
        $neuer_teamname == $team->details['teamname']
        && $passwort == 'Neues Passwort vergeben'
        && $freilose == $team->details['freilose']
    ) {
        Form::error("Es wurden keine Daten verändert");
        $error = true;
    }
    if (
        !empty(Team::teamname_to_teamid($neuer_teamname))
        && $neuer_teamname != htmlspecialchars_decode($team->details['teamname'])
    ) {
        Form::error("Der Teamname existiert bereits.");
        $error = true;
    }
    if (
        empty($neuer_teamname)
        or $freilose < 0
        or empty($passwort)
    ) {
        Form::error("Felder dürfen nicht leer sein");
        $error = true;
    }

    if (!$error) {
        if ($neuer_teamname != htmlspecialchars_decode($team->details['teamname'])) {
            $team->set_teamname($neuer_teamname);
            Form::affirm("Der Teamname wurde geändert");
        }
        if ($freilose != $team->details['freilose']) {
            $team->set_freilose($freilose);
            Form::affirm("Anzahl der Freilose wurde geändert");
        }
        if ($passwort != 'Neues Passwort vergeben') {
            $team->set_passwort($passwort, 'Nein');
            Form::affirm("Passwort wurde geändert");
        }
    }
    header('Location: lc_teamdaten.php?team_id=' . $team->id);
    die();
}
