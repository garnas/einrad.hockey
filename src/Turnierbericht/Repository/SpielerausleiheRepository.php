<?php

namespace App\Turnierbericht\Repository;

use Doctrine\ORM\EntityRepository;
use App\Shared\DoctrineWrapper;
use App\Shared\SingletonRepositoryTrait;
use App\Turnierbericht\Entity\Spielerausleihe;

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
