<?php

use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;
use App\Repository\Turnier\TurnierRepository;

if (isset($_POST['erweitern_turnier'])) {

    $higher = ($_POST['single'] ?? '') === 'higher';
    $lower = ($_POST['single'] ?? '') === 'lower';
    $single = ($_POST['single'] ?? '') === 'none';
    
    $free = ($_POST['multiple'] ?? '') === 'free';
    $multiple = ($_POST['multiple'] ?? '') === 'none';

    $turnier
        ->setBlockErweitertRunter($lower)
        ->setBlockErweitertHoch($higher)
        ->setBlockErweitertFrei($free);

    if (TurnierValidatorService::onErweiterung($turnier)) {
        $error = false;
        $sucess_text = '';

        if ($higher) {
            TurnierService::erweitereBlockHoch($turnier);
            TurnierService::setzListeAuffuellen($turnier);
            TurnierService::neueWartelistePositionen($turnier);
            TurnierRepository::get()->speichern($turnier);
            $sucess_text .= "Das Turnier wurde auf den nächsthöheren Block geöffnet. ";
        } elseif ($lower) {
            TurnierService::erweitereBlockRunter($turnier);
            TurnierService::setzListeAuffuellen($turnier);
            TurnierService::neueWartelistePositionen($turnier);
            TurnierRepository::get()->speichern($turnier);
            $sucess_text .= "Das Turnier wurde auf den nächstniedrigeren Block geöffnet. ";
        } elseif ($single) {
            $sucess_text .= "Es wurde <b>keine</b> einzelne Blockerweiterung vorgenommen. ";
        } else {
            $error = true;
            Html::error("Es konnte keine einzele Blockerweiterung vorgenommen werden. ");
        }
        
        if ($free) {
            TurnierService::erweitereBlockFrei($turnier);
            TurnierService::setzListeAuffuellen($turnier);
            TurnierService::neueWartelistePositionen($turnier);
            TurnierRepository::get()->speichern($turnier);
            $sucess_text .= "Das Turnier wurde auf alle Blöcke geöffnet. ";
        } elseif ($multiple) {
            $sucess_text .= "Es wurde <b>keine</b> Erweiterung auf alle Blöcke vorgenommen. ";
        } else {
            $error = true;
            Html::error("Es konnte keine Erweiterung auf alle Blöcke vorgenommen werden. ");
        }

        if (!$error) {
            Html::info($sucess_text, esc: false);
            Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id());
        }
    }

}



