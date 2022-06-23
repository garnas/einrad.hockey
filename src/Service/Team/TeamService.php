<?php

namespace App\Service\Team;

use App\Entity\Team\Kontakt;
use App\Entity\Team\nTeam;
use App\Entity\Team\Spieler;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;
use Config;
use Doctrine\Common\Collections\Collection;

class TeamService
{

    /**
     * @param nTeam[] $teams
     * @return Kontakt[]
     */
    public static function getEmails(array $teams): array
    {
        foreach ($teams as $team) {
            $emails[] = $team->getEmails();
        }
        return $emails ?? [];
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

    /**
     * @param nTeam $team
     * @return Spieler[]
     */
    public static function getAktiveSpieler(nTeam $team): Collection
    {
        $filter = static function (Spieler $spieler) {
            return $spieler->getLetzteSaison() === Config::SAISON;
        };
        return $team->getKader()->filter($filter);
    }

    public static function getAnzahlAktiveSpieler(nTeam $team): int
    {
        return self::getAktiveSpieler($team)->count();
    }

    public static function anmelden(nTeam $team, Turnier $turnier)
    {
        if (
            !TurnierService::hasFreieSetzPlaetze($turnier)
            || $turnier->isWartePhase()
        ) {
            TurnierService::addToWarteListe($turnier, $team);
        }
        if ($turnier->isSetzPhase()) {
            TurnierService::addToSetzListe($turnier, $team);
        }
    }

    public static function freilos(nTeam $team, Turnier $turnier): void
    {
        if (self::isAufWarteliste($team, $turnier)) {
            self::abmelden($team, $turnier);
        }
        TurnierService::addToSetzListe($turnier, $team);
        $freilose = $team->getFreilose();
        $team->setFreilose($freilose - 1);
    }

    public static function abmelden(nTeam $team, Turnier $turnier): void
    {
        foreach ($turnier->getListe() as $anmeldung) {
            if ($anmeldung->getTeam()->id() === $team->id()) {
                $turnier->getListe()->removeElement($anmeldung);
                $liste = TurnierSnippets::translate($anmeldung->getListe());
                $name = $team->getName();
                $turnier->getLogService()->addLog("Abmeldung: $name von der $liste");
            }
        }

        if ($turnier->isSetzPhase()) {
            TurnierService::setzListeAuffuellen($turnier);
        }

    }

    public static function isAufWarteliste(nTeam $team, Turnier $turnier): bool
    {
        $predicate = static function (int $key, TurniereListe $anmeldung) use ($team) {
            return $anmeldung->getTeam() === $team;
        };
        return TurnierService::getWarteliste($turnier)->exists($predicate);
    }

    public static function isAufSetzliste(nTeam $team, Turnier $turnier): bool
    {
        $predicate = static function (int $key, TurniereListe $anmeldung) use ($team) {
            return $anmeldung->getTeam() === $team;
        };
        return TurnierService::getSetzListe($turnier)->exists($predicate);
    }

    public static function isAngemeldet(nTeam $team, Turnier $turnier): bool
    {
        $predicate = static function (int $key, TurniereListe $anmeldung) use ($turnier) {
            return $anmeldung->getTurnier() === $turnier;
        };
        return $team->getTurniereListe()->exists($predicate);
    }


}