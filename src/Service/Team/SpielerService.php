<?php

namespace App\Service\Team;

use App\Entity\Team\Spieler;
use db;

class SpielerService
{


    public static function isAusbilder(Spieler $spieler): bool
    {
        $sql = "
                SELECT * 
                FROM ligaleitung 
                WHERE spieler_id = ?
                AND funktion = 'schiriausbilder'
                ";
        return db::$db->query($sql, $spieler->getSpielerId())->affected_rows() > 0;
    }

}