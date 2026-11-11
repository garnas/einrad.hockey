<?php

// Formularauswertung Turnier absagen (Ligaausschuss, Ausrichter von Spaßturnieren)
use App\Turnier\Mail\TurnierMails;
use App\Turnier\Repository\TurnierRepository;
use App\Turnier\Service\TurnierPermission;
use App\Turnier\Service\TurnierService;

$turnierliste = Helper::$ligacenter ? '/ligacenter/lc_turnierliste.php' : '/teamcenter/tc_turnierliste_verwalten.php';

if (!TurnierPermission::canCancel($turnier)) {
    Html::error("Keine Berechtigung das Turnier abzusagen");
    Helper::reload($turnierliste);
}

if (isset($_POST['absagen_turnier']) && !$turnier->isCanceled()) {
    $grund = trim($_POST['grund'] ?? '');
    if ($grund === '') {
        Html::error("Bitte gib einen Grund für die Absage an.");
        Helper::reload(get: '?turnier_id=' . $turnier->id());
    }
    TurnierService::cancel($turnier, $grund);
    TurnierRepository::get()->speichern($turnier);
    if (isset($_POST['send_mail'])) {
        TurnierMails::mailCanceled($turnier);
    }
    Html::info("Turnier wurde abgesagt.");
    Helper::reload($turnierliste);
}
