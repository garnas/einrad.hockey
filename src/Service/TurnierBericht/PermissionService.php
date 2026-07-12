<?php

namespace App\Service\TurnierBericht;

use Helper;
use Html;
use App\Service\Turnier\TurnierService;
use App\Entity\TurnierBericht\TurnierBericht;

class PermissionService
{
    public static function canEdit(TurnierBericht $bericht): bool
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
        if (!TurnierBerichtService::isInBearbeitungFrist($turnier)) {
            Html::notice("Die Bearbeitungsfrist für den Turnierreport ist abgelaufen. Änderungen sind nur noch über den Ligaausschuss möglich.");
            return false;
        }

        $frist = TurnierBerichtService::getBearbeitungFrist($turnier);
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
