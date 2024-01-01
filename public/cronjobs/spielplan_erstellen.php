<?php
use App\Event\Turnier\nLigaBot;
use App\Service\Turnier\TurnierService;
use App\Repository\Turnier\TurnierRepository;
use App\Event\Turnier\TurnierEventMailBot;
require_once '../../init.php';

$_SESSION['logins']['cronjob'] = 'Cronjob';

$aktueller_spieltag = Tabelle::get_aktuellen_spieltag();
echo "Spieltag: " . $aktueller_spieltag . "<br>";
$turniere = nTurnier::get_turniere_spieltag($aktueller_spieltag);

foreach ($turniere as $turnier) {
    # Check if dienstag vor dem Datum
    $datum_string = $turnier->get_datum();
    $datum_turnier = strtotime($datum_string);
    $aktuelles_datum = time();
    $absage_grund = "";
    $erstellen = True;
    # Wenn heute Dienstag ist, dann einen Tag weiter gehen
    if (date("N", $aktuelles_datum) == 2) {
        $aktuelles_datum = strtotime("+1 day", $aktuelles_datum);
    }
    # Check Dienstag zwischen morgen und dem Datum des Turnieres
    while ($aktuelles_datum < $datum_turnier) {
        # Dienstag = 2. Wochentag
        if (date("N", $aktuelles_datum) == 2) {
            $erstellung_starten = False;
            break;
        }
        $aktuelles_datum = strtotime("+1 day", $aktuelles_datum);
    }
    if ($erstellen) {
        Html::info("Handling Turnier " . $turnier->get_turnier_id());
        $teams = $turnier->get_spielenliste();
        $ligateams = array_filter($teams, function ($team) {
            return ($team->details["ligateam"] ?? "Nein") == "Ja";
        });
        if (count($ligateams) < 4) {
            $absage_grund = "Zu wenige Ligateams";
        } elseif (
            count($teams) == 4
            && $turnier->get_art() != "I"
        ) {
            $absage_grund = "Vierer-Turniere dürfen nur als blockeigene Turniere (Art I) stattfinden.";
        } elseif (
            count($teams) == 4
            && $turnier->get_art() == "I"
            && $turnier->get_tblock() == "ABCDEF"
        ) {
            $absage_grund = "Vierer-Turniere dürfen nur als blockeigene Turniere (Art I) stattfinden, dass Turnier
             wurde jedoch auf ein blockfreies Turnier erweitert.";
        }
        if ($absage_grund != "") {
            $turnier_new = TurnierRepository::get()->turnier($turnier->get_turnier_id());
            TurnierService::cancel($turnier_new, $absage_grund);
            TurnierRepository::get()->speichern($turnier_new);
            TurnierEventMailBot::mailCanceled($turnier_new);
            Html::info("Abgesagt: " . $turnier->get_turnier_id());
        }elseif (Spielplan::spielplan_erstellen($turnier)) { # Weitere Checks für den LA in dieser Funktion
            Html::info("Spielplan für " . $turnier->get_turnier_id() . " erstellt");
        } else {
            Html::error("Kein Spielplan für " . $turnier->get_turnier_id() . " erstellt");
        }
        Html::print_messages();
    }
}

session_destroy();