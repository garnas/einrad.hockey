<?php

// Formularauswertung Turnier löschen
if (isset($_POST['delete_turnier'])) {
    if ($_POST['delete_turnier_check'] !== 'checked') {
        Html::error('Bitte Hinweistext vor dem Löschen des Turnieres lesen.');
    } else {
        $turnier->delete($_POST['delete_turnier_grund']);
        Html::info("Turnier wurde gelöscht");
        Helper::reload('/ligacenter/lc_turnierliste.php#deleted');
    }
}

// Forumlarauswertung Turnierdaten ändern
if (isset($_POST['turnier_bearbeiten_la'])) {
    $error = false;
    
    // Ausrichter setzen
    $ausrichter = Team::name_to_id($_POST['ausrichter']);

    if (is_null($ausrichter)) {
        $error = true;
        Html::error('Der Ausrichter wurde nicht gefunden.');
    } elseif (!Team::is_ligateam($ausrichter)) {
        $error = true;
        Html::error("Der Ausrichter ist ein NL-Team");
    }

    // Turnierblock ändern:
    $art = $_POST['art'];

    // Fixierter Turnierblock?
    $fixed = match ($art) {
        'I', 'II' => 'Nein',
        default => 'Ja'
    };


    // Restliche Daten:
    $tblock = $_POST['block'];
    $datum = $_POST['datum'];
    $phase = $_POST['phase'];
    $tname = $_POST['tname'];

    if (empty($datum) || empty($phase)) {
        Html::error('Datum und/oder Phase sind leer');
        $error = true;
    }

    // Ändern der Turnierdaten
    if (!$error) {
        $turnier->set_liga('tname', $tname)
                ->set_liga('ausrichter', $ausrichter)
                ->set_liga('art', $art)
                ->set_liga('tblock', $tblock)
                ->set_liga('tblock_fixed', $fixed)
                ->set_liga('datum', $datum)
                ->set_liga('phase', $phase);

        Html::info("Turnierdaten wurden geändert");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->get_turnier_id());
    } else {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}