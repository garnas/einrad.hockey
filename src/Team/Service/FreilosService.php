<?php

namespace App\Team\Service;

use App\Team\Entity\Freilos;
use App\Team\Entity\Freilosgrund;
use App\Team\Entity\Team;
use App\Spieler\Entity\Spieler;
use App\Turnier\Entity\Turnier;
use App\Turnier\Entity\Turnieranmeldung;
use App\Team\Repository\TeamRepository;
use App\Turnier\Service\BlockService;
use App\Turnier\Service\TurnierFristenService;
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
    public static function handleAusgerichtetesTurnierFreilos(Turnier $turnier, bool $sendMail = true): bool
    {
        $team = $turnier->getAusrichter();
        if (
            $turnier->isErgebnisPhase()
            && self::validateAusgerichtetesTurnierFreilos($turnier)
        ) {
            $team->addFreilos(
                grund: Freilosgrund::TURNIER_AUSGERICHTET,
                turnierAusgerichtet: $turnier,
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
        // Das Turnier muss bis zur Freilosfrist (8 Wochen vor dem Spieltag) erstellt worden sein
        return !$turnier->isCanceled()
            && ($turnier->isFinalTurnier() || $turnier->isLigaturnier())
            && TurnierFristenService::isOffen(TurnierFristenService::freilosFrist($turnier), $turnier->getErstelltAm());
    }

    public static function freilos(Team $team, Turnier $turnier): void
    {
        if (TeamService::isAufWarteliste($team, $turnier)) {
            $anmeldung = $team->getTurniereListe()->get($turnier->id());
        } else {
            $anmeldung = new Turnieranmeldung();
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

    public static function hasZweiAusgerichteteTurnierFreilose(Team $team): bool
    {
        $filter = static function (Freilos $f) {
            return ($f->getGrund() == Freilosgrund::TURNIER_AUSGERICHTET);
        };
        $erhalteneFreilose = $team->getFreiloseBySaison()->filter($filter);
        return $erhalteneFreilose->count() >= 2;
    }

    public static function isFreilosRecyclebar(Freilos $freilos): bool
    {
        if (!$freilos->getGesetztAm()) {
            return false;
        }
        // Das Freilos muss bis zur Freilosfrist (8 Wochen vor dem Spieltag) gesetzt worden sein
        return TurnierFristenService::isOffen(
            TurnierFristenService::freilosFrist($freilos->getTurnier()),
            $freilos->getGesetztAm(),
        );
    }

    public static function validateFreilosRecycling(Freilos $freilos): bool
    {
        $turnier = $freilos->getTurnier();
        return !$turnier->isCanceled()
            && $turnier->isLigaturnier()
            && TeamService::isAufSetzliste($freilos->getTeam(), $turnier)
            && self::isFreilosRecyclebar($freilos)
            && !self::hasFreilosRecyclebarForTurnier($freilos);
    }

    public static function handleFreilosRecycling(Turnier $turnier, bool $sendMail = true): void
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
                $team->addFreilos(Freilosgrund::FREILOS_GESETZT, vorherigesFreilos: $vorherigesFreilos);
                TeamRepository::get()->speichern($team);
                Html::info("Das Team " . $team->getName() . " hat ein neues Freilos erhalten für ihr frühzeitig gesetztes Freilos.");
                if ($sendMail) {
                    Mailbot::mail_freilos_recycle($team);
                }
            }
        }

    }

    /**
     * Freilose, die bei einer Turnierabsage zurückgegeben werden: alle für das Turnier gesetzten
     * Freilose von Teams, die noch angemeldet sind und noch kein Ersatzfreilos erhalten haben.
     * Teams, die sich abgemeldet haben, erhalten ihr Freilos nicht zurück.
     *
     * @return Freilos[]
     */
    public static function getRueckgabeFreilose(Turnier $turnier): array
    {
        $rueckgabe = [];
        foreach ($turnier->getGesetzteFreilose() as $freilos) {
            if (
                TeamService::isAngemeldet($freilos->getTeam(), $turnier)
                && !self::hasErsatzfreilos($freilos)
            ) {
                $rueckgabe[] = $freilos;
            }
        }
        return $rueckgabe;
    }

    /**
     * Wurde für dieses Freilos bereits ein neues ausgegeben (Recycling oder Turnierabsage)?
     */
    public static function hasErsatzfreilos(Freilos $freilos): bool
    {
        foreach ($freilos->getTeam()->getFreilose() as $f) {
            if ($f->getVorherigesFreilos() === $freilos) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gibt bei einer Turnierabsage die gesetzten Freilose zurück.
     *
     * Das gesetzte Freilos bleibt als verbraucht bestehen; das Team erhält ein neues Freilos
     * derselben Saison, das auf das verbrauchte verweist. Gespeichert wird mit dem Turnier.
     */
    public static function handleTurnierAbsage(Turnier $turnier): void
    {
        foreach (self::getRueckgabeFreilose($turnier) as $freilos) {
            $team = $freilos->getTeam();
            $team->addFreilos(
                Freilosgrund::TURNIER_ABGESAGT,
                saison: $freilos->getSaison(),
                vorherigesFreilos: $freilos,
            );
            $turnier->getLogService()->addLog("Freilos zurückgegeben: " . $team->getName());
            Html::info("Das Team " . $team->getName() . " hat sein Freilos für das abgesagte Turnier zurückerhalten.");
        }
    }

    public static function handleSchiriFreilos(Team $team, bool $sendMail = true): bool
    {
        $filter = static function (Spieler $s) {
            return ($s->getLetzteSaison() == Config::SAISON && $s->getSchiri() >= Config::SAISON);
        };
        $aktiveSchiris = $team->getKader()->filter($filter);

        if (!TeamValidator::hasSchiriFreilosErhalten($team) && $aktiveSchiris->count() >= 2) {
            $team->addFreilos(Freilosgrund::SCHIRI);
            if ($sendMail) {
                Mailbot::mail_schiri_freilos($team);
            }
            return true;
        }
        return false;
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
