<?php

use App\Event\Turnier\nLigaBot;
use App\Repository\DoctrineWrapper;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Team\FreilosService;

$turnierEntity = TurnierRepository::get()->turnier($turnier_id);

// Besteht die Berechtigung das Turnier zu bearbeiten?
if(!Helper::$ligacenter){ // Ligacenter darf alles.
    if ((Helper::$teamcenter && ($_SESSION['logins']['team']['id'] ?? 0) != $spielplan->turnier->getAusrichter()->id())){
        Html::error("Nur der Ausrichter kann Spielergebnisse eintragen");
        Helper::reload("/liga/spielplan.php", '?turnier_id=' . $turnier_id);
    }

    // Wird das Turnierergebnis rechtzeitig eingetragen?
    $weekday = $spielplan->turnier->getDatum()->format("N"); // Numerischer Wochentag.
    $time_to_deadline = (8-$weekday) * 24*60*60 + 18*60*60; // Zeit bis zu nächsten Montag 18:00 Uhr in Sekunden.
    $interval = new DateInterval('PT' . $time_to_deadline . 'S');
    $abgabe = $spielplan->turnier->getDatum()->add($delta);

    if ($abgabe < time()){
        Html::error("Bitte wende dich an den Ligaausschuss um Ergebnisse nachträglich zu verändern.");
        Helper::reload("/liga/spielplan.php", '?turnier_id=' . $turnier_id);
    }
}

if (isset($_POST["tore_speichern"])) {
    // Neu eingetragene Tore speichern
    foreach ($spielplan->get_spiele() as $spiel_id => $spiel) {
        if (
            (string) $spiel['tore_a'] === ($_POST["tore_a"][$spiel_id] ?? '')
            && (string) $spiel['tore_b'] === ($_POST["tore_b"][$spiel_id] ?? '')
            && (string) $spiel['penalty_a'] === ($_POST["penalty_a"][$spiel_id] ?? '')
            && (string) $spiel['penalty_b'] === ($_POST["penalty_b"][$spiel_id] ?? '')
        ) {
            continue;
        }
        $spielplan->set_tore(
            $spiel['spiel_id'],
            $_POST["tore_a"][$spiel_id] ?? '',
            $_POST["tore_b"][$spiel_id] ?? '',
            $_POST["penalty_a"][$spiel_id] ?? '',
            $_POST["penalty_b"][$spiel_id] ?? ''
        );
        $spiel['tore_a'] = $_POST["tore_a"][$spiel_id]  ?? '';
        $spiel['tore_b'] = $_POST["tore_b"][$spiel_id]  ?? '';
        $spiel['penalty_a'] = $_POST["penalty_a"][$spiel_id]  ?? '';
        $spiel['penalty_b'] = $_POST["penalty_b"][$spiel_id]  ?? '';
        Discord::tickerUpdate($spiel);
    }

    Html::info("Spielergebnisse wurden gespeichert.");
    Helper::reload(get: "?turnier_id=" . $turnier_id);
}

//Turnierergebnisse speichern
if (isset($_POST["turnierergebnis_speichern"])) {

    // Sind alle Spiele gespielt und kein Penalty offen?
    if (!$spielplan->check_turnier_beendet()) {
        Html::error("Es sind noch Spiel- oder Penaltyergebnisse offen. Turnierergebnisse wurden nicht übermittelt.");
        $error = true;
    }

    // Testen ob Turnier tabellentechnisch eingetragen werden darf.
    if (!$spielplan->is_ergebnis_eintragbar()) {
        Html::error("Turnierergebnis kann nicht eingetragen werden. Kontaktiere bitte den Ligaausschuss.");
        $error = true;
    }

    // Testen ob Zweite Runde Penaltys gespielt werden müssen
    if ($spielplan->is_out_of_scope()) {
        Html::error("Es muss noch eine zweite Runde Penaltys gespielt werden.");
        $error = true;
    }

    if (!($error ?? false)) {
        $spielplan->turnier->setErgebnis($spielplan->platzierungstabelle);
        Html::info("Das Turnierergebnis wurde dem Ligaausschuss übermittelt und wird jetzt in den Ligatabellen angezeigt.");
        $spieltag = $spielplan->turnier->getSpieltag();
        if (Tabelle::is_spieltag_beendet($spieltag)) {
            nLigaBot::blockWechsel();
        }
        DoctrineWrapper::manager()->refresh($turnierEntity); # Doctrine erkennt die Änderungen in setErgebnis nicht.
        FreilosService::handleAusgerichtetesTurnierFreilos($turnierEntity);
        FreilosService::handleFreilosRecycling($turnierEntity);
        Helper::reload(get: "?turnier_id=" . $turnier_id);
    }

}

// Hinweis Kaderkontrolle und Turnierreport
if (!(new TurnierReport($turnier_id))->kader_check()) {
    Html::info("Bitte kontrolliert die Teamkader und setzt im "
            . Html::link('../teamcenter/tc_turnier_report.php?turnier_id='
            . $turnier_id, 'Turnierreport') . " das entsprechende Häkchen.", esc:false);
}

if(!$spielplan->validate_penalty_ergebnisse()){
    Html::error("Achtung: Es liegen falsch eingetragene Penaltyergebnisse vor.");
}

// Gibt es eine Diskrepanz zwischen Turnierergebnis und in der Datenbank hinterlegtem Turnierergebnis?
$error = false;
$vgl_data = $spielplan->turnier->getErgebnis();
if (!empty($vgl_data)) {
    if (
        !$spielplan->check_turnier_beendet()
        || count($vgl_data) != count($turnier->getSetzliste())
    ) {
        $error = true;
    } elseif (!$turnier->isFinalTurnier()) {
        foreach ($spielplan->platzierungstabelle as $ergebnis) {
            if ($vgl_data[$ergebnis['platz']]['ergebnis'] != $ergebnis['ligapunkte']) {
                $error = true;
            }
        }
    }
    if ($error) {
        Html::notice("Turnierergebnis stimmt nicht mit dem in der Datenbank hinterlegtem Ergebnis überein.");
    }
}

if (
    FreilosService::isAusrichterFreilosBerechtigt($turnierEntity)
) {
    if (FreilosService::hasAusrichterFreilosForAusgerichtetesTurnier($turnierEntity)) {
        HTML::info("Für dieses Turnier habt ihr mit Ergebniseintragung ein Freilos erhalten.");
    } else {
        HTML::notice("Für dieses Turnier erhaltet ihr mit Ergebniseintragung ein Freilos.");
    }
}