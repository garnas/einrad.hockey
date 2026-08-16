<?php

// Formularauswertung
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;
use App\Service\Turnier\BlockService;

// Formular fuellen
$ausrichter_name = $turnier->getAusrichter()->getName();
$ausrichter_block = $turnier->getAusrichter()->getBlock();
$ausrichter_team_id = $turnier->getAusrichter()->id();

$block_higher = BlockService::getHigherBlocks($ausrichter_block);

$turnier_datum = $turnier->getDatum();
$turnier_datum_bis = $turnier->getDatumBis();
$turnier_datum_bis = $turnier_datum_bis ? $turnier_datum_bis->format("Y-m-d") : "";

$turnier_startzeit = $turnier->getDetails()->getStartzeit()->format("H:i");
$besprechung = $turnier->getDetails()->getBesprechung();

$turnier_block = $turnier->getBlock();
$turnier_art = $turnier->getArt();
$turnier_art_block = $turnier_block ? $turnier_art . "_" . $turnier_block : $turnier_art;

$sofort_oeffnen = "none";
if ($turnier->isSofortOeffnenFrei()) {
    $sofort_oeffnen = "free";
} elseif ($turnier->isSofortOeffnenHoch()) {
    $sofort_oeffnen = "higher";
} elseif ($turnier->isSofortOeffnenRunter()) {
    $sofort_oeffnen = "lower";
}

$min_teams = (string) $turnier->getDetails()->getMinTeams();
$plaetze = (string) $turnier->getDetails()->getPlaetze();

$adresse_hallenname = $turnier->getDetails()->getHallenname();
$adresse_strasse = $turnier->getDetails()->getStrasse();
$adresse_plz = $turnier->getDetails()->getPlz();
$adresse_ort = $turnier->getDetails()->getOrt();
$adresse_haltestellen = $turnier->getDetails()->getHaltestellen();

$turnier_hinweis = $turnier->getDetails()->getHinweis();
$turnier_name = $turnier->getName();
$turnier_startgebuehr = $turnier->getDetails()->getStartgebuehr();

$turnier_organisator = $turnier->getDetails()->getOrganisator();
$turnier_handy = $turnier->getDetails()->getHandy();

$phase = $turnier->getPhase();

// Unterscheidung im HTML, wenn das Turnier vom $_POST Request kommt ($turnier_from_form = true):
// So sollen manche Felder weiterhin änderbar sein, wenn diese einen Fehler bei der Turnierstellung verursacht haben.
$turnier_from_form = false;
if (isset($_POST['change_turnier'])) {
    $turnier_from_form = true;
    $adresse_hallenname = $_POST['hallenname'];
    $adresse_strasse = $_POST['strasse'];
    $adresse_plz = $_POST['plz'];
    $adresse_ort = $_POST['ort'];
    $adresse_haltestellen = $_POST['haltestellen'];
    $turnier_hinweis = $_POST['hinweis'];
    $turnier_startgebuehr = ($_POST['startgebuehr']) ?? $turnier_startgebuehr;
    $turnier_organisator = $_POST['organisator'];
    $turnier_handy = $_POST['handy'];
    $turnier_startzeit = (string) ($_POST['startzeit'] ?? $turnier_startzeit);
    $plaetze = (int) ($_POST['plaetze'] ?? $plaetze);
    $min_teams = (int) ($_POST['min_teams'] ?? $min_teams);
    $turnier_name = $_POST['tname'];

    // Sofort oeffnen
    $sofort_oeffnen = (string) ($_POST['sofort_oeffnen'] ?? '');
    $sofort_oeffnen_frei = ($sofort_oeffnen === 'free');
    $sofort_oeffnen_runter = ($sofort_oeffnen === 'lower');
    $sofort_oeffnen_hoch = ($sofort_oeffnen === 'higher');

    // Besprechung
    if (($_POST['besprechung'] ?? '') === 'Ja') {
        $besprechung = 'Ja';
    } else {
        $besprechung = 'Nein';
    }

    // Änderungen ausschließlich durch den LA
    if (Helper::$ligacenter) {
        $unixTime = strtotime($_POST['datum']);
        $datum = new DateTime($_POST['datum']);
        $datum_bis = (!isset($_POST['datum_bis']) || $_POST['datum_bis'] === '') ? null : new DateTime($_POST['datum_bis']);
        $turnier->setDatum($datum);
        $turnier->setDatumBis($datum_bis);

        $phase = (!isset($_POST['phase']) || $_POST['phase'] === '') ? $turnier->getPhase() : $_POST['phase'];
        $turnier->setDatum($datum);
        $turnier->setDatumBis($datum_bis);
    }

    $plaetze_before = $turnier->getDetails()->getPlaetze();
    $turnier
        ->setName($turnier_name)
        ->setSofortOeffnenFrei($sofort_oeffnen_frei)
        ->setSofortOeffnenHoch($sofort_oeffnen_hoch)
        ->setSofortOeffnenRunter($sofort_oeffnen_runter)
        ->setPhase($phase);

    $turnier->getDetails()
        ->setStartzeit(DateTime::createFromFormat("H:i", $turnier_startzeit))
        ->setBesprechung($besprechung)
        ->setPlaetze($plaetze)
        ->setMinTeams($min_teams)
        ->setHallenname($adresse_hallenname)
        ->setStrasse($adresse_strasse)
        ->setPlz($adresse_plz)
        ->setOrt($adresse_ort)
        ->setHaltestellen($adresse_haltestellen)
        ->setStartgebuehr($turnier_startgebuehr)
        ->setOrganisator($turnier_organisator)
        ->setHandy($turnier_handy)
        ->setHinweis($turnier_hinweis)
    ;

    if (
        TurnierValidatorService::onChange($turnier)
        && TurnierValidatorService::mayChangePlaetze($turnier, $plaetze_before)
    ) {
        if ($turnier->isSetzPhase()) {
            TurnierService::setzListeAuffuellen($turnier);
            TurnierService::neueWartelistePositionen($turnier);
        }
        TurnierRepository::get()->speichern($turnier);
        Html::info("Turnierdaten wurden geändert");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
    } else {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}
