<?php

/**
 * Dieses Dokument muss immer als Erstes für die Logik geladen werden
 **/

/**
 * Zeitzone festlegen und Sprache festlegen
 */
date_default_timezone_set('Europe/Berlin');
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');

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
 * Nach einiger Zeit ein neues Hintergrundbild in der Navigation anzeigen
 */
if (!isset($_SESSION['neues_bild'])) {
    $_SESSION['neues_bild'] = time();
}
if (!isset($_SESSION['hintergrund']) or (time() - $_SESSION['neues_bild']) > 600) {
    // https://stackoverflow.com/questions/1761252/how-to-get-random-image-from-directory-using-php
    $imagesDir = '../bilder/hintergrund/';
    $images = glob($imagesDir . '*.{jpg,JPG,jpeg,png,gif}', GLOB_BRACE);
    $randomImage = $images[array_rand($images)];

    $_SESSION['hintergrund'] = $randomImage;
    $_SESSION['neues_bild'] = time();
}

/**
 * Verbindung zur Datenbank
 */
//$verbindung_zur_datenbank = new db; // Alte DB
dbi::initialize(); // Neue DB-Verbindung mit Prepared-Statements

/**
 * Geben an, ob man sich in einem der Center befindet.
 * Werden in session.logic.php während der Authentifikation überschrieben.
 */
Config::$teamcenter = Config::$ligacenter = false;