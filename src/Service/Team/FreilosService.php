<?php

namespace App\Service\Team;

use App\Entity\Team\Freilos;
use App\Entity\Team\FreilosGrund;
use App\Entity\Team\nTeam;
use App\Entity\Team\Spieler;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Repository\Team\TeamRepository;
use App\Service\Turnier\BlockService;
use Config;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Html;
use MailBot;

class FreilosService
{

    public static function hasAusrichterFreilosForAusgerichtetesTurnier(Turnier $turnier): bool
    {
        $tunier_id = $turnier->id();
        $filter = static function (Freilos $f) use ($tunier_id) {
            return ($f->getTurnierAusgerichtet() !== null && $f->getTurnierAusgerichtet()->id() == $tunier_id);
        };
        return $turnier->getAusrichter()->getFreiloseBySaison()->filter($filter)->count() > 0;
    }

    public static function validateAusgerichtetesTurnierFreilos(Turnier $turnier): bool
    {
        $team = $turnier->getAusrichter();
        return (
            $turnier->getSaison() == Config::SAISON
            && self::isAusrichterFreilosBerechtigt($turnier)
            && !self::hasAusrichterFreilosForAusgerichtetesTurnier($turnier)
            && !self::hasZweiAusgerichteteTurnierFreilose($team)
        );
    }
    public static function handleAusgerichtetesTurnierFreilos(Turnier $turnier, bool $sendMail = True): bool
    {
        $team = $turnier->getAusrichter();
        if (
            $turnier->isErgebnisPhase()
            && self::validateAusgerichtetesTurnierFreilos($turnier)
        ) {
            $team->addFreilos(
                grund: FreilosGrund::TURNIER_AUSGERICHTET,
                turnierAusgerichtet: $turnier
            );
            Html::info("Das Team " . $team->getName() . " hat ein Freilos für ihr frühzeitig ausgeschriebenes Turnier erhalten.");
            TeamRepository::get()->speichern($team);
            if ($sendMail) {
                Mailbot::mail_ausrichter_freilos($team);
            }
            return true;
        }
        return false;
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

    public static function freilos(nTeam $team, Turnier $turnier): void
    {
        if (TeamService::isAufWarteliste($team, $turnier)) {
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

    public static function hasZweiAusgerichteteTurnierFreilose(nTeam $team): bool
    {
        $filter = static function (Freilos $f) {
            return ($f->getGrund() == FreilosGrund::TURNIER_AUSGERICHTET);
        };
        $erhalteneFreilose = $team->getFreiloseBySaison()->filter($filter);
        return $erhalteneFreilose->count() >= 2;
    }

    public static function isFreilosRecyclebar(Freilos $freilos): bool
    {
        if (!$freilos->getGesetztAm()) {
            return false;
        }
        $freilosGesetztUnix = $freilos->getGesetztAm()->getTimestamp();
        $turnierDatumUnix = $freilos->getTurnier()->getDatum()->getTimestamp();
        return $turnierDatumUnix - $freilosGesetztUnix >= 8 * 7 * 24 * 60 * 60;
    }

    public static function validateFreilosRecycling(Freilos $freilos): bool
    {
        $turnier = $freilos->getTurnier();
        return !$turnier->isCanceled()
            && $turnier->isLigaturnier()
            && TeamService::isAufSetzliste($freilos->getTeam(), $turnier)
            && FreilosService::isFreilosRecyclebar($freilos)
            && !FreilosService::hasFreilosRecyclebarForTurnier($freilos);
    }

    public static function handleFreilosRecycling(Turnier $turnier): void
    {
        $freilose = $turnier->getGesetzteFreilose();
        foreach ($freilose as $freilos) {
            if (
                $turnier->isErgebnisPhase()
                && self::validateFreilosRecycling($freilos)
            ) {
                $team = $freilos->getTeam();
                $turnier_id = $turnier->id();
                $filter = static function (Freilos $f) use ($turnier_id) {
                    return $f->getTurnier() && $f->getTurnier()->id() === $turnier_id;
                };
                $vorherigesFreilos = $team->getGueltigeFreilose()->filter($filter)->first() ?: null;
                $team->addFreilos(FreilosGrund::FREILOS_GESETZT, vorherigesFreilos: $vorherigesFreilos);
                TeamRepository::get()->speichern($team);
                Html::info("Das Team " . $team->getName() . " hat ein neues Freilos erhalten für ihr frühzeitig gesetztes Freilos.");
            }
        }

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
        return $team->getGesetzteFreilose()->filter($filter);
    }

    private static function hasFreilosRecyclebarForTurnier(Freilos $freilos): bool
    {
        $turnier = $freilos->getTurnier();
        $freilose = $freilos->getTeam()->getFreiloseBySaison();
        foreach ($freilose as $freilos) {
            if ($freilos->getVorherigesFreilos()
                && $freilos->getVorherigesFreilos()->getTurnier()->id() === $turnier->id()) {
                return true;
            }
        }
        return false;
    }
}