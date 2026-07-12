<?php

namespace App\Service\TurnierBericht;

use App\Entity\Turnier\Turnier;
use App\Entity\TurnierBericht\TurnierBericht;
use DateTimeImmutable;

class TurnierBerichtService
{
    public static function getBearbeitungFrist(Turnier $turnier): DateTimeImmutable
    {
        $turnier_datum = DateTimeImmutable::createFromMutable($turnier->getDatum());

        // Dienstag nach dem Turnier, 23:59:59 Uhr
        $frist = $turnier_datum->modify('next tuesday')->setTime(23, 59, 59);

        // Ist das Turnier an einem Dienstag, dann ändere nur die Uhrzeit
        if ($turnier_datum->format('N') == 2) {
            $frist = $turnier_datum->setTime(23, 59, 59);
        }

        return $frist;
    }

    public static function isInBearbeitungFrist(Turnier $turnier, ?DateTimeImmutable $now = null): bool
    {
        $now ??= new DateTimeImmutable();
        return $now <= self::getBearbeitungFrist($turnier);
    }

    public static function isKaderChecked(TurnierBericht $bericht): bool
    {
        return $bericht->getKaderUeberprueft() == 'Ja';
    }
}
