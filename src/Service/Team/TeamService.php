<?php

namespace App\Service\Team;

use App\Entity\Team\Kontakt;
use App\Entity\Team\nTeam;
use App\Entity\Turnier\TurniereListe;
use App\Entity\Turnier\Turnier;
use App\Repository\DoctrineWrapper;
use DateTime;
use db;
use Doctrine\Common\Collections\Collection;

class TeamService
{

    /**
     * Ermittelt, ob das Team an gleichen Kalendertag auf einem anderen Turnier angemeldet ist
     *
     * @param DateTime $date_time
     * @param nTeam $team
     * @return bool
     */
    public static function isAmKalenderTagAufSetzliste(DateTime $date_time, nTeam $team): bool //TODO ins repo
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('l.listeId')
            ->from(TurniereListe::class, 'l')
            ->innerJoin('l.turnier', 't')
            ->where('l.team = :team')
            ->andWhere('t.datum = :datum')
            ->andWhere("t.art = 'I' OR t.art = 'II'")
            ->andWhere("t.canceled == false")
            ->setParameter('team', $team)
            ->setParameter('datum', $date_time)
        ;

        return count($query->getQuery()->getResult()) > 0;
    }

    /**
     * @param nTeam[] $teams
     * @return Kontakt[]
     */
    public static function getEmails(array $teams): array
    {
        foreach ($teams as $team) {
            $emails[] = $team->getEmails();
        }
        return $emails;
    }

    public static function getPublicEmailsAsString(nTeam $team): string
    {
        $filter = static function(Kontakt $kontakt){
            return $kontakt->getPublic() === "Ja";
        };

        $emails = $team->getEmails()->filter($filter)->toArray();
        foreach ($emails as $email) {
            $array[] = $email->getEmail();
        }
        if (isset($array)) {
            return implode(",", $array);
        }
        return "";
    }

}