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
     * Ist der Nutzer als Ligaausschuss im Ligacenter angemeldet?
     * Die einzige Stelle, an der die Berechtigungen den Bereich auswerten.
     */
    public static function isLigaausschuss(): bool
    {
        return Helper::$ligacenter;
    }

    /**
     * Ist das angemeldete Team der Ausrichter des Turniers?
     */
    public static function isAngemeldeterAusrichter(Turnier $turnier): bool
    {
        return ($_SESSION['logins']['team']['id'] ?? 0) == $turnier->getAusrichter()->id();
    }

    /**
     * Ist das angemeldete Team der Ausrichter dieses Spaßturniers?
     */
    public static function isSpassturnierAusrichter(Turnier $turnier): bool
    {
        return $turnier->isSpassturnier() && self::isAngemeldeterAusrichter($turnier);
    }

    /**
     * Volle Rechte unabhängig von Phase und Fristen: der Ligaausschuss bei allen Turnieren,
     * der Ausrichter nur bei seinem Spaßturnier.
     */
    public static function hasVolleRechte(Turnier $turnier): bool
    {
        return self::isLigaausschuss() || self::isSpassturnierAusrichter($turnier);
    }

    /**
     * Gelten die Vorgaben für Ligaturniere (Datum, Startzeit, Plätze, Startgebühr, Ausrichter)?
     * Nicht für den Ligaausschuss und nicht für Spaßturniere.
     */
    public static function isVonLigavorgabenBefreit(Turnier $turnier): bool
    {
        return self::isLigaausschuss() || $turnier->isSpassturnier();
    }

    /**
     * Abschlussturniere organisiert der Ligaausschuss vollständig.
     * Der eingetragene Ausrichter darf nur die Hinweise ändern.
     */
    public static function isNurHinweisAenderbar(Turnier $turnier): bool
    {
        return $turnier->isFinalTurnier() && !self::isLigaausschuss();
    }

    // ------------------------------------------------------------------
    // Turnier anlegen
    // ------------------------------------------------------------------

    /**
     * @return string[] Turnierarten, die der aktuelle Nutzer anlegen darf.
     */
    public static function getErstellbareArten(): array
    {
        return self::isLigaausschuss() ? self::LA_ARTEN : self::TEAM_ARTEN;
    }

    public static function canCreateArt(?string $art): bool
    {
        return \in_array($art, self::getErstellbareArten(), true);
    }

    // ------------------------------------------------------------------
    // Turnier bearbeiten
    // ------------------------------------------------------------------

    /**
     * Turniere in der Ergebnisphase ändern nur der Ligaausschuss und der Ausrichter eines Spaßturniers.
     */
    public static function canChange(Turnier $turnier): bool
    {
        return !$turnier->isErgebnisPhase() || self::hasVolleRechte($turnier);
    }

    /**
     * Turnier absagen: der Ligaausschuss, bei Spaßturnieren auch der Ausrichter.
     */
    public static function canCancel(Turnier $turnier): bool
    {
        return self::hasVolleRechte($turnier);
    }

    /**
     * Darf ein Formularfeld des Turniers geändert werden?
     * Ohne Turnier (Neuanlage) sind alle Felder änderbar.
     * Der Ausrichter eines Spaßturniers darf alle Felder außer der Turnierart ändern.
     */
    public static function canEditField(string $field, ?Turnier $turnier): bool
    {
        if (self::isLigaausschuss() || $turnier === null) {
            return true;
        }

        if (self::isNurHinweisAenderbar($turnier)) {
            return $field === 'hinweis';
        }

        if ($field !== 'art_block' && self::isSpassturnierAusrichter($turnier)) {
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
     * Plätze dürfen ab der Setzphase nicht mehr erhöht werden
     * (außer vom Ligaausschuss und vom Ausrichter eines Spaßturniers).
     */
    public static function canChangePlaetze(Turnier $turnier, int $plaetzeVorher): bool
    {
        if (self::hasVolleRechte($turnier) || $turnier->isWartePhase()) {
            return true;
        }
        return !($turnier->isSetzPhase() && $plaetzeVorher < $turnier->getDetails()->getPlaetze());
    }

    // ------------------------------------------------------------------
    // Teams an- und abmelden
    // ------------------------------------------------------------------

    /**
     * Anmeldungen sind nur bis zur Setzphase möglich, also solange es keinen Spielplan gibt.
     * Bei Spaßturnieren melden Ligaausschuss und Ausrichter jederzeit an.
     */
    public static function canRegisterTeam(Turnier $turnier): bool
    {
        return ($turnier->isSpassturnier() && self::hasVolleRechte($turnier))
            || (!$turnier->isSpielplanPhase() && !$turnier->isErgebnisPhase());
    }

    /**
     * Nichtligateams melden nur der Ligaausschuss und der Ausrichter an.
     * Die Phase prüft zusätzlich {@see canRegisterTeam()}.
     */
    public static function canRegisterNlTeam(Turnier $turnier): bool
    {
        return self::isLigaausschuss()
            || (self::isAngemeldeterAusrichter($turnier) && !$turnier->isFinalTurnier());
    }

    /**
     * Für ein Abschlussturnier können sich Teams nur in der Wartephase melden.
     * Mit der Setzphase stellt der Ligaausschuss das Teilnehmerfeld zusammen.
     */
    public static function canApplyForFinal(Turnier $turnier): bool
    {
        return $turnier->isFinalTurnier() && $turnier->isWartePhase();
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
        return self::hasVolleRechte($turnier);
    }

    /**
     * Teams manuell abmelden (Ligaausschuss / Ausrichter von Spaßturnieren):
     * bis einschließlich Spielplanphase, in der Ergebnisphase nicht mehr.
     * Spaßturniere haben keine Phasen: dort jederzeit.
     */
    public static function canDeregisterTeamManually(Turnier $turnier): bool
    {
        return !$turnier->isErgebnisPhase() || ($turnier->isSpassturnier() && self::hasVolleRechte($turnier));
    }

    /**
     * Ein Team durch ein anderes Liga- oder Nichtligateam ersetzen: nur der Ligaausschuss, jederzeit.
     */
    public static function canReplaceTeam(Turnier $turnier): bool
    {
        return self::isLigaausschuss();
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
     * Spielplan erstellen, hochladen oder löschen: der Ligaausschuss,
     * bei Spaßturnieren auch der Ausrichter. Ob ein Spielplan erstellt werden kann,
     * prüft zusätzlich {@see canCreateSpielplan()}.
     */
    public static function canManageSpielplan(Turnier $turnier): bool
    {
        return self::hasVolleRechte($turnier);
    }

    /**
     * Spielergebnisse eintragen: der Ligaausschuss jederzeit,
     * der Ausrichter nur in der Spielplanphase (Frist: {@see isErgebnisFristOffen()}),
     * der Ausrichter eines Spaßturniers jederzeit. Abschlussturniere trägt nur der Ligaausschuss ein.
     */
    public static function canEnterErgebnisse(Turnier $turnier): bool
    {
        return self::hasVolleRechte($turnier)
            || (
                self::isAngemeldeterAusrichter($turnier)
                && $turnier->isSpielplanPhase()
                && !$turnier->isFinalTurnier()
            );
    }

    /**
     * Ergebnisse können bis Dienstag 23:59 nach dem (letzten) Turniertag eingetragen werden
     * ({@see TurnierFristenService::ergebnisFrist()}). Danach nur noch durch den Ligaausschuss.
     * Für Spaßturniere gilt keine Frist.
     */
    public static function isErgebnisFristOffen(Turnier $turnier, ?DateTimeInterface $jetzt = null): bool
    {
        return self::hasVolleRechte($turnier)
            || TurnierFristenService::isOffen(TurnierFristenService::ergebnisFrist($turnier), $jetzt);
    }
}
