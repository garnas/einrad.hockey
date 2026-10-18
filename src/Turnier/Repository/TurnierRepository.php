<?php

namespace App\Turnier\Repository;

use App\Turnier\Entity\Turnier;
use App\Turnierbericht\Entity\Turnierbericht;
use App\Turnier\Entity\Turnieranmeldung;
use App\Shared\DoctrineWrapper;
use App\Shared\SingletonRepositoryTrait;
use Config;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;

class TurnierRepository
{
    use SingletonRepositoryTrait;

    private EntityRepository $liste;
    private EntityRepository $turnier;
    private EntityRepository $bericht;

    private function __construct()
    {
        $this->turnier = DoctrineWrapper::manager()->getRepository(Turnier::class);
        $this->liste = DoctrineWrapper::manager()->getRepository(Turnieranmeldung::class);
        $this->bericht = DoctrineWrapper::manager()->getRepository(Turnierbericht::class);
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

    /**
     * @return Turnier[]|Collection
     */
    public function getKommendeTurniere(): array|Collection
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
    public function getAlleTurniere(): array|Collection
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
    public function getErgebnisTurniere(int $saison = Config::SAISON, bool $desc = true): array|Collection
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
            ->orderBy('t.datum', ($desc ? 'desc' : 'asc'))
            ->setParameter('saison', $saison)
            ->setParameter('phase', "ergebnis")
        ;

        return new ArrayCollection($query->getQuery()->execute());
    }

    /**
     * Erhalte die Turniere bei denen das Team teilnimmt und für die ein Turnierreport existiert.
     *
     * @return Turnier[]|Collection
     */
    public function getTurnierreportTurniere(int $team_id): array|Collection
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('t')
            ->from(Turnier::class, 't')
            ->leftJoin('t.liste', 'l')
            ->innerJoin('t.bericht', 'b')
            ->where('t.saison = :saison')
            ->andWhere('t.canceled = false')
            ->andWhere('l.team = :team_id')
            ->andWhere('l.liste = :liste')
            ->andWhere('t.phase IN (:phasen)')
            ->orderBy('t.datum', 'ASC')
            ->setParameter('saison', Config::SAISON)
            ->setParameter('team_id', $team_id)
            ->setParameter('liste', 'setzliste')
            ->setParameter('phasen', ['spielplan', 'ergebnis'])
        ;

        return new ArrayCollection($query->getQuery()->execute());
    }

    /**
     * Existiert ein früheres, noch nicht abgeschlossenes Turnier derselben Saison und Turnierart?
     *
     * @param Turnier $turnier
     * @param array $arten
     * @return bool
     */
    public function hatFruehesOffenesTurnier(Turnier $turnier, array $arten): bool
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('COUNT(t.id)')
            ->from(Turnier::class, 't')
            ->where('t.spieltag < :spieltag')
            ->andWhere('t.spieltag != 0')
            ->andWhere('t.art IN (:arten)')
            ->andWhere('t.saison = :saison')
            ->andWhere('t.canceled = false')
            ->andWhere('t.phase != :phase')
            ->setParameter('spieltag', $turnier->getSpieltag())
            ->setParameter('arten', $arten)
            ->setParameter('saison', $turnier->getSaison())
            ->setParameter('phase', 'ergebnis')
        ;

        return (int) $query->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * Alle nicht abgesagten Turniere der Saison, die sich in der Setzphase befinden.
     *
     * @return Turnier[]|Collection
     */
    public function getSetzphaseTurniere(int $saison = Config::SAISON): array|Collection
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('t', 'details', 'l', 'ausrichter', 'team')
            ->from(Turnier::class, 't')
            ->innerJoin('t.details', 'details')
            ->leftJoin('t.ausrichter', 'ausrichter')
            ->leftJoin('t.liste', 'l')
            ->leftJoin('l.team', 'team')
            ->where('t.phase = :phase')
            ->andWhere('t.saison = :saison')
            ->andWhere('t.canceled = false')
            ->andWhere('t.art IN (:arten)')
            ->orderBy('t.datum', 'asc')
            ->setParameter('phase', 'setz')
            ->setParameter('saison', $saison)
            ->setParameter('arten', Config::TURNIER_ARTEN)
        ;

        return new ArrayCollection($query->getQuery()->execute());
    }

    /**
     * Anmeldungen aller noch nicht abgeschlossenen Turniere der Saison
     *
     * @return Turnieranmeldung[]
     */
    public function getAnmeldungenOffeneTurniere(int $saison = Config::SAISON): array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('l', 't', 'team')
            ->from(Turnieranmeldung::class, 'l')
            ->innerJoin('l.turnier', 't')
            ->leftJoin('l.team', 'team')
            ->where('t.saison = :saison')
            ->andWhere('t.phase != :phase')
            ->orderBy('l.positionWarteliste', 'asc')
            ->setParameter('saison', $saison)
            ->setParameter('phase', 'ergebnis')
        ;

        return $query->getQuery()->execute();
    }

}
