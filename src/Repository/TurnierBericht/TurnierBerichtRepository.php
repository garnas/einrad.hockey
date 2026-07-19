<?php

namespace App\Repository\TurnierBericht;

use Doctrine\ORM\EntityRepository;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use App\Entity\TurnierBericht\TurnierBericht;

class TurnierBerichtRepository
{
    use TraitSingletonRepository;

    public EntityRepository $bericht;

    private function __construct()
    {
        $this->bericht = DoctrineWrapper::manager()->getRepository(TurnierBericht::class);
    }

    public function bericht(int $turnier_id): ?TurnierBericht
    {
        return $this->bericht->findOneBy(['turnier' => $turnier_id]);
    }

    public function speichern(TurnierBericht $bericht): void
    {
        DoctrineWrapper::manager()->persist($bericht);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(TurnierBericht $bericht): void
    {
        DoctrineWrapper::manager()->remove($bericht);
        DoctrineWrapper::manager()->flush();
    }
}
