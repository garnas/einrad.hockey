<?php

namespace App\Turnier\Service;

use Helper;

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
}
