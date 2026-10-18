<?php

namespace App\Repository\Ligaleitung;

use App\Entity\Ligaleitung\Ligaleitung;
use App\Repository\DoctrineWrapper;
use App\Repository\SingletonRepositoryTrait;
use Doctrine\ORM\EntityRepository;

class LigaleitungRepository
{
    use SingletonRepositoryTrait;

    private EntityRepository $ligaleitung;

    private function __construct()
    {
        $this->ligaleitung = DoctrineWrapper::manager()->getRepository(Ligaleitung::class);
    }

    public function ligaleitung(int $id): ?Ligaleitung
    {
        return $this->ligaleitung->find($id);
    }

    public function findByLogin(string $login): ?Ligaleitung
    {
        return $this->ligaleitung->findOneBy(['login' => $login]);
    }

    /**
     * Mitglieder der Ligaleitung mit der übergebenen Funktion, sortiert nach Vorname
     *
     * @return Ligaleitung[]
     */
    public function findByFunktion(string $funktion): array
    {
        return DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('l', 's', 't')
            ->from(Ligaleitung::class, 'l')
            ->innerJoin('l.spieler', 's')
            ->leftJoin('s.team', 't')
            ->where('l.funktion = :funktion')
            ->orderBy('s.vorname', 'ASC')
            ->setParameter('funktion', $funktion)
            ->getQuery()
            ->getResult();
    }

    public function speichern(Ligaleitung $ligaleitung): void
    {
        DoctrineWrapper::manager()->persist($ligaleitung);
        DoctrineWrapper::manager()->flush();
    }

}
