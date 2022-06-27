<?php

namespace App\Service\Turnier;

use App\Entity\Turnier\Turnier;
use Env;

class TurnierLinks {

    public static function details(Turnier $turnier): string
    {
        return Env::BASE_URL . "/liga/turnier_details.php?turnier_id=" . $turnier->id();
    }

    /**
     * Gibt den Link zum Liga-Spielplan aus, je nach dem ob er manuell hochgeladen oder automatisch erstellt wurde
     *
     * @param Turnier $turnier
     * @param string $scope default: allgemein, lc => ligacenter tc => teamcenter
     * @return false|string
     */
    public static function spielplan(Turnier $turnier, string $scope = ''): false|string
    {
        // Es existiert ein manuell hochgeladener Spielplan
        if (!empty($turnier->getSpielplanDatei())) {
            return $turnier->getSpielplanDatei();
        }

        // Es existiert ein automatisch erstellter Spielplan
        if (!empty($turnier->getSpielplanVorlage())) {
            return match ($scope) {
                'lc' => Env::BASE_URL . '/ligacenter/lc_spielplan.php?turnier_id=' . $turnier->id(),
                'tc' => Env::BASE_URL . '/teamcenter/tc_spielplan.php?turnier_id=' . $turnier->id(),
                default => Env::BASE_URL . '/liga/spielplan.php?turnier_id=' . $turnier->id()
            };
        }
        if($turnier->isFinalTurnier()) {
            return match ($scope) {
                'lc' => Env::BASE_URL . '/ligacenter/lc_spielplan.php?turnier_id=' . $turnier->id(),
                'tc' => Env::BASE_URL . '/teamcenter/tc_spielplan.php?turnier_id=' . $turnier->id(),
                default => Env::BASE_URL . '/liga/spielplan_finale.php?turnier_id=' . $turnier->id()
            };
        }

        return false;
    }
}
