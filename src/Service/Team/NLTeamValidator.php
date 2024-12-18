<?php

namespace App\Service\Team;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Service\Turnier\TurnierService;
use Helper;
use Html;

class NLTeamValidator
{

    public static function isValidNLAnmeldungListe(Turnier $turnier, $liste): bool
    {
        return in_array($liste, NLTeamService::getPossibleAnmeldungListe($turnier), true);
    }

    public static function isValidNLAnmeldung(nTeam $team, Turnier $turnier, string $liste): bool
    {
        if ($turnier->isSpielplanPhase() || $turnier->isErgebnisPhase()) {
            Html::notice("Anmeldung ist schon geschlossen.");
            return false;
        }
        if (Helper::$teamcenter && !self::isValidNLAnmeldungListe($turnier, $liste)) {
            Html::notice("Falsche Liste fürs NL-Team");
            return false;
        }
        if (TeamService::isAngemeldet($team, $turnier)) {
            Html::notice("Das Nichtligateam ist bereits angemeldet.");
            return false;
        }
        if (
            $liste === "setzliste"
            && !TurnierService::hasFreieSetzPlaetze($turnier)
        ) {
            Html::notice("Die Setzliste ist bereits voll.");
            return false;
        }
        return true;
    }
}