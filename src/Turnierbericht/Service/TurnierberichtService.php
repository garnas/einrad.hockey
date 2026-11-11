<?php

namespace App\Turnierbericht\Service;

use App\Turnierbericht\Entity\Turnierbericht;

class TurnierberichtService
{
    public static function isKaderChecked(Turnierbericht $bericht): bool
    {
        return $bericht->getKaderUeberprueft() == 'Ja';
    }
}
