<?php
/**
 * Dieses Dokument muss immer als Erstes für die Logik geladen werden
 */

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
 * Quelle: https://www.php.net/manual/de/language.oop5.autoload.php
 */
spl_autoload_register(
    function ($class) {
        $class = strtolower($class);
        include __DIR__ . '/../classes/' . $class . '.class.php';
    }
);

/**
 * Verbindung zur Datenbank
 */
$verbindung_zur_datenbank = new db; // Alte DB
dbi::initialize(); // Neue DB-Verbindung mit Prepared-Statements



