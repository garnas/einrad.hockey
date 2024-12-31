<?php

namespace App\Service\Team;

use App\Entity\Team\Freilos;
use App\Entity\Team\FreilosGrund;
use App\Entity\Team\Kontakt;
use App\Entity\Team\nTeam;
use App\Entity\Team\Spieler;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Repository\Team\TeamRepository;
use App\Service\Turnier\BlockService;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;
use Config;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Html;
use MailBot;

class TeamService
{

    public static function getPublicEmailsAsString(nTeam $team): string
    {
        $filter = static function (Kontakt $kontakt) {
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
     * @return Spieler[]|Collection
     */
    public static function getAktiveSpieler(nTeam $team): Collection|array
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

    public static function anmelden(nTeam $team, Turnier $turnier): void
    {
        if (
            $turnier->isSetzPhase()
            && TurnierService::isSetzBerechtigt($turnier, $team)
            && TurnierService::hasFreieSetzPlaetze($turnier)
        ) {
            TurnierService::addToSetzListe($turnier, $team);
        } else {
            TurnierService::addToWarteListe($turnier, $team);
        }
    }

    public static function freilos(nTeam $team, Turnier $turnier): void
    {
        if (self::isAufWarteliste($team, $turnier)) {
            $anmeldung = $team->getTurniereListe()->get($turnier->id());
        } else {
            $anmeldung = new TurniereListe();
            $anmeldung->setTeam($team)->setTurnier($turnier);
        }

        $anmeldung
            ->setListe('setzliste')
            ->setFreilosGesetzt('Ja')
            ->setFreilosGesetztAm(new DateTime());
        $team->getNextFreilos()->setzen($turnier);
        $turnier->getListe()->add($anmeldung);
        $turnier->getLogService()->addLog("Freilos: " . $team->getName() . " " . BlockService::toString($team->getBlock()));
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
            TurnierService::neueWartelistePositionen($turnier);
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

    /**
     * @param nTeam $team
     * @return Collection|TurniereListe[]
     */
    public static function getGesetzteFreilose(nTeam $team): Collection|array
    {
        $filter = static function (TurniereListe $anmeldung) {
            return ($anmeldung->getTurnier()->getSaison() == Config::SAISON
                && $anmeldung->getFreilosGesetzt() === "Ja");
        };
        return $team->getTurniereListe()->filter($filter);
    }

    /**
     * @param nTeam $team
     * @return Collection|TurniereListe[]
     */
    public static function getEingetrageneTurniere(nTeam $team): Collection|array
    {
        $filter = static function (Turnier $turnier) {
            return ($turnier->getSaison() === Config::SAISON);
        };
        return $team->getAusgerichteteTurniere()->filter($filter);
    }

    /**
     * @param Turnier $turnier
     * @return bool
     */
    public static function isAusrichterFreilosBerechtigt(Turnier $turnier): bool
    {
        $erstelltAmUnix = $turnier->getErstelltAm()->getTimestamp();
        $datumAmUnix = $turnier->getDatum()->getTimestamp();
        return !$turnier->isCanceled()
            && ($turnier->isFinalTurnier() || $turnier->isLigaturnier())
            && ($datumAmUnix - $erstelltAmUnix) >= 8 * 7 * 24 * 60 * 60;
    }

    /**
     * @param TurniereListe $anmeldung
     * @return bool
     */
    public static function isFreilosRecyclebar(TurniereListe $anmeldung): bool
    {
        $freilosGesetztUnix = $anmeldung->getFreilosGesetztAm()->getTimestamp();
        $turnierDatumUnix = $anmeldung->getTurnier()->getDatum()->getTimestamp();
        return $turnierDatumUnix - $freilosGesetztUnix >= 8 * 7 * 24 * 60 * 60;
    }

    public static function handleSchiriFreilos(nTeam $team, bool $sendMail = True): bool
    {
        $filter = static function (Spieler $s) {
            return ($s->getLetzteSaison() == Config::SAISON && $s->getSchiri() >= Config::SAISON);
        };
        $aktiveSchiris = $team->getKader()->filter($filter);

        if (!TeamValidator::hasSchiriFreilosErhalten($team) && $aktiveSchiris->count() >= 2) {
            $team->addFreilos(FreilosGrund::SCHIRI);
            if ($sendMail) {
                Mailbot::mail_schiri_freilos($team);
            }
            return True;
        }
        return False;
    }

    public static function hasZweiAusgerichteteTurnierFreilose(nTeam $team): bool
    {
        $filter = static function (Freilos $f) {
            return ($f->getGrund() == FreilosGrund::TURNIER_AUSGERICHTET);
        };
        $erhalteneFreilose = $team->getFreiloseBySaison()->filter($filter);
        return $erhalteneFreilose->count() >= 2;
    }

    public static function handleAusgerichtetesTurnierFreilos(Turnier $turnier, bool $sendMail = True): bool
    {
        $team = $turnier->getAusrichter();
        if (
            $turnier->isErgebnisPhase()
            && $turnier->getSaison() == Config::SAISON
            && self::isAusrichterFreilosBerechtigt($turnier)
            && !self::hasFreilosForAusgerichtetesTurnier($team, $turnier)
            && !self::hasZweiAusgerichteteTurnierFreilose($team)
        ) {
            $team->addFreilos(
                grund: FreilosGrund::TURNIER_AUSGERICHTET,
                turnierAusgerichtet: $turnier
            );
            Html::info("Freilos" . $turnier->id() . " für " . $team->getName());
            TeamRepository::get()->speichern($team);
            if ($sendMail) {
                Mailbot::mail_ausrichter_freilos($team);
            }
            return true;
        }
        return false;
    }

    private static function hasFreilosForAusgerichtetesTurnier(nTeam $team, Turnier $turnier): bool
    {
        $tunier_id = $turnier->id();
        $filter = static function (Freilos $f) use ($tunier_id) {
            return ($f->getTurnierAusgerichtet() !== null && $f->getTurnierAusgerichtet()->id() == $tunier_id);
        };
        return $team->getFreiloseBySaison()->filter($filter)->count() > 0;
    }

    public static function update_bestehender_turnier_ausgerichtet_freilose(): void
    {
        $teams = TeamRepository::get()->activeLigaTeams();
        foreach ($teams as $team) {
            $filter = static function (Freilos $f) {
                return ($f->getGrund() == FreilosGrund::TURNIER_AUSGERICHTET);
            };
            /** @var Freilos[] $erhalteneFreilose */
            $erhalteneFreilose = $team->getFreiloseBySaison()->filter($filter);

            foreach ($erhalteneFreilose as $f) {
                $filter = static function (Turnier $t) {
                    return ($t->isErgebnisPhase() && $t->getSaison() == Config::SAISON);
                };
                /** @var Turnier[] $erhalteneFreilose */
                $turniere = $team->getAusgerichteteTurniere()->filter($filter);
                foreach ($turniere as $turnier) {
                    if (self::isAusrichterFreilosBerechtigt($turnier)) {
                        $f->setTurnierAusgerichtet($turnier);
                        Html::info("Freilos für turnier " . $turnier->id());
                        TeamRepository::get()->speichern($team);
                    }
                }
            }
        }
    }
}