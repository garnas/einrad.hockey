<?php

use App\Entity\Turnier\TurnierDetails;
use App\Event\Turnier\nLigaBot;
use App\Event\Turnier\TurnierEventMailBot;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\BlockService;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;

$block_higher = BlockService::getHigherBlocks($ausrichter_block);
$block_higher_str = BlockService::toString($block_higher);

// Formularauswertung
if (isset($_POST['create_turnier'])) {    

    $unixTime = strtotime($_POST['datum']);
    $datum = (new DateTime)->setTimestamp($unixTime);

    $name = (string)$_POST['tname'];
    $hallenname = (string)$_POST['hallenname'];
    $strasse = (string)$_POST['strasse'];
    $plz = (string)$_POST['plz'];
    $ort = (string)$_POST['ort'];
    $haltestellen = (string)$_POST['haltestellen'];
    $hinweis = (string)$_POST['hinweis'];
    $startgebuehr = (string)$_POST['startgebuehr'];
    $organisator = (string)$_POST['organisator'];
    $handy = (string)$_POST['handy'];
    $art = (string)$_POST['art'];
    $block = ($art === 'I') ? $ausrichter_block : $_POST['block'];
    $startzeit = DateTime::createFromFormat("H:i", ((string)($_POST['startzeit'] ?? '')));
    $plaetze = (string)($_POST['plaetze'] ?? '');
    $sofotOeffnen = @($_POST['sofort_oeffnen'] === "Ja");

    // Besprechung
    if (($_POST['besprechung'] ?? '') === 'Ja') {
        $besprechung = 'Ja';
    } else {
        $besprechung = 'Nein';
    }

    // Eintragen des Turnieres
    // Turnier erstellen
    $turnier = new App\Entity\Turnier\Turnier();
    $turnier
        ->setDatum($datum)
        ->setName($name)
        ->setArt($art)
        ->setAusrichter($ausrichter)
        ->setBlock($block)
        ->setSaison(Config::SAISON)
        ->setPhase('warte')
        ->setCanceled(false)
        ->setErstelltAm(new DateTime())
        ->setSofortOeffnen($sofotOeffnen);

    $details = new TurnierDetails();
    $details
        ->setTurnier($turnier)
        ->setBesprechung($besprechung)
        ->setHallenname($hallenname)
        ->setHaltestellen($haltestellen)
        ->setHandy($handy)
        ->setOrganisator($organisator)
        ->setHinweis($hinweis)
        ->setPlz($plz)
        ->setOrt($ort)
        ->setStrasse($strasse)
        ->setStartgebuehr($startgebuehr)
        ->setStartzeit($startzeit)
        ->setPlaetze($plaetze);
    $turnier->setDetails($details);



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