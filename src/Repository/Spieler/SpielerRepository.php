<?php

namespace App\Repository\Spieler;

use App\Entity\Team\Spieler;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use Config;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

class SpielerRepository
{
    use TraitSingletonRepository;

    private EntityRepository $spieler;

    private function __construct()
    {
        $this->spieler = DoctrineWrapper::manager()->getRepository(Spieler::class);
    }

    public function spieler(int $id): ?Spieler
    {
        return $this->spieler->find($id);
    }

    /**
     * Spieler anderer Teams, die noch nicht in der aktuellen Saison gemeldet sind (für die Übernahme ins eigene Team)
     * @return Collection|Spieler[]
     */
    public function findUebernehmbareSpieler(int $ausschlussTeamId): Collection|array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('s', 't')
            ->from(Spieler::class, 's')
            ->leftJoin('s.team', 't')
            ->andWhere('s.letzteSaison < :saison')
            ->andWhere('s.letzteSaison >= (:saison - 2)')
            ->andWhere('t.id IS NULL OR t.id != :teamId')
            ->orderBy('s.nachname', 'ASC')
            ->addOrderBy('s.vorname', 'ASC')
            ->setParameter('saison', Config::SAISON)
            ->setParameter('teamId', $ausschlussTeamId)
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * @return Collection|Spieler[]
     */
    public function getSpielerAndTeam(): Collection|array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('s')
            ->from(Spieler::class, 's')
            ->leftJoin('s.team', 't')
        ;
        return $query->getQuery()->getResult();
    }

    public function speichern(Spieler $spieler): void
    {
        DoctrineWrapper::manager()->persist($spieler);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(Spieler $spieler): void
    {
        DoctrineWrapper::manager()->remove($spieler);
        DoctrineWrapper::manager()->flush();
    }

}
