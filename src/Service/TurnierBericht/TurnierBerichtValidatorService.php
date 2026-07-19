<?php

namespace App\Service\TurnierBericht;

use Team;

class TurnierBerichtValidatorService
{
    public static function validTeam(string $teamname): bool
    {
        $team_id = Team::name_to_id($teamname);
        return Team::is_ligateam($team_id);
    }

    public static function validKaderCheck(string $check): bool
    {
        return in_array($check, ['Ja', 'Nein']);
    }
}
