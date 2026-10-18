<?php

namespace App\Team\Service;

use App\Team\Entity\Team;
use App\Turnier\Entity\Turnier;
use App\Turnier\Entity\Turnieranmeldung;
use App\Shared\DoctrineWrapper;
use App\Team\Repository\TeamRepository;
use App\Turnier\Service\TurnierService;

class NLTeamService
{
    public static function findByName(string $name): ?Team
    {
        return TeamRepository::get()->findByName($name . "*");
    }

    public static function create(string $name): Team
    {
        $nlTeam = new Team();
        $nlTeam->setName($name . '*')->setLigateam("Nein");
        DoctrineWrapper::manager()->persist($nlTeam);
        return $nlTeam;
    }

    public static function findOrCreate($teamname): Team
    {
        return self::findByName($teamname) ?? self::create($teamname);
    }

    public static function getPossibleAnmeldungListe(Turnier $turnier): array
    {
        if ($turnier->isWartePhase()) {
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
        $filter = static function (Turnieranmeldung $anmeldung) {
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
