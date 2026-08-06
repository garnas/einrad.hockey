<?php

use App\Entity\Turnier\Turnier;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;
use App\Repository\Turnier\TurnierRepository;

if (isset($_POST['erweitern_turnier'])) {
    /** @var Turnier $turnier */
    $einBlockHoch = ($_POST['single'] ?? '') === 'higher';
    $einBlockRunter = ($_POST['single'] ?? '') === 'lower';
    $blockfrei = ($_POST['multiple'] ?? '')  === 'free';

    if (TurnierValidatorService::onErweiterung($turnier)) {
        if ($einBlockHoch) {
            TurnierService::erweitereBlockHoch($turnier);
            TurnierService::setzListeAuffuellen($turnier);
            TurnierService::neueWartelistePositionen($turnier);
            TurnierRepository::get()->speichern($turnier);
            Html::info("Das Turnier wurde auf den nächsthöheren Block geöffnet");
        } elseif ($einBlockRunter) {
            TurnierService::erweitereBlockRunter($turnier);
            TurnierService::setzListeAuffuellen($turnier);
            TurnierService::neueWartelistePositionen($turnier);
            TurnierRepository::get()->speichern($turnier);
            Html::info("Das Turnier wurde auf den nächstniedrigeren Block geöffnet.");
        }

        if ($blockfrei) {
            TurnierService::erweitereBlockFrei($turnier);
            TurnierService::setzListeAuffuellen($turnier);
            TurnierService::neueWartelistePositionen($turnier);
            TurnierRepository::get()->speichern($turnier);
            Html::info("Das Turnier wurde auf alle Blöcke geöffnet.");
        }

        if (!$blockfrei and !$einBlockHoch and !$einBlockRunter) {
            Html::notice("Die Blöcke des Turniers wurden nicht verändert.");
        }

        Helper::reload(get: "?turnier_id=" . $turnier->id());
    }

}
