<?php

// Dieses Dokument muss immer als Erstes für die Logik geladen werden
// Die Reihenfolge, wann was geladen wird, ist wichtig

/**
 * PHP-Einstellungen vornehmen
 */
// Session-Sicherheit
ini_set('session.cookie_lifetime', '7200'); // Entspricht 2h
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite ', '"Strict"');
ini_set('session.cookie_secure', '1');
// https://www.php.net/manual/de/session.security.ini.php
// https://www.php.net/manual/de/features.session.security.management.php

// Sonstige Einstellungen
ini_set('date.timezone', 'Europe/Berlin');
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '90');
ini_set('error_reporting', E_ALL);
ini_set('log_errors', 'On');
ini_set('display_errors', 'Off');
ini_set('error_log', __DIR__ . '/system/logs/errors.log');


/**
 * Enviroment-Variablen laden
 */
require_once __DIR__ . '/env.php';


// Nur für Localhost-Einstellungen
if (Env::IS_LOCALHOST) {
    ini_set('session.cookie_secure', '0'); // $_SESSION Funktioniert auch ohne https
    ini_set('display_errors', 'On'); // Fehler werden angzeigt und nicht nur geloggt
    ini_set('max_execution_time', '0'); // Für Debugging
}


/**
 * Security-Header
 */
//header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Referrer-Policy: no-referrer-when-downgrade');


/**
 * Autoloader der Klassen
 *
 * https://www.php.net/manual/de/language.oop5.autoload.php
 */
spl_autoload_register(
    static function ($class) {
        $class = strtolower($class);
        $array = explode('\\', $class);
        $className = $array[array_key_last($array)];
        $path = Env::BASE_PATH . '/classes/' . $className . '.class.php';
        if (file_exists($path)) {
            include $path;
        }
    }

);

/**
 * Diese Funktion wird nach Beendigung des Skriptes ausgeführt.
 * Sie logt User und gibt Fehlerseiten aus.
 *
 * https://phpdelusions.net/articles/error_reporting#error_page
 */
register_shutdown_function(static function () {

    // Fehlerseiten aufrufen

    if (
        // Kein Fehlerhandling für localhost (Debugging)
        !Env::IS_LOCALHOST
        // Es lag ein Fehler vor
        && ($error = error_get_last()) !== null
    ) {
        if ($error['type'] === E_USER_ERROR) { // Ein von uns hervorgerufener Fehler (zB. falscher Spielplan)
            $_SESSION['error']['text'] = $error['message'];
            $_SESSION['error']['url'] = $_SERVER['REQUEST_URI'];
            Helper::reload("/errors/409.php");
        } else if ($error['type'] === E_ERROR) { // Ein von PHP hervorgerufener Fehler (zB. durch Null geteilt)
            Helper::reload("/errors/500.html");
        }
    }

    // Logs der Besucher

    // Referrer falls relevant
    if (
        empty($_SERVER['HTTP_REFERER'])
        || str_contains($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME'])
    ) {
        $referrer = '';
    } else {
        $referrer = " | " . ($_SERVER['HTTP_REFERER'] ?? '') . " (Referrer)";
    }

    // Logs schreiben
    Helper::log(Config::LOG_USER,
        $_SERVER['REQUEST_URI']
        . " | " . round(microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"], 3) . " s (Load)"
        . " | " . ndbWrapper::$query_count . " (Querys)"
        . $referrer,
        true);
});

/**
 * Session starten und Session-Hijacking erschweren
 *
 * https://owasp.org/www-community/attacks/Session_fixation
 * https://www.php.net/session_regenerate_id
 *
 * Bei mangelnder Performance session_regenerate_id nur bei Login und Logout einrichten.
 * Bei häufigen ungültigen Sessions wegen einer schlechten Inet-Verbindung von 15 Sekunden hoch gehen.
 */
session_start();
if (
    isset($_SESSION['destroyed'])
    && $_SESSION['destroyed'] < time()  - 15
) {
    session_unset();
    trigger_error("Ungültige Session-ID.", E_USER_ERROR);
}

$_SESSION['destroyed'] = time(); // Legt den Destroy-Zeitstempel fest
session_regenerate_id(); // Kopiert die bestehende Session
unset($_SESSION['destroyed']); // Die neue Session braucht keinen Destroy-Zeitstempel

/**
 * Verbindung zur Datenbank
 */
db::initialize(); // Neue DB-Verbindung mit Prepared-Statements


/**
 * Sprache für Zeitformate in Deutsch --> strftime()
 */
setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');


/**
 * Wartungsmodus
 * Besuche freeme.php, um die Seite im Wartungsmodus trotzdem verwenden zu können.
 */
if (
    (Env::WARTUNGSMODUS)
    && !isset($_SESSION['wartungsmodus'])
) {
    require(__DIR__ . '/public/errors/wartungsmodus.php');
    die();
}

require_once __DIR__ . '/vendor/autoload.php';

function e(mixed $value) {
    return db::escape($value);
}

App\Repository\DoctrineWrapper::setup();