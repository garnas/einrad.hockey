<?php

namespace App\Repository\Spieler;

use App\Entity\Team\Spieler;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
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

    public function findBySpieler(Spieler $spieler): ?Spieler
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('s', 't')
            ->from(Spieler::class, 's')
            ->leftJoin('s.team', 't')
            ->andWhere('s.vorname = :vorname')
            ->andWhere('s.nachname = :nachname')
            ->andWhere('s.jahrgang = :jahrgang')
            ->andWhere('s.geschlecht = :geschlecht')
            ->setParameter('vorname', $spieler->getVorname())
            ->setParameter('nachname', $spieler->getNachname())
            ->setParameter('jahrgang', $spieler->getJahrgang())
            ->setParameter('geschlecht', $spieler->getGeschlecht())
        ;
        return $query->getQuery()->getOneOrNullResult();
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