<?php

namespace App\Entity\Team;

enum Freilosgrund: string
{
    case SCHIRI = "Freilos für Schiris";
    case TURNIER_AUSGERICHTET = "Freilos für Turnierausrichtung";
    case FREILOS_GESETZT = "Freilos für frühzeitig gesetztes Freilos";
    case SONSTIGES = "Freilos";
    case NEUES_LIGATEAM = "Neues Ligateam";

    public static function fromName(string $name): Freilosgrund
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name) {
                return $status;
            }
        }
        return self::SONSTIGES;
    }

}
