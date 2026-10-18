<?php

namespace App\Repository\TurnierBericht;

use Doctrine\ORM\EntityRepository;
use App\Repository\DoctrineWrapper;
use App\Repository\SingletonRepositoryTrait;
use App\Entity\Turnierbericht\Zeitstrafe;

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
