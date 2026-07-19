<?php

namespace App\Repository\TurnierBericht;

use Doctrine\ORM\EntityRepository;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use App\Entity\TurnierBericht\SpielerAusleihe;

class SpielerAusleiheRepository
{
    use TraitSingletonRepository;

    public EntityRepository $bericht;

    public function speichern(SpielerAusleihe $leihe): void
    {
        DoctrineWrapper::manager()->persist($leihe);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(SpielerAusleihe $leihe): void
    {
        DoctrineWrapper::manager()->remove($leihe);
        DoctrineWrapper::manager()->flush();
    }
}
