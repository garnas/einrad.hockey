<?php

namespace App\Service\Team;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\TurniereListe;
use App\Entity\Turnier\Turnier;
use App\Repository\DoctrineWrapper;
use DateTime;
use db;
class TeamService
{

    /**
     * Ermittelt, ob das Team an gleichen Kalendertag auf einem anderen Turnier angemeldet ist
     *
     * @param DateTime $date_time
     * @param nTeam $team
     * @return bool
     */
    public static function isAmKalenderTagAufSetzliste(DateTime $date_time, nTeam $team): bool
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('l.listeId')
            ->from(TurniereListe::class, 'l')
            ->innerJoin('l.turnier', 't')
            ->where('l.team = :team')
            ->andWhere('t.datum = :datum')
            ->andWhere("t.art = 'I' OR t.art = 'II'")
            ->setParameter('team', $team)
            ->setParameter('datum', $date_time)
        ;

        return count($query->getQuery()->getResult()) > 0;

    }
}