<?php

namespace App\Neuigkeit\Repository;

use App\Neuigkeit\Entity\Neuigkeit;
use App\Neuigkeit\Enum\NeuigkeitArt;
use App\Shared\DoctrineWrapper;
use App\Shared\SingletonRepositoryTrait;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Collection;

class NeuigkeitRepository
{
    use SingletonRepositoryTrait;

    private EntityRepository $neuigkeit;

    private function __construct()
    {
        $this->neuigkeit = DoctrineWrapper::manager()->getRepository(Neuigkeit::class);
    }

    public function create(Neuigkeit $neuigkeit): void
    {
        DoctrineWrapper::manager()->persist($neuigkeit);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(Neuigkeit $neuigkeit): void
    {
        DoctrineWrapper::manager()->remove($neuigkeit);
        DoctrineWrapper::manager()->flush();
    }

    public function update(Neuigkeit $neuigkeit): void
    {
        DoctrineWrapper::manager()->flush();
    }

    public function findById(int $id): Neuigkeit
    {
        return $this->neuigkeit->find($id);
    }

    public function findAll(): array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('n')
            ->from(Neuigkeit::class, 'n')
            ->orderBy('n.zeit', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function findActiveBeforeNow(): array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('n')
            ->from(Neuigkeit::class, 'n')
            ->where('n.zeit <= :now')
            ->andWhere('n.aktiv = :aktiv')
            ->setParameter('aktiv', true)
            ->setParameter('now', new DateTime())
            ->orderBy('n.zeit', 'DESC');

        return $query->getQuery()->getResult();
    }
    public function findAllBeforeNow(): array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('n')
            ->from(Neuigkeit::class, 'n')
            ->where('n.zeit <= :now')
            ->setParameter('now', new DateTime())
            ->orderBy('n.zeit', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function findActive(): array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('n')
            ->from(Neuigkeit::class, 'n')
            ->where('n.aktiv = :aktiv')
            ->setParameter('aktiv', true)
            ->orderBy('n.zeit', 'DESC');

        return $query->getQuery()->getResult();
    }

    public function findByType(NeuigkeitArt $art): array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('n')
            ->from(Neuigkeit::class, 'n')
            ->where('n.art = :art')
            ->setParameter('art', $art)
            ->orderBy('n.zeit', 'DESC');

        return $query->getQuery()->getResult();
    }

}
