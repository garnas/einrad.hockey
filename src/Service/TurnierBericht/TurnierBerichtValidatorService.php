<?php

namespace App\Service\TurnierBericht;

use App\Repository\Team\TeamRepository;
use App\Service\Team\TeamService;

class TurnierBerichtValidatorService
{
    public static function validTeam(string $teamname): bool
    {
        $team = TeamRepository::get()->findByName($teamname);
        return (bool) $team?->isLigaTeam();
    }

    public static function validKaderCheck(string $check): bool
    {
        return in_array($check, ['Ja', 'Nein']);
    }
}
