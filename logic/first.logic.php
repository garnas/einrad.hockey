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
            $_SESSION['error']['text'] = $error['message']; // Nur selbst erstellte Fehlertexte mit trigger_error(...) werden angezeigt
            $_SESSION['error']['url'] = $_SERVER['REQUEST_URI'];
        }
        Handler::reload("/errors/500.php");
    }

    // Logs der Besucher
    if (strpos(Env::BASE_URL, $_SERVER['SERVER_NAME']) > 0){
        $referrer = " | " . ($_SERVER['HTTP_REFERER'] ?? '') . " (Referrer)";
    } else {
        $referrer = '';
    }
    Handler::log("user.log",
        $_SERVER['REQUEST_URI']
        . " | " . round(microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"], 3) . " s (Load)"
        . " | " . dbi::$db->query_count . " (Querys)"
        . $referrer);

});

/**
 * Verbindung zur Datenbank
 */
dbi::initialize(); // Neue DB-Verbindung mit Prepared-Statements

if((Env::WARTUNGSMODUS) && !isset($_SESSION['wartungsmodus'])){
    $text = "Die Seite befindet sich im Wartungsmodus";
    include Env::BASE_PATH . '/templates/errors/error.tmp.php';
    die();
}