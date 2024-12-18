<?php
// Turnierobjekt erstellen
$turnier_id = (int)@$_GET['turnier_id'];
$turnier = nTurnier::get($turnier_id);

// Berechtigung zum Verändern des Reports
$team_id = $_SESSION['logins']['team']['id'] ?? 0;
$change_tbericht = ($turnier->is_ausrichter($team_id) || Helper::$ligacenter);
$read_tbericht = Helper::$ligacenter || array_key_exists($team_id, $turnier->get_spielenliste()) || $turnier->is_ausrichter($team_id);

// Berechtigung zum Verändern des Reports widerrufen für Ausrichter, wenn das Turnier mehr als zwei Tage zurückliegt.
if (
    strtotime($turnier->get_datum()) - time() < -3 * 24 * 60 * 60
    && !Helper::$ligacenter
) {
    $change_tbericht = false;
    if ($turnier->is_ausrichter($team_id)) {
        Html::notice("Das Turnier liegt bereits in der Vergangenheit. Bearbeiten des Turnierreports nur noch via den Ligaausschuss möglich.");
    }
}

$tbericht = new TurnierReport ($turnier_id);

// Existiert das Turnier?
if (empty($turnier->get_turnier_id())) {
    Html::error("Turnier wurde nicht gefunden");
    Helper::reload('/liga/turniere.php');
}

// Gibt es eine Leseberechtigung?
if (!$read_tbericht) 
{
    Html::error("Der Turnierreport kann nur von teilnehmenden Teams eingesehen werden.");
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->get_turnier_id());
}

// Ist es ein Spass-Turnier?
if ($turnier->get_art() === 'spass') {
    Html::notice("Spaßturniere erfordern keinen Turnierreport.");
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->get_turnier_id());
}

// Liste der Teams
$teams = $turnier->get_spielenliste();
// Kader und Ausbilder
$kader_array = $turnier->get_kader();
$ausbilder_liste = [];
$spieler_liste = [];

// Todo In Funktion
$alle_ausbilder = LigaLeitung::get_all('schiriausbilder');
foreach ($kader_array as $team_id => $kader) {
    foreach ($kader as $spieler) {
        if (isset($alle_ausbilder[$spieler->id()])) {
            $ausbilder_liste[$spieler->id()] = $spieler;
        }
        $spieler_liste[$spieler->id()] = $spieler;
    }
}

$spieler_ausleihen = $tbericht->get_spieler_ausleihen();

if ($change_tbericht) {

    // Spielerausleihe löschen
    foreach ($spieler_ausleihen as $ausleihe_id => $ausleihe) {
        if (isset($_POST[('del_ausleihe_' . $ausleihe_id)])) { //TODO Array bauen
            $tbericht->delete_spieler_ausleihe($ausleihe_id);
            Html::info("Spielerausleihe wurde entfernt.");
            header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier->get_turnier_id());
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
            header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier->get_turnier_id());
            die();
        }
        $tbericht->set_spieler_ausleihe($name, $team_auf, $team_ab);
        Html::info("Spielerausleihe wurde hinzugefügt.");
        header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier->get_turnier_id());
        die();
    }

    $zeitstrafen = $tbericht->get_zeitstrafen();

    // Zeitstrafe löschen
    foreach ($zeitstrafen as $zeitstrafe_id => $zeitstrafe) {
        if (isset($_POST[('del_zeitstrafe_' . $zeitstrafe_id)])) {
            $tbericht->delete_zeitstrafe($zeitstrafe_id);
            Html::info("Zeitstrafe wurde entfernt.");
            header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier->get_turnier_id());
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
        header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier->get_turnier_id());
        die();
    }

    //Turnierbericht
    if (
        isset($_POST['set_turnierbericht'])
        || isset($_POST['kader_check'])
        || isset($_POST['turnierbericht'])
    ) {
        $bericht = $_POST['turnierbericht'];
        $kader_check = $_POST['kader_check'];
        $tbericht->set_turnier_bericht($bericht, $kader_check);
        Html::info("Turnierbericht wurde aktualisiert");
        header("Location:" . db::escape($_SERVER['PHP_SELF']) . "?turnier_id=" . $turnier->get_turnier_id());
        die();
    }
}





