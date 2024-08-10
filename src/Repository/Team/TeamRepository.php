<?php

namespace App\Repository\Team;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Repository\TraitSingletonRepository;
use App\Repository\DoctrineWrapper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Config;

class TeamRepository
{

    use TraitSingletonRepository;

    private EntityRepository $team;

    private function __construct()
    {
        $this->team = DoctrineWrapper::manager()->getRepository(nTeam::class);
    }

    public function team(int $id): nTeam
    {
        return $this->team->find($id);
    }

    /**
     * @param nTeam $team
     * @return Collection|Turnier[]
     */
    public static function getAusrichterTurniere(nTeam $team): Collection|array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('t', 'details', 'l')
            ->from(Turnier::class, 't')
            ->innerJoin('t.details', 'details')
            ->innerJoin('t.ausrichter', 'ausrichter')
            ->leftJoin('t.liste', 'l')
            ->andWhere('t.ausrichter = :ausrichter')
            ->andWhere('t.saison = :saison')
            ->orderBy('t.canceled', 'asc')
            ->addOrderBy('t.datum', 'asc')
            ->setParameter('saison', Config::SAISON)
            ->setParameter('ausrichter', $team)
        ;

        $sort = static function(Turnier $turnier) {
            if ($turnier->isCanceled()) {
                return 1;
            }
            if ($turnier->isErgebnisPhase()) {
                return 1;
            }
            return -1;
        };

        $result = $query->getQuery()->getResult();
        uasort($result, $sort);

        return new ArrayCollection($result);
    }

    /**
     * @return nTeam[]
     */
    public function activeLigaTeams(): array //TODO 1+n query
    {
        return $this->team->findBy(['aktiv' => 'Ja', 'ligateam' => 'Ja']);
    }

    public function speichern(nTeam $team): void
    {
        DoctrineWrapper::manager()->persist($team);
        DoctrineWrapper::manager()->flush();
    }

    public function delete(nTeam $team): void
    {
        DoctrineWrapper::manager()->remove($team);
        DoctrineWrapper::manager()->flush();
    }

    /**
     * @param string $name
     * @return nTeam|null
     */
    public function findByName(string $name): ?nTeam
    {
        return $this->team->findOneBy(['name' => $name]);
    }

    public function deleteFoto(nTeam $team): void
    {
        $teamfoto = $team->getDetails()->getTeamfoto();
        $team->getDetails()->setTeamfoto(null);
        $this->speichern($team);

        # Datei löschen
        if (file_exists($teamfoto)) {
            unlink($teamfoto);
        }
    }

}