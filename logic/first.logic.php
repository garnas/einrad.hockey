<?php
//declare(strict_types=1); Abklären mit Joschi

//Dieses Dokument muss immer als Erstes für die Logik geladen werden
//Session hauptsächlich für Authentifizierung
session_start();
session_regenerate_id();

//Quelle: https://www.php-kurs.com/php-datumsausgabe-deutsch.htm
setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');

//Läd automatisch die benötigten Klassen, wichtig ist, dass der relative Pfad stimmt
//Quelle https://www.php.net/manual/de/language.oop5.autoload.php
spl_autoload_register(function ($class) {
    if ($class != 'PHPMailer'){ //Der PHPMailer muss als externes Framework manuell über phpmailer.logic.php geladen werden
        $class = strtolower($class);
        include __DIR__ . '/../classes/' . $class . '.class.php';
    }        
});

//SQL-Connection wird hergestellt und automatisch wieder gechlossen
$verbindung_zur_datenbank = new db;

//Sanitizing von $_POST und $_GET
if (!empty($_POST)){
    $_POST = db::sanitize($_POST);
}
if (!empty($_GET)){
    $_GET = db::sanitize($_GET);
    $_GET = db::escape($_GET); //XSS über Url. Damit keine Entities in der Datenbank gespeichert werden, sollte man $_GET nicht in der Datenbank speichern.
}

//Nach einer Zeit ein neues Hintergrundbild für die Navigation
if (!isset($_SESSION['neues_bild'])){
    $_SESSION['neues_bild'] = Config::time_offset();
}
if (!isset($_SESSION['hintergrund']) or (Config::time_offset() - $_SESSION['neues_bild']) > 600){
    //https://stackoverflow.com/questions/1761252/how-to-get-random-image-from-directory-using-php 
    $imagesDir = '../bilder/hintergrund/';
    $images = glob($imagesDir . '*.{jpg,JPG,jpeg,png,gif}', GLOB_BRACE);
    $randomImage = $images[array_rand($images)];

    $_SESSION['hintergrund'] = $randomImage;
    $_SESSION['neues_bild'] = Config::time_offset();
}