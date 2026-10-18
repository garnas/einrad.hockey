<?php

namespace App\Service\Ligaleitung;

use App\Entity\Ligaleitung\Ligaleitung;
use App\Repository\Ligaleitung\LigaleitungRepository;
use Config;
use Helper;
use Html;

class LigaleitungService
{
    /**
     * Erneuert das Passwort einer Ligaleitung
     */
    public static function setPasswort(string $login, string $passwort, string $passwortAlt): bool
    {
        $ligaleitung = LigaleitungRepository::get()->findByLogin($login);

        // Überprüfung des PWs
        if ($ligaleitung === null || !password_verify($passwortAlt, $ligaleitung->getPasswort() ?? '')) {
            return false;
        }

        $ligaleitung->setPasswort(password_hash($passwort, PASSWORD_DEFAULT));
        LigaleitungRepository::get()->speichern($ligaleitung);
        return true;
    }

    /**
     * Prüft das Passwort einer Ligaleitung, ohne einzuloggen
     */
    public static function checkPasswort(string $login, string $passwort): bool
    {
        $ligaleitung = LigaleitungRepository::get()->findByLogin($login);
        return $ligaleitung !== null && password_verify($passwort, $ligaleitung->getPasswort() ?? '');
    }

    /**
     * Login der Ligaleitung
     *
     * @param string $funktion 'ligaausschuss', 'team_social_media'
     */
    public static function login(string $login, string $passwort, string $funktion): bool
    {
        $ligaleitung = LigaleitungRepository::get()->findByLogin($login);

        // Existenz prüfen
        if ($ligaleitung === null) {
            Html::error("Unbekannter Loginname");
            Helper::log(Config::LOG_LOGIN, "Falscher Login    | Loginname: " . $login);
            return false;
        }

        // Funktion prüfen
        $context = self::getContext($funktion);
        if ($context === null || $context !== self::getContext($ligaleitung->getFunktion())) {
            return false;
        }

        // Passwort prüfen
        if (password_verify($passwort, $ligaleitung->getPasswort() ?? '')) {
            $_SESSION['logins'][$context]['id'] = $ligaleitung->getLigaleitungId();
            $_SESSION['logins'][$context]['login'] = $ligaleitung->getLogin();
            Helper::log(
                Config::LOG_LOGIN,
                "Erfolgreich       | Loginname: " . $login . " | Kontext: " . $context,
            );
            return true;
        }

        // Passwort falsch
        Helper::log(Config::LOG_LOGIN, "Falsches Passwort | Loginname: " . $login);
        Html::error("Falsches Passwort");
        return false;
    }

    public static function isLoggedIn(string $funktion): bool
    {
        $context = self::getContext($funktion);
        return $context !== null && isset($_SESSION['logins'][$context]);
    }

    /**
     * Die eingeloggte Ligaleitung im Kontext der Funktion
     */
    public static function getLoggedInLigaleitungByFunktion(string $funktion): ?Ligaleitung
    {
        $context = self::getContext($funktion);
        $login = $_SESSION['logins'][$context]['login'] ?? null;
        return $login === null ? null : LigaleitungRepository::get()->findByLogin($login);
    }

    /**
     * Name und ggf. Teamname eines Ligaleitungsmitglieds, z. B. "Max Mustermann (Team)"
     */
    public static function getNameMitTeam(Ligaleitung $ligaleitung): string
    {
        $spieler = $ligaleitung->getSpieler();
        $teamname = $spieler->getTeam()?->getName();
        return $spieler->getVorname() . ' ' . $spieler->getNachname()
            . (!empty($teamname) ? ' (' . $teamname . ')' : '');
    }

    private static function getContext(string $funktion): ?string
    {
        return match ($funktion) {
            "admin", "ligaausschuss" => "la",
            "team_social_media" => "oa",
            default => null,
        };
    }

}
