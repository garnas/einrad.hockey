<?php

namespace App\Service\Team;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Repository\DoctrineWrapper;
use App\Repository\Team\TeamRepository;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;

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
            if (self::hasNLTeamAufSetzliste($turnier) || !TurnierService::hasFreieSetzPlaetze($turnier)) {
                return ['warteliste'];
            }
            return ['warteliste', 'setzliste'];
        }
        if (
            TurnierService::hasFreieSetzPlaetze($turnier)
            && (($turnier->getDetails()->getPlaetze() - self::getAnzahlNlTeamsAufSetzliste($turnier) - 1) >= 4)
            && (self::getAnzahlNlTeamsAufSetzliste($turnier) < 3)
        ) {
            return ['setzliste'];
        }
        return ['warteliste'];
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

    public static function getAnzahlNlTeamsAufSetzliste(Turnier $turnier): int
    {
        $liste = TurnierService::getSetzListe($turnier);
        $anzahl = 0;
        foreach ($liste as $anmeldung) {
            if (!$anmeldung->getTeam()->isLigaTeam()) {
                ++$anzahl;
            }
        }
        return $anzahl;
    }

}