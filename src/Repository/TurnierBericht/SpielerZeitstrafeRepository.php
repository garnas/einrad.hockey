<?php

namespace App\Repository\TurnierBericht;

use Doctrine\ORM\EntityRepository;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use App\Entity\TurnierBericht\SpielerZeitstrafe;

class SpielerZeitstrafeRepository
{
    use TraitSingletonRepository;

    public EntityRepository $bericht;

    public function speichern(SpielerZeitstrafe $strafe): void
    {
        DoctrineWrapper::manager()->persist($strafe);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(SpielerZeitstrafe $strafe): void
    {
        DoctrineWrapper::manager()->remove($strafe);
        DoctrineWrapper::manager()->flush();
    }
}
