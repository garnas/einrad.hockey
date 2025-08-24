<?php
// Turnierobjekt erstellen
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Team\TeamService;
use App\Service\Turnier\TurnierService;

$turnier_id = (int)@$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnier_id);
if (!$turnier) {
    Helper::not_found("Turnier wurde nicht gefunden.");
}

$team_id = $_SESSION['logins']['team']['id'] ?? 0;
$team = TeamRepository::get()->team($team_id);


// Berechtigung zum Verändern des Reports
$change_tbericht = (TurnierService::isAusrichter($turnier, $team_id) || Helper::$ligacenter);
$read_tbericht =
    Helper::$ligacenter
    || $team && TeamService::isAufSetzliste(team: $team, turnier: $turnier)
    || TurnierService::isAusrichter($turnier, $team_id);

// Berechtigung zum Verändern des Reports widerrufen für Ausrichter, wenn das Turnier mehr als drei Tage zurückliegt.
$turnier_datum = DateTimeImmutable::createFromMutable($turnier->getDatum());
if (
    !Helper::$ligacenter
    && $turnier_datum->modify('-2 days') > (new DateTime())
) {
    $change_tbericht = false;
    if (TurnierService::isAusrichter($turnier, $team_id)) {
        Html::notice("Das Turnier liegt bereits in der Vergangenheit. Bearbeiten des Turnierreports nur noch via den Ligaausschuss möglich.");
    }
}

$tbericht = new TurnierReport ($turnier_id);

// Gibt es eine Leseberechtigung?
if (!$read_tbericht) 
{
    Html::error("Der Turnierreport kann nur von teilnehmenden Teams eingesehen werden.");
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
}

// Ist es ein Spass-Turnier?
if ($turnier->isSpassTurnier()) {
    Html::notice("Spaßturniere erfordern keinen Turnierreport.");
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
}

$kader_array = [];
$teams = [];
$setzliste = TurnierService::getSetzListe($turnier);
foreach ($setzliste as $setz) {
    $teams[$setz->getTeam()->id()] = $setz->getTeam();
    $kader_array[$setz->getTeam()->id()] = $setz->getTeam()->getKaderAktuell();
}

$ausbilder_liste = [];
$spieler_liste = [];

$alle_ausbilder = LigaLeitung::get_all('schiriausbilder');
foreach ($kader_array as $team_id => $kader) {
    foreach ($kader as $spieler) {
        if (isset($alle_ausbilder[$spieler->getSpielerId()])) {
            $ausbilder_liste[$spieler->getSpielerId()] = $spieler;
        }
        $spieler_liste[$spieler->getSpielerId()] = $spieler;
    }
}

$spieler_ausleihen = $tbericht->get_spieler_ausleihen();
$zeitstrafen = $tbericht->get_zeitstrafen();

if ($change_tbericht) {

    // Spielerausleihe löschen
    foreach ($spieler_ausleihen as $ausleihe_id => $ausleihe) {
        if (isset($_POST[('del_ausleihe_' . $ausleihe_id)])) { //TODO Array bauen
            $tbericht->delete_spieler_ausleihe($ausleihe_id);
            Html::info("Spielerausleihe wurde entfernt.");
            Helper::reload(get: '?turnier_id=' . $turnier->id());
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
            Helper::reload(get: "?turnier_id=" . $turnier->id());
        }
        $tbericht->set_spieler_ausleihe($name, $team_auf, $team_ab);
        Html::info("Spielerausleihe wurde hinzugefügt.");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }

    // Zeitstrafe löschen
    foreach ($zeitstrafen as $zeitstrafe_id => $zeitstrafe) {
        if (isset($_POST[('del_zeitstrafe_' . $zeitstrafe_id)])) {
            $tbericht->delete_zeitstrafe($zeitstrafe_id);
            Html::info("Zeitstrafe wurde entfernt.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());
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
        Helper::reload(get: "?turnier_id=" . $turnier->id());
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
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }
}





