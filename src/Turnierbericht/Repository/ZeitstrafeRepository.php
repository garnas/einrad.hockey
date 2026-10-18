<?php

namespace App\Turnierbericht\Repository;

use Doctrine\ORM\EntityRepository;
use App\Shared\DoctrineWrapper;
use App\Shared\SingletonRepositoryTrait;
use App\Turnierbericht\Entity\Zeitstrafe;

class ZeitstrafeRepository
{
    use SingletonRepositoryTrait;

    public EntityRepository $bericht;

    public function speichern(Zeitstrafe $strafe): void
    {
        DoctrineWrapper::manager()->persist($strafe);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(Zeitstrafe $strafe): void
    {
        DoctrineWrapper::manager()->remove($strafe);
        DoctrineWrapper::manager()->flush();
    }
}
