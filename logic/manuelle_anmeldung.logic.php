<?php

use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Form\FormLogicTeam;
use App\Service\Team\TeamService;
use App\Service\Turnier\TurnierService;
use App\Service\Team\NLTeamService;

$turnierId = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnierId);

// Existiert das Turnier?
if (!$turnier) {
    Helper::not_found("Turnier wurde nicht gefunden.");
}

// im Teamcenter testen, ob es sich um den Ausrichter handelt
if (Helper::$teamcenter && ($turnier->getAusrichter()->id() != $_SESSION['logins']['team']['id'] || !$turnier->isSpassTurnier())) {
    Html::error("Fehlende Berechtigung Teams zu diesem Turnier anzumelden");
    Helper::reload('/liga/turniere.php');
}

// Nichtligateam anmelden
if (isset($_POST['nl_anmelden'])) {
    $liste = $_POST['nl_liste'];
    $name = $_POST['nl_teamname'];
    FormLogicTeam::nlTeamAnmelden($turnier, $liste, $name);
}

// Ligaausschuss: Team abmelden
if (isset($_POST['abmelden'])) {
    foreach ($turnier->getListe() as $anmeldung) {
        $team = $anmeldung->getTeam();
        if (isset($_POST['team_abmelden'][$team->id()])) {
            TeamService::abmelden($team, $turnier);
            Html::info($team->getName() . " wurde abgemeldet");
        }
    }
    TurnierRepository::get()->speichern($turnier);
    Helper::reload(get: '?turnier_id=' . $turnier->id());
}

// Ligaauschuss: Ligateam anmelden
if (isset($_POST['team_anmelden'])) {
    $liste = $_POST['liste'];
    $team = TeamRepository::get()->findByName($_POST['teamname']);
    $error = false;

    if (!$team) {
        Html::error("Team wurde nicht gefunden");
        $error = true;
    } elseif (TeamService::isAngemeldet($team, $turnier)) {
        $error = true;
        Html::error("Team ist bereits angemeldet");
    }

    // Ist das Team bereits angemeldet?
    if ($liste === 'setz' && !TurnierService::hasFreieSetzPlaetze($turnier)) {
        $error = true;
        Html::error("Setzliste ist voll.");
    }

    if (!$error) {
        if ($liste === 'warte') {
            TurnierService::addToWarteListe($turnier, $team);
        } elseif ($liste === 'setz') {
            TurnierService::addToSetzListe($turnier, $team);
        }
        TurnierRepository::get()->speichern($turnier);
        Html::info($team->getName() . " wurde angemeldet");
        Helper::reload(get: '?turnier_id=' . $turnier->id());
    }
}

// Ligaausschuss: Team durch ein NL-Team ersetzen
if (isset($_POST['swap_to_nl'])) {

    $team = TeamRepository::get()->team($_POST['team_liste']);
    foreach ($turnier->getListe() as $anmeldung) {
        if ($anmeldung->getTeam()->id() === $team->id()) {
            
            // Das Team wird abgemeldet
            TeamService::abmelden($anmeldung->getTeam(), $turnier);
            Html::info($anmeldung->getTeam()->getName() . " wurde abgemeldet");
            $liste = $anmeldung->getListe();
            
            // Das NL-Team wird angemeldet
            $nlTeam = NLTeamService::findOrCreate($_POST['nl_teamname']);
            TurnierService::nlAnmelden($turnier, $nlTeam, $liste);
            
            TurnierRepository::get()->speichern($turnier);
            Html::info("Der Tausch war erfolgreich.");
        }
    }

    // Aktualisiere den Spielplan falls angefordert
    if (isset($_POST['update_schedule']) && $turnier->getPhase() == 'spielplan') {
        $nlTeam = NLTeamService::findOrCreate($_POST['nl_teamname']);
        $nturnier = nTurnier::get($turnier->id());
        Spielplan::replace_team($nturnier, $team, $nlTeam);
    }
    
    Helper::reload(get: '?turnier_id=' . $turnier->id());
}

// Ligaausschuss: Team durch ein NL-Team ersetzen
if (isset($_POST['swap_to_liga'])) {

    $team_to_replace = TeamRepository::get()->team($_POST['team_liste']);
    $team_replacing = TeamRepository::get()->findByName($_POST['teamname']);
    
    foreach ($turnier->getListe() as $anmeldung) {
        if ($anmeldung->getTeam()->id() === $team_to_replace->id()) {
            
            // Das Team wird abgemeldet
            TeamService::abmelden($anmeldung->getTeam(), $turnier);
            Html::info($anmeldung->getTeam()->getName() . " wurde abgemeldet");
            $liste = $anmeldung->getListe();
            
            // Das Ligateam wird angemeldet
            if ($liste === 'warteliste') {
                TurnierService::addToWarteListe($turnier, $team_replacing);
            } elseif ($liste === 'setzliste') {
                TurnierService::addToSetzListe($turnier, $team_replacing);
            }
            
            TurnierRepository::get()->speichern($turnier);
            Html::info("Der Tausch war erfolgreich.");
        }
    }

    // Aktualisiere den Spielplan falls angefordert
    if (isset($_POST['update_schedule']) && $turnier->getPhase() == 'spielplan') {
        $nturnier = nTurnier::get($turnier->id());
        Spielplan::replace_team($nturnier, $team_to_replace, $team_replacing);
    }
    
    Helper::reload(get: '?turnier_id=' . $turnier->id());
}

// Ligaauschuss: Warteliste neu Durchnummerieren
if (isset($_POST['warteliste_aktualisieren'])) {
    TurnierService::neueWartelistePositionen($turnier);
    TurnierRepository::get()->speichern($turnier);
    Html::info("Warteliste wurde aktualisiert");
    Helper::reload(get: '?turnier_id=' . $turnier->id());
}

// Ligaauschuss: Setzliste von der Warteliste neu auffuellen
if (isset($_POST['setzliste_auffuellen'])) {
    TurnierService::setzListeAuffuellen($turnier);
    TurnierRepository::get()->speichern($turnier);
    Html::info("Warteliste wurde aktualisiert");
    Helper::reload(get: '?turnier_id=' . $turnier->id());
}
