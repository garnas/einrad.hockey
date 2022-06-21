<?php

namespace App\Service\Turnier;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use Config;
use Db;

class TurnierService
{

    public static function isLigaTurnier(Turnier $turnier): bool
    {
        return in_array($turnier->getArt(), Config::TURNIER_ARTEN, true);
    }

    public static function getTurnierEintrageFrist(Turnier $turnier): int
    {
        $unix = $turnier->getDatum()->getTimestamp();

        $tag = date("N", $unix); // Numerische Zahl des Wochentages 1-7
        // Faktor 3.93 und strtotime(date("d-M-Y"..)) -> Reset von 12 Uhr Mittags auf Null Uhr, um Winter <-> Sommerzeit korrekt handzuhaben

        if ($tag >= 3) {
            return strtotime(date("d-M-Y", $unix - 3.93 * 7 * 24 * 60 * 60 + (6 - $tag) * 24 * 60 * 60));
        }

        return strtotime(date("d-M-Y", $unix - 3.93 * 7 * 24 * 60 * 60 - $tag * 24 * 60 * 60));
    }

    public static function isSpielBerechtigt(Turnier $turnier, nTeam $team): bool
    {
        if (!$team->isLigaTeam()) {
            return true;
        }

        return BlockService::isBlockPassend($turnier, $team);
    }

    public static function aufSetzListe(Turnier $turnier, nTeam $team): void
    {
        $anmeldung = new TurniereListe();
        $anmeldung->setTeam($team)
            ->setListe('setz')
            ->setTurnier($turnier)
            ->setFreilosGesetzt('Nein');
        db::debug($turnier->getListe()->count());
         $turnier->getListe()->add($anmeldung);
        db::debug($turnier->getListe()->count());

    }

}