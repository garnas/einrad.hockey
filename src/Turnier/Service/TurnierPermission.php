<?php

namespace App\Turnier\Service;

use App\Turnier\Entity\Turnier;
use App\Turnier\Enum\TurnierPhase;
use Config;
use DateTimeInterface;
use Helper;

/**
 * Alle Regeln, wer was an einem Turnier tun darf – abhängig von Turnierphase, Bereich
 * (Ligacenter / Teamcenter) und Rolle (Ligaausschuss, Ausrichter, Team).
 *
 * Die Methoden prüfen nur und geben keine Meldungen aus; Fehlermeldungen bleiben beim Aufrufer.
 */
class TurnierPermission
{
    /**
     * Turnierarten, die jedes Team anlegen darf.
     */
    private const TEAM_ARTEN = ['I', 'II', 'spass'];

    /**
     * Turnierarten, die der Ligaausschuss anlegen darf.
     * III (offene ABCDEF-Turniere) ist eine alte Turnierart und kann nicht mehr angelegt werden.
     */
    private const LA_ARTEN = ['I', 'II', 'final', 'fixed', 'spass'];

    /**
     * Formularfelder, die ein Team in der jeweiligen Phase nicht mehr ändern darf.
     * Der Ligaausschuss darf alle Felder ändern.
     */
    private const GESPERRTE_FELDER = [
        'datum' => [TurnierPhase::WARTE, TurnierPhase::SETZ, TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS],
        'startzeit' => [TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS],
        'besprechung' => [TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS],
        'startgebuehr' => [TurnierPhase::SETZ, TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS],
        'sofort_oeffnen' => [TurnierPhase::SETZ, TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS],
        'art_block' => [TurnierPhase::WARTE, TurnierPhase::SETZ, TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS],
        'plaetze' => [TurnierPhase::SETZ, TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS],
        'min_teams' => [TurnierPhase::SETZ, TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS],
    ];

    // ------------------------------------------------------------------
    // Rollen
    // ------------------------------------------------------------------

    /**
     * Rechte wie der Ligaausschuss: im Ligacenter, als Ausrichter eines Finalturniers
     * oder bei Spaßturnieren.
     */
    /**
     * Ist das angemeldete Team der Ausrichter des Turniers?
     */
    public static function isAngemeldeterAusrichter(Turnier $turnier): bool
    {
        return ($_SESSION['logins']['team']['id'] ?? 0) == $turnier->getAusrichter()->id();
    }

    public static function hasLaRights(Turnier $turnier): bool
    {
        return
            Helper::$ligacenter
            || (
                $turnier->isFinalTurnier()
                && TurnierService::isAusrichter($turnier, $_SESSION['logins']['team']['id'])
            )
            || $turnier->isSpassturnier();
    }

    // ------------------------------------------------------------------
    // Turnier anlegen
    // ------------------------------------------------------------------

    /**
     * @return string[] Turnierarten, die der aktuelle Nutzer anlegen darf.
     */
    public static function getErstellbareArten(): array
    {
        return Helper::$ligacenter ? self::LA_ARTEN : self::TEAM_ARTEN;
    }

    public static function canCreateArt(?string $art): bool
    {
        return \in_array($art, self::getErstellbareArten(), true);
    }

    // ------------------------------------------------------------------
    // Turnier bearbeiten
    // ------------------------------------------------------------------

    /**
     * Turniere in der Ergebnisphase darf nur der Ligaausschuss ändern.
     */
    public static function canChange(Turnier $turnier): bool
    {
        return !$turnier->isErgebnisPhase() || self::hasLaRights($turnier);
    }

    /**
     * Darf ein Formularfeld des Turniers geändert werden?
     * Ohne Turnier (Neuanlage) sind alle Felder änderbar.
     */
    public static function canEditField(string $field, ?Turnier $turnier): bool
    {
        if (Helper::$ligacenter || $turnier === null) {
            return true;
        }

        if (\in_array($turnier->getPhase(), self::GESPERRTE_FELDER[$field] ?? [], true)) {
            return false;
        }

        return match ($field) {
            'single_higher' => self::canExtendBlockHoch($turnier) && !TurnierService::isErweitertBlock($turnier),
            'single_lower' => self::canExtendBlockRunter($turnier) && !TurnierService::isErweitertBlock($turnier),
            'single_none' => !TurnierService::isErweitertBlock($turnier),
            'multiple_free' => self::canExtendBlockFrei($turnier) && !TurnierService::isBlockfrei($turnier),
            'multiple_none' => !TurnierService::isBlockfrei($turnier),
            default => true,
        };
    }

    /**
     * Plätze dürfen ab der Setzphase nicht mehr erhöht werden (außer vom Ligaausschuss).
     */
    public static function canChangePlaetze(Turnier $turnier, int $plaetzeVorher): bool
    {
        if (self::hasLaRights($turnier) || $turnier->isWartePhase()) {
            return true;
        }
        return !($turnier->isSetzPhase() && $plaetzeVorher < $turnier->getDetails()->getPlaetze());
    }

    // ------------------------------------------------------------------
    // Teams an- und abmelden
    // ------------------------------------------------------------------

    /**
     * Anmeldungen sind nur bis zur Setzphase möglich, also solange es keinen Spielplan gibt.
     */
    public static function canRegisterTeam(Turnier $turnier): bool
    {
        return !$turnier->isSpielplanPhase() && !$turnier->isErgebnisPhase();
    }

    /**
     * Nichtligateams melden nur der Ligaausschuss und der Ausrichter an.
     * Die Phase prüft zusätzlich {@see canRegisterTeam()}.
     */
    public static function canRegisterNlTeam(Turnier $turnier): bool
    {
        return Helper::$ligacenter || self::isAngemeldeterAusrichter($turnier);
    }

    /**
     * Ein Team meldet sich selbst ab: von der Warteliste jederzeit,
     * von der Setzliste nur in der Setzphase bis zur Abmeldefrist
     * ({@see TurnierFristenService::abmeldeFrist()}).
     */
    public static function canDeregisterTeam(Turnier $turnier, bool $aufSetzliste, ?DateTimeInterface $jetzt = null): bool
    {
        return !$aufSetzliste
            || (
                $turnier->isSetzPhase()
                && TurnierFristenService::isOffen(TurnierFristenService::abmeldeFrist($turnier), $jetzt)
            );
    }

    /**
     * Teams manuell an- und abmelden: Ligaausschuss, bei Spaßturnieren auch der Ausrichter.
     */
    public static function canManageTeams(Turnier $turnier): bool
    {
        return Helper::$ligacenter
            || !Helper::$teamcenter
            || (self::isAngemeldeterAusrichter($turnier) && $turnier->isSpassturnier());
    }

    /**
     * Teams manuell abmelden (Ligaausschuss / Ausrichter von Spaßturnieren):
     * bis einschließlich Spielplanphase, in der Ergebnisphase nicht mehr.
     */
    public static function canDeregisterTeamManually(Turnier $turnier): bool
    {
        return !$turnier->isErgebnisPhase();
    }

    /**
     * Ein Team durch ein anderes Liga- oder Nichtligateam ersetzen: nur der Ligaausschuss, jederzeit.
     */
    public static function canReplaceTeam(Turnier $turnier): bool
    {
        return Helper::$ligacenter;
    }

    // ------------------------------------------------------------------
    // Turnierblock erweitern
    // ------------------------------------------------------------------

    /**
     * Der Turnierblock kann nur in der Setzphase erweitert werden.
     */
    public static function canExtendBlock(Turnier $turnier): bool
    {
        return $turnier->isSetzPhase();
    }

    /**
     * Ist das Turnier nach oben erweiterbar?
     */
    public static function canExtendBlockHoch(Turnier $turnier): bool
    {
        return self::canExtendBlock($turnier)
            && !$turnier->isBlockErweitertHoch()
            && !$turnier->isBlockErweitertRunter()
            && !$turnier->isBlockErweitertFrei()
            && $turnier->getBlock() !== 'AB'
            && $turnier->getBlock() !== 'A'
            && $turnier->isLigaTurnier();
    }

    /**
     * Ist das Turnier nach unten erweiterbar?
     */
    public static function canExtendBlockRunter(Turnier $turnier): bool
    {
        return self::canExtendBlock($turnier)
            && !$turnier->isBlockErweitertRunter()
            && !$turnier->isBlockErweitertHoch()
            && !$turnier->isBlockErweitertFrei()
            && $turnier->getBlock() !== 'EF'
            && $turnier->getBlock() !== 'F'
            && $turnier->isLigaTurnier();
    }

    /**
     * Ist das Turnier blockfrei erweiterbar?
     */
    public static function canExtendBlockFrei(Turnier $turnier): bool
    {
        return self::canExtendBlock($turnier)
            && $turnier->isLigaturnier()
            && !$turnier->isBlockErweitertFrei()
            && $turnier->getBlock() != Config::BLOCK_ALL[0];
    }

    // ------------------------------------------------------------------
    // Spielplan und Ergebnisse
    // ------------------------------------------------------------------

    /**
     * Ligaturniere brauchen für einen Spielplan die Setzphase.
     */
    public static function canCreateSpielplan(Turnier $turnier): bool
    {
        return !$turnier->isLigaturnier() || $turnier->isSetzPhase();
    }

    /**
     * Spielergebnisse eintragen: der Ligaausschuss jederzeit,
     * der Ausrichter nur in der Spielplanphase (Frist: {@see isErgebnisFristOffen()}).
     */
    public static function canEnterErgebnisse(Turnier $turnier): bool
    {
        return Helper::$ligacenter
            || (self::isAngemeldeterAusrichter($turnier) && $turnier->isSpielplanPhase());
    }

    /**
     * Ergebnisse können bis Dienstag 23:59 nach dem (letzten) Turniertag eingetragen werden
     * ({@see TurnierFristenService::ergebnisFrist()}). Danach nur noch durch den Ligaausschuss.
     */
    public static function isErgebnisFristOffen(Turnier $turnier, ?DateTimeInterface $jetzt = null): bool
    {
        return Helper::$ligacenter
            || TurnierFristenService::isOffen(TurnierFristenService::ergebnisFrist($turnier), $jetzt);
    }
}
