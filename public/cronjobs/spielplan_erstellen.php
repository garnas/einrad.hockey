<?php

use App\Event\Turnier\TurnierEventMailBot;
use App\Repository\TurnierBericht\TurnierBerichtRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Entity\TurnierBericht\TurnierBericht;
use App\Service\Turnier\TabelleService;
use App\Service\Turnier\TurnierService;

require_once '../../init.php';

$_SESSION['logins']['cronjob'] = 'Cronjob';

$aktueller_spieltag = TabelleService::getAktuellenSpieltag();
echo "Spieltag: " . $aktueller_spieltag . "<br>";
$turniere = TurnierRepository::get()->getTurniereSpieltag($aktueller_spieltag);

foreach ($turniere as $turnier) {
    $datum_turnier = $turnier->getDatum()->getTimestamp();
    $aktuelles_datum = time();
    $absage_grund = "";
    $erstellen = true;

    # Sollte heute Dienstag sein, dann schieben das Datum einmal nach vorne
    if (date("N", $aktuelles_datum) == 2) {
        $aktuelles_datum = strtotime("+1 day", $aktuelles_datum);
    }

    # Prüfe, ob noch zwei Dienstage zwischen dem Turnier und dem Ausgangstag liegen
    $dienstag_counter = 0;
    while ($aktuelles_datum < $datum_turnier) {
        # Dienstag = 2. Wochentag
        if (date("N", $aktuelles_datum) == 2) {
            $dienstag_counter++;

            if ($dienstag_counter > 1) {
                $erstellen = false;
                break;
            }
        }
        $aktuelles_datum = strtotime("+1 day", $aktuelles_datum);
    }

    if ($turnier->getPhase() != "setz") {
        $erstellen = false;
    }

    if ($erstellen) {
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
            TurnierEventMailBot::mailCanceled($turnier);
            Html::info("Abgesagt: " . $turnier->id());
        } elseif (Spielplan::spielplan_erstellen($turnier)) { # Weitere Checks für den LA in dieser Funktion
            $turnierbericht = new TurnierBericht($turnier);
            TurnierBerichtRepository::get()->speichern($turnierbericht);
            Html::info("Spielplan für " . $turnier->id() . " erstellt");
        } else {
            Html::error("Keinen Spielplan für " . $turnier->id() . " erstellt");
        }
        Html::print_messages();
    }
}

session_destroy();
