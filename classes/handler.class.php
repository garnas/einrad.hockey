<?php

class Handler
{
    public static function error(string $text, $type = E_USER_ERROR): void
    {
        include (Env::BASE_PATH . '/templates/errors/error.tmp.php');
        trigger_error($text, $type);
    }

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
        //SQL-Logdatei erstellen/beschreiben
        $log_file = fopen($path . $file_name, 'ab');
        $line = date('[Y-M-d H:i:s e]: ') . $line . "\n";
        fwrite($log_file, $line);
        fclose($log_file);
    }
}

