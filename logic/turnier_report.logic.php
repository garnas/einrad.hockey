<?php

// Turnierobjekt erstellen
use App\Entity\TurnierBericht\SpielerAusleihe;
use App\Entity\TurnierBericht\SpielerZeitstrafe;
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Repository\TurnierBericht\TurnierBerichtRepository;
use App\Service\Turnier\TurnierService;
use App\Service\TurnierBericht\PermissionService;
use App\Repository\TurnierBericht\SpielerAusleiheRepository;
use App\Repository\TurnierBericht\SpielerZeitstrafeRepository;
use App\Service\TurnierBericht\TurnierBerichtValidatorService;

$turnier_id = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnier_id);
$turnier_bericht = TurnierBerichtRepository::get()->bericht($turnier_id);

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

if ($turnier->isSpassTurnier()) {
    Html::notice("Spaßturniere erfordern keinen Turnierreport.");
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
}

if (!PermissionService::canRead()) {
    Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
}

$setzliste = TurnierService::getSetzListe($turnier);
$spieler_ausleihen = $turnier->getLeihen();
$spieler_zeitstrafen = $turnier->getZeitstrafen();

$allow_edit = PermissionService::canEdit($turnier_bericht);
if ($allow_edit) {

    // Spielerausleihe löschen
    foreach ($spieler_ausleihen as $ausleihe_id => $ausleihe) {
        if (isset($_POST[('del_ausleihe_' . $ausleihe_id)])) {
            SpielerAusleiheRepository::get()->delete($ausleihe);
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

        if (!TurnierBerichtValidatorService::validTeam($team_auf)) {
            Html::error("Das aufnehmende Team wurde nicht gefunden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());
        }
            
        if (!TurnierBerichtValidatorService::validTeam($team_ab)) {
            Html::error("Das abgebende Team wurde nicht gefunden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());
        }
        
        $ausleihe = new SpielerAusleihe($turnier);
        $ausleihe
            ->setSpieler($name)
            ->setTeamAuf($team_auf)
            ->setTeamAb($team_ab);

        SpielerAusleiheRepository::get()->speichern($ausleihe);
        Html::info("Spielerausleihe wurde hinzugefügt.");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }

    // Zeitstrafe löschen
    foreach ($spieler_zeitstrafen as $zeitstrafe_id => $zeitstrafe) {
        if (isset($_POST[('del_zeitstrafe_' . $zeitstrafe_id)])) {
            SpielerZeitstrafeRepository::get()->delete($zeitstrafe);
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
        
        if (!TurnierBerichtValidatorService::validTeam($team_a)) {
            Html::error("Das erstgenannte Team in der Spielpaarung wurde nicht gefunden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());            
        }
        
        if (!TurnierBerichtValidatorService::validTeam($team_b)) {
            Html::error("Das zweitgenannte Team in der Spielpaarung wurde nicht gefunden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());            
        }
        
        $strafe = new SpielerZeitstrafe($turnier);
        $strafe
            ->setTeamA($team_a)
            ->setTeamB($team_b)
            ->setSpieler($name)
            ->setGrund($bericht)
            ->setDauer($dauer);
        
        SpielerZeitstrafeRepository::get()->speichern($strafe);
        Html::info("Zeitstrafe wurde hinzugefügt.");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }

    // Kadercheck hinzufügen
    if (isset($_POST['set_kader_check'])) {
        $kader_check = $_POST['kader_check'] ?? false;
        $kader_check = $kader_check ? 'Ja' : 'Nein';

        if (!TurnierBerichtValidatorService::validKaderCheck($kader_check)) {
            Html::error("Der Kader-Check konnte nicht aktualisiert werden.");
            Helper::reload(get: "?turnier_id=" . $turnier->id());                    
        }
        
        $turnier_bericht->setKaderUeberprueft($kader_check);
        TurnierBerichtRepository::get()->speichern($turnier_bericht);
        Html::info("Turnierbericht wurde aktualisiert");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }
    
    // Turnierbericht
    if (isset($_POST['set_turnierbericht']) || isset($_POST['turnierbericht'])) {
        $bericht = $_POST['turnierbericht'];

        $turnier_bericht->setBericht($bericht);
        TurnierBerichtRepository::get()->speichern($turnier_bericht);
        Html::info("Turnierbericht wurde aktualisiert");
        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }
}
