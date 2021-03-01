<?php

class Handler
{
    public static function reload($path = null): void
    {
        $url = ($path === null) ? dbi::escape($_SERVER['PHP_SELF']) : Env::BASE_URL . $path;
        header("Location: $url");
        die();
    }

    public static function not_found($text): void
    {
        include (Env::BASE_PATH . '/templates/errors/error.tmp.php');
        trigger_error($text, E_USER_NOTICE);
        die();
    }

    /**
     * Erstellt einen Log in einer Logdatei im System-Ordner
     *
     * @param string $file_name Name der Logdatei
     * @param string $line Einzutragender Text in die Logdatei
     */
    public static function log(string $file_name, string$line): void
    {
        $path = Env::BASE_PATH . '/system/logs/';
        $log_file = fopen($path . $file_name, 'ab');
        $line = date('[Y-M-d\TH:i:s] [') . self::get_akteur() . "]:\n" . $line . "\n\n";

        fwrite($log_file, $line);
        fclose($log_file);
    }


    /**
     * Welcher Akteur benutzt gerade die Seite? Für Logs.
     *
     * @param bool $hide_la_name
     * @return string
     */
    public static function get_akteur(bool $hide_la_name = false): string
    {
        if (Config::$ligacenter){
            return ($hide_la_name) ? "Ligaausschuss" : $_SESSION['logins']['la']['login'];
        }

        if (Config::$teamcenter) {
            return $_SESSION['logins']['team']['name'];
        }

        // Welche Akteure können alles am Werk sein?
        $akteure = [
            $_SESSION['logins']['team']['name'] ?? '',
            $_SESSION['logins']['la']['login'] ?? '',
            $_SESSION['ligabot'] ?? ''
        ];
        return implode(" | ", array_filter($akteure)) ?: 'Unbekannt';
    }
}

