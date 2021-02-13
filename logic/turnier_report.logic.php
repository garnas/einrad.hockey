<?php
// Turnierobjekt erstellen
$turnier_id = (int)$_GET['turnier_id'] ?? 0;
$turnier = new Turnier ($turnier_id);

if ($turnier->details['ausrichter'] == ($_SESSION['team_id'] ?? '') or Config::$ligacenter) {
    $change_tbericht = true; // Berechtigung zum Verändern des Reports
} else {
    $change_tbericht = false;
}

if (strtotime($turnier->details['datum']) - time() < -3 * 24 * 60 * 60 && !Config::$ligacenter) {
    $change_tbericht = false; //Berechtigung zum Verändern des Reports widerrufen für Ausrichter, wenn das Turnier mehr als zwei Tage zurückliegt.
    if ($turnier->details['ausrichter'] == ($_SESSION['team_id'] ?? '')) {
        Form::attention("Das Turnier liegt bereits in der Vergangenheit. Bearbeiten des Turnierreports nur noch via den Ligaausschuss möglich.");
    }
}

$tbericht = new TurnierReport ($turnier_id);

// Existiert das Turnier?
if (empty($turnier->details)) {
    Form::error("Turnier wurde nicht gefunden");
    header('Location: ../liga/turniere.php');
    die();
}

if ($turnier->details['art'] == 'spass') {
    Form::attention("Spaßturniere erfordern keinen Turnierreport.");
    header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier->details['turnier_id']);
    die();
}

// Liste der Teams
$teams = $turnier->get_liste_spielplan();
// Kader und Ausbilder
$kader_array = $turnier->get_kader_kontrolle();
$ausbilder_liste = [];
$spieler_liste = [];
foreach ($kader_array as $team_id => $kader) {
    foreach ($kader as $spieler_id => $spieler) {
        if ($spieler['schiri'] == 'Ausbilder/in') {
            $ausbilder_liste[$spieler_id] = $spieler;
        }
        $spieler_liste[$spieler_id] = $spieler;
    }
}


if ($change_tbericht) {

    $spieler_ausleihen = $tbericht->get_spieler_ausleihen();

    // Spielerausleihe löschen
    foreach ($spieler_ausleihen as $ausleihe_id => $ausleihe) {
        if (isset($_POST['del_ausleihe_' . $ausleihe_id])) {
            $tbericht->delete_spieler_ausleihe($ausleihe_id);
            Form::affirm("Spielerausleihe wurde entfernt.");
            header('Location:' . dbi::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
            die();
        }
    }

    // Spielerausleihe hinzufügen
    if (isset($_POST['new_ausleihe'])) {
        $name = $_POST['ausleihe_name'];
        $team_ab = $_POST['ausleihe_team_ab'];
        $team_auf = $_POST['ausleihe_team_auf'];
        $team_id_ab = Team::teamname_to_teamid($team_ab);
        $team_id_auf = Team::teamname_to_teamid($team_auf);
        if (!(Team::is_ligateam($team_id_ab) && Team::is_ligateam($team_id_auf))) {
            Form::error("Ligateams der Spielerausleihe wurden nicht gefunden");
            header('Location:' . dbi::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
            die();
        }
        $tbericht->set_spieler_ausleihe($name, $team_auf, $team_ab);
        Form::affirm("Spielerausleihe wurde hinzugefügt.");
        header('Location:' . dbi::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
        die();
    }

    $zeitstrafen = $tbericht->get_zeitstrafen();

    // Zeitstrafe löschen
    foreach ($zeitstrafen as $zeitstrafe_id => $zeitstrafe) {
        if (isset($_POST['del_zeitstrafe_' . $zeitstrafe_id])) {
            $tbericht->delete_zeitstrafe($zeitstrafe_id);
            Form::affirm("Zeitstrafe wurde entfernt.");
            header('Location:' . dbi::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
            die();
        }
    }

    // Zeitstrafe hinzufügen
    if (isset($_POST['new_zeitstrafe'])) {
        $dauer = $_POST['zeitstrafe_dauer'];
        $name = $_POST['zeitstrafe_spieler'];
        $team_a = $_POST['zeitstrafe_team_a'];
        $team_b = $_POST['zeitstrafe_team_b'];
        $bericht = $_POST['zeitstrafe_bericht'];
        $tbericht->new_zeitstrafe($name, $dauer, $team_a, $team_b, $bericht);
        Form::affirm("Zeitstrafe wurde hinzugefügt.");
        header('Location:' . dbi::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
        die();
    }

    //Turnierbericht
    if (
        isset($_POST['set_turnierbericht'])
        or isset($_POST['kader_check'])
        or isset($_POST['turnierbericht'])
    ) {
        $bericht = $_POST['turnierbericht'];
        $kader_check = $_POST['kader_check'];
        if ($kader_check == "kader_checked") {
            $kader_check = 'Ja';
        } else {
            $kader_check = 'Nein';
        }
        $tbericht->set_turnier_bericht($bericht, $kader_check);
        Form::affirm("Turnierbericht wurde aktualisiert");
        header("Location:" . dbi::escape($_SERVER['PHP_SELF']) . "?turnier_id=$turnier_id");
        die();
    }
}





