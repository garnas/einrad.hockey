<?php

namespace App\Service\Turnierbericht;

use App\Repository\Team\TeamRepository;

class TurnierberichtValidator
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
