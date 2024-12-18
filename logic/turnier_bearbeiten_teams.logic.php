<?php

// Formularauswertung
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;


// Block erweitern

// Um den nächsten höheren Buchstaben erweitern
if (isset($_POST['block_erweitern'])) {
    if (TurnierValidatorService::isErweiterbarBlockhoch($turnier)) {
        TurnierService::blockhochErweitern($turnier);
        TurnierService::setzListeAuffuellen($turnier);
        TurnierRepository::get()->speichern($turnier);
        Html::info("Das Turnier wurde auf den nächst höheren Block geöffnet.");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
    } else {
        Html::error("Das Turnier kann nicht um den nächsthöheren Block erweitert werden.");
    }
}

// Auf ABCDEF erweitern
if (isset($_POST['block_frei'])) {
    if (TurnierValidatorService::isErweiterbarBlockfrei($turnier)) {
        TurnierService::blockOeffnen($turnier);
        TurnierService::setzListeAuffuellen($turnier);
        TurnierRepository::get()->speichern($turnier);
        Html::info("Das Turnier wurde auf alle Blöcke geöffnet.");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
    } else {
        Html::error("Das Turnier kann nicht auf ABCDEF erweitert werden.");
    }
}

if (isset($_POST['change_turnier'])) {

    $hallenname = $_POST['hallenname'];
    $strasse = $_POST['strasse'];
    $plz = $_POST['plz'];
    $ort = $_POST['ort'];
    $haltestellen = $_POST['haltestellen'];
    $hinweis = $_POST['hinweis'];
    $startgebuehr = $_POST['startgebuehr'];
    $organisator = $_POST['organisator'];
    $handy = $_POST['handy'];
    $startzeit = DateTime::createFromFormat("H:i", $_POST['startzeit']);
    $plaetze = (int) $_POST['plaetze'];
    $min_teams = (int) $_POST['min_teams'];
    $tname = $_POST['tname'];
    $sofortOeffnen = @($_POST['sofort_oeffnen'] === "Ja");

    // Leere Felder können eigentlich nicht auftreten (nur durch html-Manipulation), aber sicherheitshalber das hier...
    if (
        empty($plaetze) || empty($startzeit) || empty($hallenname) || empty($strasse) || empty($plz) || empty($ort)
        || empty($organisator) || empty($handy) || empty($min_teams)
    ) {
        Html::error("Bitte alle nicht optionalen Felder ausfüllen.");
        Helper::reload();
    }

    // Besprechung
    if (($_POST['besprechung'] ?? '') === 'Ja') {
        $besprechung = 'Ja';
    } else {
        $besprechung = 'Nein';
    }
    $plaetze_before = $turnier->getDetails()->getPlaetze();
    $turnier
        ->setName($tname);
    if ($turnier->isWartePhase()) {
        $turnier->setSofortOeffnen($sofortOeffnen);
    }
    $turnier->getDetails()->setStartzeit($startzeit)
        ->setBesprechung($besprechung)
        ->setPlaetze($plaetze)
        ->setMinTeams($min_teams)
        ->setHallenname($hallenname)
        ->setStrasse($strasse)
        ->setPlz($plz)
        ->setOrt($ort)
        ->setHaltestellen($haltestellen)
        ->setStartgebuehr($startgebuehr)
        ->setOrganisator($organisator)
        ->setHandy($handy)
        ->setHinweis($hinweis)
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
