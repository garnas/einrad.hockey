<?php

namespace App\Repository\Neuigkeit;

use App\Entity\Sonstiges\Neuigkeit;
use App\Enum\NeuigkeitArt;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\Collection;

class NeuigkeitRepository
{
    
    use TraitSingletonRepository;

    private EntityRepository $neuigkeit;

    private function __construct()
    {
        $this->neuigkeit = DoctrineWrapper::manager()->getRepository(Neuigkeit::class);
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