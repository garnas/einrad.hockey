<?php

namespace App\Service\Team;

use App\Entity\Team\nTeam;
use App\Repository\Team\TeamRepository;

class NLTeamService
{
    public static function findByName(string $name): nTeam
    {
        TeamRepository::get()->findByName($name . "*");
    }

    public static function create(string $teamname): nTeam
    {
        $nlTeam = new nTeam();
        $nlTeam->setName($teamname)
            ->setLigateam("Nein");
    }

}