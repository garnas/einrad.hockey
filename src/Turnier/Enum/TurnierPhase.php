<?php

namespace App\Turnier\Enum;

/**
 * Phasen eines Turniers, gespeichert in turniere_liga.phase.
 *
 * Ablauf: Wartephase -> Setzphase -> Spielplanphase -> Ergebnisphase
 */
enum TurnierPhase: string
{
    /** Anmeldung offen, Setzliste noch nicht fix (bis 4 Wochen vor dem Turnier) */
    case WARTE = 'warte';

    /** Setzliste wird nach Rangtabelle aufgefüllt, Spielplan kann erstellt werden */
    case SETZ = 'setz';

    /** Spielplan steht, Ergebnisse können eingetragen werden */
    case SPIELPLAN = 'spielplan';

    /** Turnierergebnis ist eingetragen */
    case ERGEBNIS = 'ergebnis';

    public function label(): string
    {
        return match ($this) {
            self::WARTE => 'Wartephase',
            self::SETZ => 'Setzphase',
            self::SPIELPLAN => 'Spielplanphase',
            self::ERGEBNIS => 'Ergebnisphase',
        };
    }
}
