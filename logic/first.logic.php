<?php

// Dieses Dokument muss immer als Erstes für die Logik geladen werden

/**
 * php.ini Einstellungen
 */
require_once __DIR__ . '/../system/ini_set.php';

/**
 * Enviroment-Variablen laden
 */
require_once __DIR__ . '/../env.php';

/**
 * Session starten
 */
session_start();
session_regenerate_id();

/**
 * Autoloader der Klassen
 *
 * https://www.php.net/manual/de/language.oop5.autoload.php
 */
spl_autoload_register(
    static function ($class) {
        $class = strtolower($class);
        include Env::BASE_PATH . '/classes/' . $class . '.class.php';
    }
);

/**
 * Diese Funktion wird nach Beendigung des Skriptes ausgeführt.
 * Sie logt User und gibt Fehlerseiten aus.
 *
 * https://phpdelusions.net/articles/error_reporting#error_page
 */
register_shutdown_function(static function () {

    // Fehlerseite nur bei Fatal Errors
    $error = error_get_last();
    if (
        $error !== null
        && ($error['type'] === E_USER_ERROR || $error['type'] === E_ERROR)
    ) {
        if ($error['type'] === E_USER_ERROR) {
            $text = $error['message']; // Nur selbst erstellte Fehlertexte mit trigger_error(...) werden angezeigt
        }
        include Env::BASE_PATH . '/templates/errors/error.tmp.php';
    }

    // Logs der Besucher
    Handler::log("user.log",
        $_SERVER['REQUEST_URI']
        . " | " . round(microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"], 3) . " s (Load)"
        . " | " . dbi::$db->query_count . " (Querys)");

});

/**
 * Verbindung zur Datenbank
 */
dbi::initialize(); // Neue DB-Verbindung mit Prepared-Statements

if(Env::WARTUNGSMODUS && !isset($_SESSION['wartungsmodus'])){
    trigger_error("Die Seite befindet sich im Wartungsmodus", E_USER_ERROR);
}