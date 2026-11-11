<?php

use App\Spielplan\Service\SpielplanService;
use App\Turnier\Mail\TurnierMails;
use App\Turnier\Repository\TurnierRepository;
use App\Turnier\Service\TurnierFristenService;
use App\Turnier\Service\TurnierService;
use App\Turnierbericht\Entity\Turnierbericht;
use App\Turnierbericht\Repository\TurnierberichtRepository;

require_once '../../init.php';

$_SESSION['logins']['cronjob'] = 'Cronjob';

# Die Spielplanphase beginnt Dienstag 23:59 in der Woche vor dem Spieltag (siehe TurnierFristenService).
$turniere = TurnierRepository::get()->getSetzphaseTurniere();
$jetzt = new DateTimeImmutable();

foreach ($turniere as $turnier) {
    $absage_grund = "";

    if ($jetzt >= TurnierFristenService::spielplanphaseAb($turnier)) {
        Html::info("Handling Turnier " . $turnier->id());
        $setzliste = $turnier->getSetzliste()->toArray();
        $ligateams = array_filter($setzliste, static function ($listeneintrag) {
            return ($listeneintrag->getTeam()->isLigaTeam());
        });
        $min_ligateams = count($setzliste) === 4 ? 3 : 4;
        if (count($ligateams) < $min_ligateams) {
            $absage_grund = "Zu wenige Ligateams";
        }
        if ($turnier->getDetails()->getMinTeams() && (count($setzliste) < $turnier->getDetails()->getMinTeams())) {
            $absage_grund = "Minimale Anzahl an Teams nicht erreicht";
        }
        if ($absage_grund != "") {
            TurnierService::cancel($turnier, $absage_grund);
            TurnierRepository::get()->speichern($turnier);
            TurnierMails::mailCanceled($turnier);
            Html::info("Abgesagt: " . $turnier->id());
        } elseif (SpielplanService::erstellen($turnier)) { # Weitere Checks für den LA in dieser Funktion
            $turnierbericht = new Turnierbericht($turnier);
            TurnierberichtRepository::get()->speichern($turnierbericht);
            Html::info("Spielplan für " . $turnier->id() . " erstellt");
        } else {
            Html::error("Keinen Spielplan für " . $turnier->id() . " erstellt");
        }
        Html::print_messages();
    }
}

session_destroy();
