<?php

use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Form\FormLogicTeam;
use App\Service\Team\NLTeamService;
use App\Service\Team\TeamService;
use App\Service\Team\TeamValidator;
use App\Service\Turnier\TurnierService;

$turnierId = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnierId);

// Existiert das Turnier?
if (!$turnier){
    Helper::not_found("Turnier wurde nicht gefunden.");
}

// im Teamcenter testen, ob es sich um den Ausrichter handelt
if (Helper::$teamcenter && ($turnier->getAusrichter()->id() != $_SESSION['logins']['team']['id'] || !$turnier->isSpassTurnier())){
    Html::error("Fehlende Berechtigung Teams zu diesem Turnier anzumelden");
    Helper::reload('/liga/turniere.php');
}

// Team als Ligaausschuss abmelden
if (isset($_POST['abmelden'])){
    foreach ($turnier->getListe() as $anmeldung) {
        $team = $anmeldung->getTeam();
        if (isset($_POST['team_abmelden'][$team->id()])) {
            TeamService::abmelden($team, $turnier);
            if ($turnier->isSetzPhase()) {
                TurnierService::setzListeAuffuellen($turnier);
            }
            TurnierRepository::get()->speichern($turnier);
            Html::info ($team->getName(). " wurde abgemeldet");
            Helper::reload(get: '?turnier_id='. $turnier->id());
        }
    }
    Html::error("Es wurde kein Team abgemeldet.");
}

// Ligateam als Ligaausschuss anmelden
if (isset($_POST['team_anmelden'])){
    $liste = $_POST['liste'];
    $team = TeamRepository::get()->findByName($_POST['teamname']);
    $error = false;

    if (!$team) {
        Html::error("Team wurde nicht gefunden");
        $error = true;
    } elseif (TeamService::isAngemeldet($team, $turnier)){
        $error = true;
        Html::error("Team ist bereits angemeldet");
    }

    // Ist das Team bereits angemeldet?
    if ($liste === 'setz' && !TurnierService::hasFreieSetzPlaetze($turnier)){
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
        Html::info ($team->getName() . " wurde angemeldet");
        Helper::reload(get: '?turnier_id='. $turnier->id());
    }
}

// Nichtligateam anmelden
if (isset($_POST['nl_anmelden'])){
   FormLogicTeam::nlTeamAnmelden($turnier);
}

// Warteliste neu Durchnummerieren
if (isset($_POST['warteliste_aktualisieren'])) {
    TurnierService::neueWartelistePositionen($turnier);
    TurnierRepository::get()->speichern($turnier);
    Html::info("Warteliste wurde aktualisiert");
    Helper::reload(get: '?turnier_id='. $turnier->id());
}

// Setzliste von der Warteliste neu auffuellen
if (isset($_POST['setzliste_auffuellen'])){
    TurnierService::setzListeAuffuellen($turnier);
    TurnierRepository::get()->speichern($turnier);
    Html::info("Warteliste wurde aktualisiert");
    Helper::reload(get: '?turnier_id='. $turnier->id());
}