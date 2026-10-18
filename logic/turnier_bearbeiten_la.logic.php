<?php

// Formularauswertung Turnier löschen
use App\Turnier\Mail\TurnierMails;
use App\Turnier\Repository\TurnierRepository;
use App\Turnier\Service\TurnierService;

if (isset($_POST['absagen_turnier'])) {
    TurnierService::cancel($turnier, $_POST['grund']);
    TurnierRepository::get()->speichern($turnier);
    if (isset($_POST['send_mail'])) {
        TurnierMails::mailCanceled($turnier);
    }
    Html::info("Turnier wurde abgesagt.");
    Helper::reload('/ligacenter/lc_turnierliste.php');
}
