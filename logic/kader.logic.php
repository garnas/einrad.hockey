<?php

// Neuer Spieler eintragen
use App\Entity\Team\FreilosGrund;
use App\Repository\Team\TeamRepository;
use App\Service\Team\TeamService;

if (isset($_POST['neuer_eintrag'])) {
    $error = false;
    $vorname = $_POST['vorname'];
    $nachname = $_POST['nachname'];
    $jahrgang = $_POST['jahrgang'];
    $geschlecht = $_POST['geschlecht'];

    if (($_POST['dsgvo'] ?? '') !== 'zugestimmt') {
        $error = true;
        Html::error("Den Datenschutz-Hiweisen muss zugestimmt werden, um in einem Ligateam spielen zu können.");
    }
    if (empty($vorname) || empty($nachname) || empty($jahrgang) || empty($geschlecht)) {
        $error = true;
        Html::error("Bitte Felder ausfüllen");
    }

    if (!$error) {
        $spieler = new nSpieler();
        if ($spieler
            ->set_vorname($vorname)
            ->set_nachname($nachname)
            ->set_jahrgang($jahrgang)
            ->set_geschlecht($geschlecht)
            ->set_team_id($team_id)
            ->set_letzte_saison(Config::SAISON)
            ->speichern(true)
        ) {
            Html::info("Der Spieler wurde erfolgreich eingetragen.");
            Helper::reload(get:'?team_id=' . $team_id);
        } else {
            Html::error("Der Spieler konnte nicht eingetragen werden.");
        }
    }
}

// Spieler aus der Vorsaison übernehmen
if (isset($_POST['submit_takeover'])) {
    $changed = false;
    if (($_POST['dsgvo'] ?? '') !== 'zugestimmt') {
        Html::error("Den Datenschutz-Hiweisen muss zugestimmt werden, um in einem Ligateam spielen zu können.");
    } else {
        foreach (($_POST['takeover'] ?? []) as $spieler_id) {
            if (!empty($kader_vorsaison[$spieler_id])) { // Validation + Schutz gegen Html-Manipulation
                $spieler = nSpieler::get($spieler_id);
                $spieler
                    ->set_letzte_saison(Config::SAISON)
                    ->speichern();
                $changed = true;
            }
        }
        if ($changed) {
            Html::info("Die Spieler wurden in die neue Saison übernommen.");
            $team = TeamRepository::get()->team($team_id);
            if (TeamService::handleSchiriFreilos($team)) {
                TeamRepository::get()->speichern($team);
                Html::info("Schirifreilos erhalten!");
            }
        }
        Helper::reload();
    }
}
