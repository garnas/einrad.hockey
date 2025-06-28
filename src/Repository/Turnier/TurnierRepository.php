<?php

namespace App\Repository\Turnier;

use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurnierBericht;
use App\Entity\Turnier\TurniereListe;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use Config;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

class TurnierRepository
{
    use TraitSingletonRepository;

    public EntityRepository $liste;
    public EntityRepository $turnier;
    public EntityRepository $bericht;

    private function __construct()
    {
        $this->turnier = DoctrineWrapper::manager()->getRepository(Turnier::class);
        $this->liste = DoctrineWrapper::manager()->getRepository(TurniereListe::class);
        $this->bericht = DoctrineWrapper::manager()->getRepository(TurnierBericht::class);
    }

    /**
     * @param Turnier $turnier
     * @return TurniereListe[]
     */
    public function getWarteListe(Turnier $turnier): array
    {
        return $this->liste->findBy(['turnier' => $turnier, 'liste' => 'warteliste'], ['positionWarteliste' => 'ASC']);
    }

    public function turnier(int $turnier_id = 1005): ?Turnier
    {
        return $this->turnier->find($turnier_id);
    }

    public function speichern(Turnier $turnier): void
    {
        DoctrineWrapper::manager()->persist($turnier);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(Turnier $turnier): void
    {
        DoctrineWrapper::manager()->remove($turnier);
        DoctrineWrapper::manager()->flush();
    }

    public function getBericht(int $turnier_id): ?TurnierBericht
    {
        return $this->bericht->findOneBy(['turnierId' => $turnier_id]);
    }

    /**
     * @return Turnier[]|Collection
     */
    public static function getKommendeTurniere(): array|Collection
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('t', 'details', 'l', 'ausrichter', 'team')
            ->from(Turnier::class, 't')
            ->innerJoin('t.details', 'details')
            ->leftJoin('t.ausrichter', 'ausrichter')
            ->leftJoin('t.liste', 'l')
            ->leftJoin('l.team', 'team')
            ->where('t.phase != :phase')
            ->andWhere('t.canceled = 0')
            ->andWhere('t.saison = :saison')
            ->orderBy('t.datum', 'asc')
            ->setParameter('phase', 'ergebnis')
            ->setParameter('saison', Config::SAISON)
        ;

        return new ArrayCollection($query->getQuery()->execute());
    }

    /**
     * @return Turnier[]|Collection
     */
    public static function getAlleTurniere(): array|Collection
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('t', 'details', 'l', 'ausrichter', 'team')
            ->from(Turnier::class, 't')
            ->innerJoin('t.details', 'details')
            ->leftJoin('t.ausrichter', 'ausrichter')
            ->leftJoin('t.liste', 'l')
            ->leftJoin('l.team', 'team')
            ->where('t.saison = :saison')
            ->orderBy('t.datum', 'asc')
            ->setParameter('saison', Config::SAISON)
        ;

        return new ArrayCollection($query->getQuery()->execute());
    }

    /**
     * @return Turnier[]|Collection
     */
    public static function getErgebnisTurniere(int $saison = Config::SAISON): array|Collection
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('t', 'details', 'l', 'ausrichter', 'team')
            ->from(Turnier::class, 't')
            ->innerJoin('t.details', 'details')
            ->leftJoin('t.ausrichter', 'ausrichter')
            ->leftJoin('t.liste', 'l')
            ->leftJoin('l.team', 'team')
            ->where('t.saison = :saison')
            ->andWhere('t.phase = :phase')
            ->orderBy('t.datum', 'desc')
            ->setParameter('saison', $saison)
            ->setParameter('phase', "ergebnis")
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * @return Turnier[]|Collection
     */
    public static function getSetzlisteTurniere(int $team_id): array|Collection
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('t')
            ->from(Turnier::class, 't')
            ->leftJoin('t.liste', 'l')
            ->where('t.saison = :saison')
            ->andWhere('t.canceled = false')
            ->andWhere('l.team = :team_id')
            ->andWhere('l.liste = :liste')
            ->orderBy('t.datum', 'ASC')
            ->setParameter('saison', Config::SAISON)
            ->setParameter('team_id', $team_id)
            ->setParameter('liste', 'setzliste')
        ;

        return new ArrayCollection($query->getQuery()->execute());
    }



    public function last_turnier(int $ausrichter_id): ?Turnier
    {
        return $this->turnier->findOneBy(['ausrichter' => $ausrichter_id], ['id' => 'DESC']);
    }

}