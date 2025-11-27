<?php

namespace App\Service\Neuigkeit;
use Helper;
use App\Enum\NeuigkeitArt;

class PermissionService
{
    public static function canEmbedLink(): bool
    {
        return Helper::$ligacenter || Helper::$oeffentlichkeitsausschuss;
    }

    public static function canSetTime(): bool
    {
        return Helper::$ligacenter || Helper::$oeffentlichkeitsausschuss;
    }

    public static function canSetArt(NeuigkeitArt $art): bool
    {
        // Der Ligaausschuss und der Öffentlichkeitsausschuss dürfen jede Art anlegen
        if (Helper::$ligacenter || Helper::$oeffentlichkeitsausschuss) {
            return true;
        }

        // Teams dürfen nur Fördermittel und Neuigkeiten anlegen
        if ($art == NeuigkeitArt::FOERDERMITTEL || $art == NeuigkeitArt::NEUIGKEIT) {
            return true;
        }

        return false;
    }

    public static function canEdit(string $eingetragen_von): bool
    {
        // Der Ligaausschuss darf immer bearbeiten
        if (Helper::$ligacenter) {
            return true;
        }

        // Der Öffentlichkeitsausschuss darf bearbeiten, wenn es nicht vom Ligaausschuss eingetragen wurde
        if (Helper::$oeffentlichkeitsausschuss && !($eingetragen_von === "Ligaausschuss")) {
            return true;
        }

        // Das Team darf nur bearbeiten, wenn es die Neuigkeit selbst eingetragen hat
        if (isset($_SESSION['logins']['team']['name']) && $_SESSION['logins']['team']['name'] === $eingetragen_von) {
            return true;
        }
        
        return false;
    }

    public static function canDelete(string $eingetragen_von): bool
    {
        // Der Ligaausschuss darf immer löschen
        if (Helper::$ligacenter) {
            return true;
        }

        // Der Öffentlichkeitsausschuss darf nur löschen, wenn er die Neuigkeit selbst eingetragen hat
        if (Helper::$oeffentlichkeitsausschuss && $eingetragen_von === "Öffentlichkeitsausschuss") {
            return true;
        }

        // Das Team darf nur löschen, wenn es die Neuigkeit selbst eingetragen hat
        if (isset($_SESSION['logins']['team']['name']) && $_SESSION['logins']['team']['name'] === $eingetragen_von) {
            return true;
        }
        
        return false;
    }

    public static function canArchive(string $eingetragen_von): bool
    {
        // Der Ligaausschuss und der Öffentlichkeitsausschuss dürfen immer archivieren
        if (Helper::$ligacenter || Helper::$oeffentlichkeitsausschuss) {
            return true;
        }

        // Das Team darf nur archivieren, wenn es die Neuigkeit selbst eingetragen hat
        if (isset($_SESSION['logins']['team']['name']) && $_SESSION['logins']['team']['name'] === $eingetragen_von) {
            return true;
        }
        
        return false;
    }
}
