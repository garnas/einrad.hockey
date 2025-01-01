<?php

use App\Entity\Team\Spieler;
use App\Repository\Spieler\SpielerRepository;
use App\Repository\Team\TeamRepository;
use App\Service\Team\FreilosService;

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
        $spieler = (new Spieler())
            ->setVorname($vorname)
            ->setNachname($nachname)
            ->setJahrgang($jahrgang)
            ->setGeschlecht($geschlecht)
            ->setTeam($teamEntity)
            ->setLetzteSaison(Config::SAISON);
        $existingSpieler = SpielerRepository::get()->findBySpieler($spieler);
        if ($existingSpieler === null) {
            SpielerRepository::get()->speichern($spieler);
            Html::info("Der Spieler wurde erfolgreich eingetragen.");
            Helper::reload(get:'?team_id=' . $team_id);
        } elseif ($existingSpieler->getLetzteSaison() < Config::SAISON) {
            $vorherigesTeam = $existingSpieler->getTeam()->getName();
            $existingSpieler->setTeam($teamEntity);
            $existingSpieler->setLetzteSaison(Config::SAISON);
            SpielerRepository::get()->speichern($existingSpieler);
            Html::info("Der Spieler wurde erfolgreich vom vorherigen Team ($vorherigesTeam) übernommen.");
            Helper::reload(get:'?team_id=' . $team_id);
        } else {
            $aktuellesTeam = $existingSpieler->getTeam()->getName();
            Html::error("Der Spieler ist für diese Saison bereits in einem anderen Team gemeldet ($aktuellesTeam).");
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
                $spieler = SpielerRepository::get()->spieler($spieler_id);
                $spieler->setLetzteSaison(Config::SAISON);
                SpielerRepository::get()->speichern($spieler);
                $changed = true;
            }
        }
        if ($changed) {
            Html::info("Die Spieler wurden in die neue Saison übernommen.");
            $team = TeamRepository::get()->team($team_id);
            if (FreilosService::handleSchiriFreilos($team)) {
                TeamRepository::get()->speichern($team);
                Html::info("Schirifreilos erhalten!");
            }
        }
        Helper::reload();
    }
}
