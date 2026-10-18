<?php

namespace App\Turnierbericht\Service;

use Helper;
use Html;
use App\Turnier\Service\TurnierService;
use App\Turnierbericht\Entity\Turnierbericht;

class TurnierberichtPermission
{
    public static function canEdit(Turnierbericht $bericht): bool
    {
        $turnier = $bericht->getTurnier();
        $team_id = $_SESSION['logins']['team']['id'] ?? null;

        // Der Ligaausschuss darf immer bearbeiten
        if (Helper::$ligacenter) {
            Html::notice("Der Ligaausschuss hat dauerhaft die Berechtigung, den Turnierreport anzupassen.");
            return true;
        }

        // Nur der Ausrichter darf als Team bearbeiten
        if (!TurnierService::isAusrichter($turnier, $team_id)) {
            Html::notice("Es fehlen die notwendigen Berechtigungen, um den Turnierreport zu bearbeiten.");
            return false;
        }

        // Der Ausrichter darf nur innerhalb der Bearbeitungsfrist bearbeiten
        if (!TurnierberichtService::isInBearbeitungFrist($turnier)) {
            Html::notice("Die Bearbeitungsfrist für den Turnierreport ist abgelaufen. Änderungen sind nur noch über den Ligaausschuss möglich.");
            return false;
        }

        $frist = TurnierberichtService::getBearbeitungFrist($turnier);
        Html::notice("Der Turnierreport kann noch bis zum " . $frist->format('d.m.Y H:i') . " bearbeitet werden.");
        return true;
    }

    public static function canRead(): bool
    {
        if (Helper::$ligacenter || Helper::$teamcenter) {
            return true;
        }

        Html::notice("Der Turnierreport kann nur aus dem Ligacenter oder dem Teamcenter aufgerufen werden");
        return false;
    }
}
