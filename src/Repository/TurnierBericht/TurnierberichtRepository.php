<?php

namespace App\Repository\TurnierBericht;

use Doctrine\ORM\EntityRepository;
use App\Repository\DoctrineWrapper;
use App\Repository\SingletonRepositoryTrait;
use App\Entity\Turnierbericht\Turnierbericht;

class TurnierberichtRepository
{
    use SingletonRepositoryTrait;

    private EntityRepository $bericht;

    private function __construct()
    {
        $this->bericht = DoctrineWrapper::manager()->getRepository(Turnierbericht::class);
    }

    public function bericht(int $turnier_id): ?Turnierbericht
    {
        return $this->bericht->findOneBy(['turnier' => $turnier_id]);
    }

    public function speichern(Turnierbericht $bericht): void
    {
        DoctrineWrapper::manager()->persist($bericht);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(Turnierbericht $bericht): void
    {
        DoctrineWrapper::manager()->remove($bericht);
        DoctrineWrapper::manager()->flush();
    }
}
