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
 * Verbindung zur Datenbank
 */
$verbindung_zur_datenbank = new db;
new dbi();


/**
 * Sanitizing von $_POST und $_GET
 */
if (!empty($_POST)) {
    $_POST = db::sanitize($_POST);
}
if (!empty($_GET)) {
    $_GET = db::sanitize($_GET);
    $_GET = db::escape($_GET); //XSS über Url. Damit keine Entities in der Datenbank gespeichert werden, sollte man $_GET nicht ohne weiteres in der Datenbank speichern.
}

/**
 * Nach einiger Zeit ein neues Hintergrundbild in der Navigation anzeigen
 */
if (!isset($_SESSION['neues_bild'])) {
    $_SESSION['neues_bild'] = Config::time_offset();
}
if (!isset($_SESSION['hintergrund']) or (Config::time_offset() - $_SESSION['neues_bild']) > 600) {
    //https://stackoverflow.com/questions/1761252/how-to-get-random-image-from-directory-using-php 
    $imagesDir = '../bilder/hintergrund/';
    $images = glob($imagesDir . '*.{jpg,JPG,jpeg,png,gif}', GLOB_BRACE);
    $randomImage = $images[array_rand($images)];

    $_SESSION['hintergrund'] = $randomImage;
    $_SESSION['neues_bild'] = Config::time_offset();
}

/**
 * Geben an, ob man sich in einem der Center befindet.
 * Werden in sesses.logic.php während der Authentifikation überschrieben.
 */
$teamcenter = $ligacenter = false;