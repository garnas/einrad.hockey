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
    $geschlecht = (!isset($_POST['geschlecht']) || $_POST['geschlecht'] === '') ? null : $_POST['geschlecht'];

    if (($_POST['dsgvo'] ?? '') !== 'zugestimmt') {
        $error = true;
        Html::error("Den Datenschutz-Hinweisen muss zugestimmt werden, um in einem Ligateam spielen zu können.");
    }
    if (empty($vorname) || empty($nachname) || empty($jahrgang)) {
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
            ->setTimestamp(new DateTime())
            ->setLetzteSaison(Config::SAISON);
        SpielerRepository::get()->speichern($spieler);
        Html::info("Der Spieler wurde erfolgreich eingetragen.");
        Helper::reload(get: '?team_id=' . $team_id);
    }
}

// Spieler eines anderen Teams übernehmen (Auswahl aus der Liste nicht mehr aktueller Spieler)
if (isset($_POST['spieler_uebernahme'])) {
    $error = false;
    $spieler_id = $_POST['spieler_id'] ?? '';

    if (($_POST['dsgvo'] ?? '') !== 'zugestimmt') {
        $error = true;
        Html::error("Den Datenschutz-Hinweisen muss zugestimmt werden, um in einem Ligateam spielen zu können.");
    }
    if (!ctype_digit((string) $spieler_id)) {
        $error = true;
        Html::error("Bitte einen Spieler aus der Liste auswählen.");
    }

    if (!$error) {
        $spieler = SpielerRepository::get()->spieler((int) $spieler_id);
        if ($spieler === null) {
            Html::error("Bitte einen Spieler aus der Liste auswählen.");
        } elseif ($spieler->getLetzteSaison() >= Config::SAISON) {
            $aktuellesTeam = $spieler->getTeam()?->getName() ?? 'einem anderen Team';
            Html::error("Der Spieler ist für diese Saison bereits in einem anderen Team gemeldet ($aktuellesTeam).");
        } else {
            $vorherigesTeam = $spieler->getTeam()?->getName();
            $spieler->setTeam($teamEntity);
            $spieler->setLetzteSaison(Config::SAISON);
            $spieler->setTimestamp(new DateTime());
            SpielerRepository::get()->speichern($spieler);
            Html::info($vorherigesTeam !== null
                ? "Der Spieler wurde erfolgreich vom vorherigen Team ($vorherigesTeam) übernommen."
                : "Der Spieler wurde erfolgreich übernommen.");
            Helper::reload(get: '?team_id=' . $team_id);
        }
    }
}

// Spieler aus der Vorsaison übernehmen
if (isset($_POST['submit_takeover'])) {
    $changed = false;
    if (($_POST['dsgvo'] ?? '') !== 'zugestimmt') {
        Html::error("Den Datenschutz-Hinweisen muss zugestimmt werden, um in einem Ligateam spielen zu können.");
    } else {
        foreach (($_POST['takeover'] ?? []) as $spieler_id) {
            $spieler = SpielerRepository::get()->spieler($spieler_id);
            if ($spieler->getTeam()->id() == $teamEntity->id()) { // Validation + Schutz gegen Html-Manipulation
                $spieler->setLetzteSaison(Config::SAISON);
                $spieler->setTimestamp(new DateTime());
                SpielerRepository::get()->speichern($spieler);
                $changed = true;
            } else {
                Html::error("Spieler gehörte nicht zum Team.");
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
