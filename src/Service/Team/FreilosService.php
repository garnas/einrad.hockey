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

    public static function hasFreilosForAusgerichtetesTurnier(nTeam $team, Turnier $turnier): bool
    {
        $tunier_id = $turnier->id();
        $filter = static function (Freilos $f) use ($tunier_id) {
            return ($f->getTurnierAusgerichtet() !== null && $f->getTurnierAusgerichtet()->id() == $tunier_id);
        };
        return $team->getFreiloseBySaison()->filter($filter)->count() > 0;
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
}