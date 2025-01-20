<?php

namespace App\Entity\Team;

enum FreilosGrund: string
{
    case SCHIRI = "Freilos für Schiris";
    case TURNIER_AUSGERICHTET = "Freilos für Turnierausrichtung";
    case FREILOS_GESETZT = "Freilos für frühzeitig gesetztes Freilos";
    case SONSTIGES = "Freilos";

    public static function fromName(string $name): FreilosGrund
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name) {
                return $status;
            }
        }
        return self::SONSTIGES;
    }

}