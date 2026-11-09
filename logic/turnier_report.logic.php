<?php

// Turnierobjekt erstellen
use App\Turnierbericht\Entity\Spielerausleihe;
use App\Turnierbericht\Entity\Zeitstrafe;
use App\Team\Repository\TeamRepository;
use App\Turnier\Repository\TurnierRepository;
use App\Turnierbericht\Repository\TurnierberichtRepository;
use App\Turnier\Service\TurnierService;
use App\Turnierbericht\Service\TurnierberichtPermission;
use App\Turnierbericht\Repository\SpielerausleiheRepository;
use App\Turnierbericht\Repository\ZeitstrafeRepository;
use App\Turnierbericht\Service\TurnierberichtValidator;
use App\Turnierbericht\Service\SpielerausleiheService;
use App\Spieler\Repository\SpielerRepository;

$turnier_id = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnier_id);
$turnier_bericht = TurnierberichtRepository::get()->bericht($turnier_id);

$team_id = $_SESSION['logins']['team']['id'] ?? 0;
$team = TeamRepository::get()->team($team_id);

if (!$turnier) {
    Helper::not_found("Turnier wurde nicht gefunden.");
}

if (!$turnier_bericht) {
    Helper::not_found("Turnierbericht wurde nicht gefunden.");
}

if (!$team && !Helper::$ligacenter) {
    Helper::not_found("Team wurde nicht gefunden.");
}

if ($turnier->isSpassturnier()) {
    Html::notice("Spaßturniere erfordern keinen Turnierreport.");
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
}

if (!TurnierberichtPermission::canRead()) {
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
}

$setzliste = TurnierService::getSetzListe($turnier);
$spieler_ausleihen = $turnier->getLeihen();
$spieler_zeitstrafen = $turnier->getZeitstrafen();
$leihbare_spieler = SpielerausleiheService::getLeihbareSpielerNachTeam($turnier);

$allow_edit = TurnierberichtPermission::canEdit($turnier_bericht);
if ($allow_edit) {

    // Spielerausleihe löschen
    foreach ($spieler_ausleihen as $ausleihe_id => $ausleihe) {
        if (isset($_POST[('del_ausleihe_' . $ausleihe_id)])) {
            SpielerausleiheRepository::get()->delete($ausleihe);
            Html::info("Spielerausleihe wurde entfernt.");
            Helper::reload(get: '?turnier_id=' . $turnier->id());
        }
    }

    // Spielerausleihe hinzufügen
    if (isset($_POST['new_ausleihe'])) {
        $team_auf = TeamRepository::get()->team((int) ($_POST['ausleihe_team_auf'] ?? 0));
        $spieler_auswahl = $_POST['ausleihe_spieler'] ?? '';

        if ($team_auf === null) {
            Html::error("Das aufnehmende Team wurde nicht gefunden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());
        }

        if (!ctype_digit((string) $spieler_auswahl)) {
            // Kein Treffer in der Liste: nicht gemeldete Person, nur für Nichtligateams
            if ($team_auf->isLigaTeam()) {
                Html::error("Bitte eine Spieler:in aus der Liste auswählen.");
                Helper::reload(get: "?turnier_id=" . $turnier->id());
            }
            $name = trim($_POST['ausleihe_name'] ?? '');
            $fehler = SpielerausleiheService::pruefeFreieAusleihe($turnier, $team_auf, $name);
            $team_ab_name = $team_auf->getName();
        } else {
            $spieler = SpielerRepository::get()->spieler((int) $spieler_auswahl);
            if ($spieler === null) {
                Html::error("Die Spieler:in wurde nicht gefunden.");
                Helper::reload(get: "?turnier_id=" . $turnier->id());
            }
            $fehler = SpielerausleiheService::pruefeAusleihe($turnier, $team_auf, $spieler);
            $name = $spieler->getName(fullName: false);
            $team_ab_name = $spieler->getTeam()?->getName() ?? '';
        }

        if ($fehler !== null) {
            Html::error($fehler);
            Helper::reload(get: "?turnier_id=" . $turnier->id());
        }

        $ausleihe = new Spielerausleihe($turnier);
        $ausleihe
            ->setSpieler($name)
            ->setTeamAuf($team_auf->getName())
            ->setTeamAb($team_ab_name);

        SpielerausleiheRepository::get()->speichern($ausleihe);
        Html::info("Spielerausleihe wurde hinzugefügt.");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }

    // Zeitstrafe löschen
    foreach ($spieler_zeitstrafen as $zeitstrafe_id => $zeitstrafe) {
        if (isset($_POST[('del_zeitstrafe_' . $zeitstrafe_id)])) {
            ZeitstrafeRepository::get()->delete($zeitstrafe);
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

        if (!TurnierberichtValidator::validTeam($team_a)) {
            Html::error("Das erstgenannte Team in der Spielpaarung wurde nicht gefunden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());
        }

        if (!TurnierberichtValidator::validTeam($team_b)) {
            Html::error("Das zweitgenannte Team in der Spielpaarung wurde nicht gefunden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());
        }

        $strafe = new Zeitstrafe($turnier);
        $strafe
            ->setTeamA($team_a)
            ->setTeamB($team_b)
            ->setSpieler($name)
            ->setGrund($bericht)
            ->setDauer($dauer);

        ZeitstrafeRepository::get()->speichern($strafe);
        Html::info("Zeitstrafe wurde hinzugefügt.");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }

    // Kadercheck hinzufügen
    if (isset($_POST['set_kader_check'])) {
        $kader_check = $_POST['kader_check'] ?? false;
        $kader_check = $kader_check ? 'Ja' : 'Nein';

        if (!TurnierberichtValidator::validKaderCheck($kader_check)) {
            Html::error("Der Kader-Check konnte nicht aktualisiert werden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());
        }

        $turnier_bericht->setKaderUeberprueft($kader_check);
        TurnierberichtRepository::get()->speichern($turnier_bericht);
        Html::info("Turnierbericht wurde aktualisiert");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }

    // Turnierbericht
    if (isset($_POST['set_turnierbericht']) || isset($_POST['turnierbericht'])) {
        $bericht = $_POST['turnierbericht'];

        $turnier_bericht->setBericht($bericht);
        TurnierberichtRepository::get()->speichern($turnier_bericht);
        Html::info("Turnierbericht wurde aktualisiert");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }
}
