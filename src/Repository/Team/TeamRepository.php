<?php

namespace App\Repository\Team;

use App\Entity\Team\nTeam;
use App\Repository\TraitSingletonRepository;
use App\Repository\DoctrineWrapper;
use Doctrine\ORM\EntityRepository;

class TeamRepository
{

    use TraitSingletonRepository;

    private EntityRepository $team;

    private function __construct()
    {
        $this->team = DoctrineWrapper::manager()->getRepository(nTeam::class);
    }

    public function team(int $id): nTeam
    {
        return $this->team->find($id);
    }

}