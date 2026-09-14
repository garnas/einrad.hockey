<?php

namespace App\Repository\Spielplan;

use App\Entity\Spielplan\SpielplanDetails;
use App\Entity\Spielplan\SpielplanPaarungen;
use App\Entity\Turnier\Spiel;
use App\Entity\Turnier\Turnier;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use Doctrine\ORM\EntityRepository;

/**
 * Datenzugriff für JgJ-Spielpläne: die konkreten Spiele eines Turniers sowie die
 * in der DB hinterlegten Spielplanvorlagen (spielplan_details / spielplan_paarungen).
 */
class SpielplanRepository
{
    use TraitSingletonRepository;

    private EntityRepository $spiele;
    private EntityRepository $details;
    private EntityRepository $paarungen;

    private function __construct()
    {
        $this->spiele = DoctrineWrapper::manager()->getRepository(Spiel::class);
        $this->details = DoctrineWrapper::manager()->getRepository(SpielplanDetails::class);
        $this->paarungen = DoctrineWrapper::manager()->getRepository(SpielplanPaarungen::class);
    }

    /**
     * Alle Spiele eines Turniers, nach Spiel-ID sortiert und keyed by Spiel-ID.
     * Team- und Schiri-Assoziationen werden mitgeladen (fetch join gegen N+1).
     *
     * @return array<int, Spiel>
     */
    public function getSpiele(Turnier $turnier): array
    {
        $result = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('s', 'ta', 'tb', 'sa', 'sb')
            ->from(Spiel::class, 's')
            ->innerJoin('s.teamA', 'ta')
            ->innerJoin('s.teamB', 'tb')
            ->innerJoin('s.schiriTeamA', 'sa')
            ->innerJoin('s.schiriTeamB', 'sb')
            ->where('s.turnier = :turnier')
            ->orderBy('s.spielId', 'ASC')
            ->setParameter('turnier', $turnier)
            ->getQuery()
            ->getResult();

        $spiele = [];
        foreach ($result as $spiel) {
            $spiele[$spiel->getSpielId()] = $spiel;
        }
        return $spiele;
    }

    public function getSpiel(Turnier $turnier, int $spielId): ?Spiel
    {
        return $this->spiele->findOneBy(['turnier' => $turnier, 'spielId' => $spielId]);
    }

    /**
     * Existiert (mind. ein) Spiel und damit ein dynamischer Spielplan zum Turnier?
     */
    public function hatSpielplan(int $turnierId): bool
    {
        $count = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('COUNT(s.spielId)')
            ->from(Spiel::class, 's')
            ->where('s.turnier = :turnier')
            ->setParameter('turnier', $turnierId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }

    public function getDetails(string $vorlage): ?SpielplanDetails
    {
        return $this->details->find($vorlage);
    }

    /**
     * Die Paarungen einer Spielplanvorlage, nach Spiel-ID sortiert.
     *
     * @return SpielplanPaarungen[]
     */
    public function getVorlagePaarungen(SpielplanDetails $vorlage): array
    {
        return $this->paarungen->findBy(
            ['spielplanPaarung' => $vorlage->getSpielplanPaarung()],
            ['spielId' => 'ASC'],
        );
    }

    /**
     * @param Spiel[] $spiele
     */
    public function speichereSpiele(array $spiele): void
    {
        foreach ($spiele as $spiel) {
            DoctrineWrapper::manager()->persist($spiel);
        }
        DoctrineWrapper::manager()->flush();
    }

    public function loescheSpiele(Turnier $turnier): void
    {
        DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->delete(Spiel::class, 's')
            ->where('s.turnier = :turnier')
            ->setParameter('turnier', $turnier)
            ->getQuery()
            ->execute();
    }

    public function flush(): void
    {
        DoctrineWrapper::manager()->flush();
    }
}
