<?php

namespace App\Turnierbericht\Service;

use App\Team\Repository\TeamRepository;
use App\Team\Service\TeamService;
use App\Turnier\Entity\Turnier;

class TurnierberichtValidator
{
    public static function validTeam(string $teamname, Turnier $turnier): bool
    {
        $team = TeamRepository::get()->findByName($teamname);
        return $team !== null && TeamService::isAufSetzliste($team, $turnier);
    }

    public static function validKaderCheck(string $check): bool
    {
        return in_array($check, ['Ja', 'Nein']);
    }
}
