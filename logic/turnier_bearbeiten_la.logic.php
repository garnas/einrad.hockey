<?php

// Formularauswertung Turnier löschen
use App\Event\Turnier\TurnierEventMailBot;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierService;

if (isset($_POST['absagen_turnier'])) {
    TurnierService::cancel($turnier, $_POST['grund']);
    TurnierRepository::get()->speichern($turnier);
    if (isset($_POST['send_mail'])) {
        TurnierEventMailBot::mailCanceled($turnier);
    }
    Html::info("Turnier wurde abgesagt.");
    Helper::reload('/ligacenter/lc_turnierliste.php');
}
