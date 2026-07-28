<?php

use App\Entity\Turnier\TurnierDetails;
use App\Entity\Turnier\Turnier;
use App\Event\Turnier\nLigaBot;
use App\Event\Turnier\TurnierEventMailBot;
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\BlockService;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;

$turnier = null;

$ausrichter_team_id = $_SESSION['logins']['team']['id'];
$ausrichter_name = $_SESSION['logins']['team']['name'];
$ausrichter_block = $_SESSION['logins']['team']['block'];
$ausrichter = TeamRepository::get()->team($ausrichter_team_id);

$block_higher = BlockService::getHigherBlocks($ausrichter_block);

// Formularauswertung
// Unterscheidung im HTML, wenn das Turnier vom $_POST Request kommt ($turnier_from_form = true):
// So sollen manche Felder weiterhin änderbar sein, wenn diese einen Fehler bei der Turnierstellung verursacht haben.
$turnier_from_form = false;
if (isset($_POST['create_turnier'])) {
    // Formular fuellen sollte es abbrechen
    $turnier_from_form = true;
    $turnier_datum = new DateTime($_POST['datum']);
    $turnier_datum_bis = (!isset($_POST['datum_bis']) || $_POST['datum_bis'] === '') ? null : new DateTime($_POST['datum_bis']);

    $turnier_name = (string) $_POST['tname'];
    $adresse_hallenname = (string) $_POST['hallenname'];
    $adresse_strasse = $strasse = (string) $_POST['strasse'];
    $adresse_plz = (string) $_POST['plz'];
    $adresse_ort = (string) $_POST['ort'];
    $adresse_haltestellen = (string) $_POST['haltestellen'];
    $turnier_hinweis = (string) $_POST['hinweis'];
    $turnier_startgebuehr = (string) $_POST['startgebuehr'];
    $turnier_organisator = (string) $_POST['organisator'];
    $turnier_handy = (string) $_POST['handy'];
    $turnier_startzeit = (string) $_POST['startzeit'];
    $plaetze = (string) ($_POST['plaetze'] ?? '');
    $min_teams = (int) ($_POST['min_teams'] ?? '');

    // Sofort oeffnen
    $sofort_oeffnen = (string) ($_POST['sofort_oeffnen'] ?? '');
    $sofort_oeffnen_frei = ($sofort_oeffnen === 'free');
    $sofort_oeffnen_runter = ($sofort_oeffnen === 'lower');
    $sofort_oeffnen_hoch = ($sofort_oeffnen === 'higher');

    // Turnierblock und -art
    $turnier_art_block = $_POST['art_block'];
    if (!str_contains($turnier_art_block, "_")) {
        $art = $turnier_art_block ;
        $block = null;
    } else {
        [$art, $block] = explode("_", $turnier_art_block);
    }

    $fixed = "Nein";
    if ($art === 'fixed') {
        $fixed = "Ja";
    }

    // Besprechung
    if (($_POST['besprechung'] ?? '') === 'Ja') {
        $besprechung = 'Ja';
    } else {
        $besprechung = 'Nein';
    }

    // Turnier erstellen
    $turnier = new Turnier();
    $turnier
        ->setDatum($turnier_datum)
        ->setDatumBis($turnier_datum_bis)
        ->setName($turnier_name)
        ->setArt($art)
        ->setAusrichter($ausrichter)
        ->setBlock($block)
        ->setSaison(Config::SAISON)
        ->setPhase('warte')
        ->setCanceled(false)
        ->setErstelltAm(new DateTime())
        ->setSofortOeffnenFrei($sofort_oeffnen_frei)
        ->setSofortOeffnenHoch($sofort_oeffnen_hoch)
        ->setSofortOeffnenRunter($sofort_oeffnen_runter)
        ->setBlockErweitertFrei(false)
        ->setBlockErweitertHoch(false)
        ->setBlockErweitertRunter(false)
        ->setBlockFixed($fixed);

    $details = new TurnierDetails();
    $details
        ->setTurnier($turnier)
        ->setBesprechung($besprechung)
        ->setHallenname($adresse_hallenname)
        ->setHaltestellen($adresse_haltestellen)
        ->setHandy($turnier_handy)
        ->setOrganisator($turnier_organisator)
        ->setHinweis($turnier_hinweis)
        ->setPlz($adresse_plz)
        ->setOrt($adresse_ort)
        ->setStrasse($strasse)
        ->setStartgebuehr($turnier_startgebuehr)
        ->setStartzeit(DateTime::createFromFormat("H:i", $turnier_startzeit))
        ->setPlaetze($plaetze)
        ->setMinTeams($min_teams);

    $turnier->setDetails($details);


    // Anlegen des neuen Turniers, wenn alle Eintragungen valide sind
    if (TurnierValidatorService::onCreate($turnier)) {

        TurnierService::addToSetzListe($turnier, $ausrichter);

        TurnierRepository::get()->speichern($turnier);
        nLigaBot::setSpieltage();

        if (Helper::$teamcenter) {
            TurnierEventMailBot::mailNeuesTurnier($turnier);
        }

        Html::info("Euer Turnier wurde erfolgreich eingetragen.");

        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());

    } else {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht eingetragen.");
    }

}
