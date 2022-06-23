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

    /**
     * @return nTeam[]
     */
    public function activeLigaTeams(): array
    {
        return $this->team->findBy(['aktiv' => 'Ja', 'ligateam' => 'Ja']);
    }

    /**
     * @return nTeam[]
     */
    public function fromTurnier(Turnier $turnier): array
    {
        return $this->team->findBy(['aktiv' => 'Ja', 'ligateam' => 'Ja']);
    }

    /**
     * @param string $name
     * @return nTeam|null
     */
    public function findByName(string $name): ?nTeam
    {
        return $this->team->findOneBy(['name' => $name]);
    }

}