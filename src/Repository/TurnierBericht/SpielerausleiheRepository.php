<?php

namespace App\Repository\TurnierBericht;

use Doctrine\ORM\EntityRepository;
use App\Repository\DoctrineWrapper;
use App\Repository\SingletonRepositoryTrait;
use App\Entity\Turnierbericht\Spielerausleihe;

class SpielerausleiheRepository
{
    use SingletonRepositoryTrait;

    public EntityRepository $bericht;

    public function speichern(Spielerausleihe $leihe): void
    {
        DoctrineWrapper::manager()->persist($leihe);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(Spielerausleihe $leihe): void
    {
        DoctrineWrapper::manager()->remove($leihe);
        DoctrineWrapper::manager()->flush();
    }
}
