<?php

namespace App\Service\Team;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Repository\DoctrineWrapper;
use App\Repository\Team\TeamRepository;
use App\Service\Turnier\TurnierService;

class NLTeamService
{
    public static function findByName(string $name): nTeam|null
    {
        return TeamRepository::get()->findByName($name . "*");
    }

    public static function create(string $name): nTeam
    {
        $nlTeam = new nTeam();
        $nlTeam->setName($name . '*')->setLigateam("Nein");
        DoctrineWrapper::manager()->persist($nlTeam);
        return $nlTeam;
    }

    public static function findOrCreate($teamname): nTeam
    {
        return self::findByName($teamname) ?? self::create($teamname);
    }

    public static function getPossibleAnmeldungListe(Turnier $turnier): array
    {
        if($turnier->isWartePhase()) {
            if (self::hasNLTeamAufSetzliste($turnier)) {
                return ['warteliste'];
            }
            return ['warteliste', 'setzliste'];
        }
        return ['setzliste'];
    }

    public static function countNLTeams(Turnier $turnier): int
    {
        $filter = static function (TurniereListe $anmeldung) {
            return !$anmeldung->getTeam()->isLigaTeam();
        };
        return $turnier->getListe()->filter($filter)->count();
    }

    public static function hasNLTeamAufSetzliste(Turnier $turnier): bool
    {
        $liste = TurnierService::getSetzListe($turnier);
        foreach ($liste as $anmeldung) {
            if (!$anmeldung->getTeam()->isLigaTeam()) {
                return true;
            }
        }
        return false;
    }

}