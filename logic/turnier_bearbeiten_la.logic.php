<?php

// Formularauswertung Turnier löschen
use App\Event\Turnier\TurnierEventMailBot;
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;

if (isset($_POST['delete_turnier'])) {
        TurnierRepository::get()->delete($turnier);
        Html::info("Turnier wurde gelöscht.");
        Helper::reload('/liga/turniere.php');
}

if (isset($_POST['absagen_turnier'])) {
    TurnierService::cancel($turnier, $_POST['grund']);
    TurnierRepository::get()->speichern($turnier);
    if (isset($_POST['send_mail'])) {
        TurnierEventMailBot::mailCanceled($turnier);
    }
    Html::info("Turnier wurde abgesagt.");
    Helper::reload('/ligacenter/lc_turnierliste.php');
}

// Forumlarauswertung Turnierdaten ändern
if (isset($_POST['turnier_bearbeiten_la'])) {

    // Ausrichter setzen
    $ausrichter = TeamRepository::get()->findByName($_POST['ausrichter']);

    if (is_null($ausrichter)) {
        $error = true;
        Html::error('Der Ausrichter wurde nicht gefunden.');
    } elseif (!$ausrichter->isLigaTeam()) {
        $error = true;
        Html::error("Der Ausrichter ist ein NL-Team");
    }

    // Turnierblock ändern:
    $art = $_POST['art'];

    // Restliche Daten:
    $block = $_POST['block'];
    $unixTime = strtotime($_POST['datum']);
    $datum = new DateTime($_POST['datum']);
    $datum_bis = (!isset($_POST['datum_bis']) || $_POST['datum_bis'] === '') ? null : new DateTime($_POST['datum_bis']);
    $phase = $_POST['phase'];

    $turnier
        ->setAusrichter($ausrichter)
        ->setArt($art)
        ->setDatum($datum)
        ->setDatumBis($datum_bis)
        ->setBlock($block)
        ->setPhase($phase);

    // Ändern der Turnierdaten
    if (TurnierValidatorService::onChange($turnier)) {

        TurnierRepository::get()->speichern($turnier);
        Html::info("Turnierdaten wurden geändert");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
    } else {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}