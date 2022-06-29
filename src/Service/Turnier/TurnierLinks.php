<?php

namespace App\Service\Turnier;

use App\Entity\Turnier\Turnier;
use Env;
use Html;

class TurnierLinks {

    public static function details(Turnier $turnier): string
    {
        return Env::BASE_URL . "/liga/turnier_details.php?turnier_id=" . $turnier->id();
    }

    public static function getLinksAnmeldeListe(Turnier $turnier): array
    {
        return [
            Html::link("tc_team_anmelden.php?turnier_id=" . $turnier->id(), 'An- / Abmeldung', false, 'how_to_reg'),
            Html::link("../liga/turnier_details.php?turnier_id=" .  $turnier->id(), 'Turnierdetails', false, 'info')
        ];
    }
    
    public static function getLinksFuerLa(Turnier $turnier)
    {   
        $turnierId = $turnier->id();
        $links =
            [
                Html::link("../liga/turnier_details.php?turnier_id=$turnierId", 'Details', icon:'info'),
                Html::link("lc_turnier_log.php?turnier_id=$turnierId", ' Log einsehen', icon:'info_outline'),
                Html::link("lc_team_anmelden.php?turnier_id=$turnierId", ' Teams an/abmelden', icon:'how_to_reg'),
                Html::link("lc_turnier_bearbeiten.php?turnier_id=$turnierId", ' Turnier bearbeiten/löschen', icon:'create'),
                Html::link("lc_spielplan_verwalten.php?turnier_id=$turnierId", ' Spielplan/Ergebnis verwalten', icon:'playlist_play'),
                Html::link("../ligacenter/lc_turnier_report.php?turnier_id=$turnierId", 'Turnierreport bearbeiten', icon:'article')
            ];
        if ($turnier->isSpielplanPhase()){
            $links[] = Html::link(self::spielplan($turnier),'Spielergebnis eintragen', icon:'reorder');
        }
        return $links;
    }

    public static function getLinksFuerAusrichter(Turnier $turnier): array
    {
        $turnierId = $turnier->id();
        $links[] = Html::link("../liga/turnier_details.php?turnier_id=$turnierId",
            "Turnierdetails",
            icon: "info");

        if ($turnier->isCanceled()) {
            return $links;
        }

        $links[] = Html::link("tc_turnier_bearbeiten.php?turnier_id=$turnierId",
            "Turnier bearbeiten",
            icon: "create");

        if ($turnier->isWartePhase() || $turnier->isSetzPhase()) {
            $links[] = Html::link("../teamcenter/tc_nl_team_anmelden.php?turnier_id=$turnierId",
                "Nichtligateam anmelden",
                icon:"how_to_reg"
            );
        }

        if ($turnier->isSpassTurnier()) {
            $links[] = Html::link("../teamcenter/tc_spassturnier_anmeldung.php?turnier_id=$turnierId",
                "Teams manuell anmelden",
                icon:"how_to_reg"
            );
        }
        if ($turnier->isSpielplanPhase()) {
            $links[] = "<b>"
                . Html::link("../teamcenter/tc_spielplan.php?turnier_id=$turnierId",
                    "Ergebnisse eintragen",
                    icon:"reorder")
                . "</b>";
            $links[] = "<b>"
                . Html::link("../teamcenter/tc_turnier_report.php?turnier_id=$turnierId",
                    "Turnierreport eintragen",
                    icon:"article")
                . "</b>";
        }
        if ($turnier->isErgebnisPhase()) {
            $links[] = Html::link("../teamcenter/tc_spielplan.php?turnier_id=$turnierId",
                "Ergebnisse verändern",
                icon:"reorder");
            $links[] = Html::link("../teamcenter/tc_turnier_report.php?turnier_id=$turnierId",
                "Turnierreport verändern",
                icon:"article");
        }
        return $links;
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
        if ($turnier->getSpielplanVorlage() !== null) {
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
