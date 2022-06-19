<?php

namespace App\Repository\Turnier;

use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurnierBericht;
use App\Entity\Turnier\TurniereListe;
use App\Repository\TraitSingletonRepository;

use Doctrine\ORM\EntityRepository;
use App\Repository\DoctrineWrapper;

class TurnierRepository
{
    use TraitSingletonRepository;

    private EntityRepository $liste;
    private EntityRepository $turnier;
    private EntityRepository $bericht;

    private function __construct()
    {
        $this->liste = DoctrineWrapper::manager()->getRepository(TurniereListe::class);
        $this->turnier = DoctrineWrapper::manager()->getRepository(Turnier::class);
        $this->bericht = DoctrineWrapper::manager()->getRepository(TurnierBericht::class);
    }

    /**
     * @param int $turnier_id
     * @return TurniereListe[]
     */
    public function getSpielenliste(int $turnier_id = 1005): array
    {
        return $this->liste->findBy(['turnierId' => $turnier_id, 'liste' => 'spiele']);
    }

    public function turnier(int $turnier_id = 1005): Turnier
    {
        return $this->turnier->find($turnier_id);
    }

    public function getBericht(int $turnier_id): ?TurnierBericht
    {
        return $this->bericht->findOneBy(['turnierId' => $turnier_id]);
    }

}