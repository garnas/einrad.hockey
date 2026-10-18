<?php

namespace App\Spieler\Service;

use App\Ligaleitung\Repository\LigaleitungRepository;
use App\Spieler\Entity\Spieler;
use Config;

class SpielerService
{
    public static function isAusbilder(Spieler $spieler): bool
    {
        return LigaleitungRepository::get()->hatFunktion($spieler, 'schiriausbilder');
    }

    public static function isSchiri(Spieler $spieler): bool
    {
        return $spieler->getSchiri() >= Config::SAISON;
    }

}
