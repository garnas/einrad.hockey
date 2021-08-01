<?php
// Turnierobjekt erstellen
$turnier_id = (int)@$_GET['turnier_id'];
$turnier = new Turnier ($turnier_id);

// Berechtigung zum Verändern des Reports
$change_tbericht = (
    $turnier->details['ausrichter'] === ($_SESSION['logins']['team']['id'] ?? '')
    || Helper::$ligacenter
);

// Berechtigung zum Verändern des Reports widerrufen für Ausrichter, wenn das Turnier mehr als zwei Tage zurückliegt.
if (
    strtotime($turnier->details['datum']) - time() < -3 * 24 * 60 * 60
    && !Helper::$ligacenter
) {
    $change_tbericht = false;
    if ($turnier->details['ausrichter'] == ($_SESSION['logins']['team']['id'] ?? '')) {
        Html::notice("Das Turnier liegt bereits in der Vergangenheit. Bearbeiten des Turnierreports nur noch via den Ligaausschuss möglich.");
    }
}

$tbericht = new TurnierReport ($turnier_id);

// Existiert das Turnier?
if (empty($turnier->details)) {
    Html::error("Turnier wurde nicht gefunden");
    Helper::reload('/liga/turniere.php');
}

// Gibt es eine Leseberechtigung?
if (
    !Helper::$ligacenter && $turnier->get_liste($_SESSION['logins']['team']['id']) !== 'spiele'
) {
    Html::error("Der Turnierreport kann nur von teilnehmenden Teams eingesehen werden.");
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id);
}

// Ist es ein Spass-Turnier?
if ($turnier->details['art'] === 'spass') {
    Html::notice("Spaßturniere erfordern keinen Turnierreport.");
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id);
}

// Liste der Teams
$teams = $turnier->get_liste_spielplan();
// Kader und Ausbilder
$kader_array = $turnier->get_kader_kontrolle();
$ausbilder_liste = [];
$spieler_liste = [];
foreach ($kader_array as $team_id => $kader) { // Todo In Funktion
    foreach ($kader as $spieler_id => $spieler) {
        if ($spieler['schiri'] == 'Ausbilder/in') {
            $ausbilder_liste[$spieler_id] = $spieler;
        }
        $spieler_liste[$spieler_id] = $spieler;
    }
}

$spieler_ausleihen = $tbericht->get_spieler_ausleihen();

if ($change_tbericht) {

    // Spielerausleihe löschen
    foreach ($spieler_ausleihen as $ausleihe_id => $ausleihe) {
        if (isset($_POST[('del_ausleihe_' . $ausleihe_id)])) { //TODO Array bauen
            $tbericht->delete_spieler_ausleihe($ausleihe_id);
            Html::info("Spielerausleihe wurde entfernt.");
            header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
            die();
        }
    }

    // Spielerausleihe hinzufügen
    if (isset($_POST['new_ausleihe'])) {
        $name = $_POST['ausleihe_name'];
        $team_ab = $_POST['ausleihe_team_ab'];
        $team_auf = $_POST['ausleihe_team_auf'];
        $team_id_ab = Team::name_to_id($team_ab);
        $team_id_auf = Team::name_to_id($team_auf);
        if (!(Team::is_ligateam($team_id_ab) && Team::is_ligateam($team_id_auf))) {
            Html::error("Ligateams der Spielerausleihe wurden nicht gefunden");
            header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
            die();
        }
        $tbericht->set_spieler_ausleihe($name, $team_auf, $team_ab);
        Html::info("Spielerausleihe wurde hinzugefügt.");
        header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
        die();
    }

    $zeitstrafen = $tbericht->get_zeitstrafen();

    // Zeitstrafe löschen
    foreach ($zeitstrafen as $zeitstrafe_id => $zeitstrafe) {
        if (isset($_POST[('del_zeitstrafe_' . $zeitstrafe_id)])) {
            $tbericht->delete_zeitstrafe($zeitstrafe_id);
            Html::info("Zeitstrafe wurde entfernt.");
            header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
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
        Html::info("Zeitstrafe wurde hinzugefügt.");
        header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
        die();
    }

    //Turnierbericht
    if (
        isset($_POST['set_turnierbericht'])
        || isset($_POST['kader_check'])
        || isset($_POST['turnierbericht'])
    ) {
        $bericht = $_POST['turnierbericht'];
        $kader_check = $_POST['kader_check'] == "kader_checked";
        $tbericht->set_turnier_bericht($bericht, $kader_check);
        Html::info("Turnierbericht wurde aktualisiert");
        header("Location:" . db::escape($_SERVER['PHP_SELF']) . "?turnier_id=$turnier_id");
        die();
    }
}





