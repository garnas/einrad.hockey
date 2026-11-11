<?php

namespace App\Turnier\Service;

use App\Turnier\Entity\Turnier;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * Alle Termine und Fristen rund um ein Turnier, an einer Stelle berechnet.
 *
 * Grundlage ist der Spieltag: Turniere finden am Wochenende oder an Feiertagen statt.
 * Ein Spieltag beginnt am Samstag und endet am Sonntag. Turniere am Montag oder Dienstag
 * zählen zum vorherigen Spieltag, Turniere von Mittwoch bis Freitag zum folgenden.
 *
 * Beispiel: Turnier am Samstag, 02.05.2026 (Spieltag ab Sa, 02.05.2026 00:00)
 *  - Freilosfrist:          Sa, 07.03.2026 00:00 (8 Wochen vor dem Spieltag)
 *  - Wechsel in Setzphase:  Sa, 04.04.2026 00:00 (4 Wochen vor dem Spieltag)
 *  - Abmeldefrist:          Fr, 17.04.2026 23:59 (Freitag zwei Wochen vor dem Spieltag)
 *  - Spielplanphase ab:     Di, 21.04.2026 23:59 (Dienstag der Vorwoche)
 *  - Ergebnisfrist:         Di, 05.05.2026 23:59 (Dienstag nach dem letzten Turniertag)
 */
final class TurnierFristenService
{
    /**
     * Beginn des Spieltags (Samstag 00:00), dem das Turnier zugeordnet ist.
     */
    public static function spieltagBeginn(Turnier $turnier): DateTimeImmutable
    {
        return self::spieltagBeginnFuerDatum($turnier->getDatum());
    }

    /**
     * Beginn des Spieltags (Samstag 00:00) für ein beliebiges Turnierdatum.
     */
    public static function spieltagBeginnFuerDatum(DateTimeInterface $datum): DateTimeImmutable
    {
        $tag = DateTimeImmutable::createFromInterface($datum)->setTime(0, 0);
        $wochentag = (int) $tag->format('N'); // 1 = Montag ... 7 = Sonntag

        $delta = match (true) {
            $wochentag <= 2 => -($wochentag + 1), // Mo, Di -> vorheriger Samstag
            $wochentag === 7 => -1,               // So -> Samstag davor
            default => 6 - $wochentag,            // Mi bis Sa -> (kommender) Samstag
        };

        return $tag->modify("$delta days");
    }

    /**
     * Freilosfrist: 8 Wochen vor dem Spieltag.
     *
     * - Ein Ausrichter erhält ein Freilos, wenn das Turnier bis dahin erstellt wurde.
     * - Ein Freilos wird recycelt, wenn es bis dahin für das Turnier gesetzt wurde.
     */
    public static function freilosFrist(Turnier $turnier): DateTimeImmutable
    {
        return self::spieltagBeginn($turnier)->modify('-8 weeks');
    }

    /**
     * Ende der Wartephase und Beginn der Setzphase: 4 Wochen vor dem Spieltag.
     * Bis dahin können Teams Ligaturniere eintragen.
     */
    public static function setzphaseAb(Turnier $turnier): DateTimeImmutable
    {
        return self::spieltagBeginn($turnier)->modify('-4 weeks');
    }

    /**
     * Abmeldefrist für Teams auf der Setzliste: Freitag 23:59 zwei Wochen vor dem Spieltag.
     */
    public static function abmeldeFrist(Turnier $turnier): DateTimeImmutable
    {
        return self::spieltagBeginn($turnier)->modify('-2 weeks')->modify('-1 second');
    }

    /**
     * Beginn der Spielplanphase: Dienstag 23:59 in der Woche vor dem Spieltag.
     */
    public static function spielplanphaseAb(Turnier $turnier): DateTimeImmutable
    {
        return self::spieltagBeginn($turnier)->modify('-11 days')->setTime(23, 59);
    }

    /**
     * Ergebnisfrist: Dienstag 23:59 nach dem (letzten) Turniertag.
     * Findet das Turnier an einem Dienstag statt, endet die Frist am selben Tag.
     *
     * Bis dahin trägt der Ausrichter die Ergebnisse ein und bearbeitet den Turnierreport.
     * Spaßturniere wechseln danach automatisch in die Ergebnisphase.
     */
    public static function ergebnisFrist(Turnier $turnier): DateTimeImmutable
    {
        $letzterTag = DateTimeImmutable::createFromInterface($turnier->getDatumBis() ?? $turnier->getDatum());
        $tageBisDienstag = (2 - (int) $letzterTag->format('N') + 7) % 7;

        return $letzterTag->modify("+$tageBisDienstag days")->setTime(23, 59, 59);
    }

    /**
     * Bearbeitungsfrist für den Turnierreport (inkl. Zeitstrafen und Spielerausleihen).
     */
    public static function berichtFrist(Turnier $turnier): DateTimeImmutable
    {
        return self::ergebnisFrist($turnier);
    }

    /**
     * Ist die Frist zum Zeitpunkt $jetzt (Standard: jetzt) noch nicht abgelaufen?
     */
    public static function isOffen(DateTimeInterface $frist, ?DateTimeInterface $jetzt = null): bool
    {
        return ($jetzt ?? new DateTimeImmutable()) <= $frist;
    }
}
