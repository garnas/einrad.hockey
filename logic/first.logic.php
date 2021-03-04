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
 * Security-Header
 */
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Referrer-Policy: no-referrer-when-downgrade');

/**
 * Session starten
 */
session_start();

/**
 * Session-Hijacking erschweren
 * https://owasp.org/www-community/attacks/Session_fixation
 * https://www.php.net/session_regenerate_id
 *
 * Bei mangelnder Performance session_regenerate_id nur bei Login und Logout einrichten.
 */
if (
    isset($_SESSION['destroyed'])
    && $_SESSION['destroyed'] < time() - 15
) {
    unset ($_SESSION['logins']);
    trigger_error("Zugriff auf ungültige Session-ID.", E_USER_ERROR);
}

$_SESSION['destroyed'] = time(); // Legt den Destroy-Zeitstempel fest

session_regenerate_id(); // Kopiert die bestehende Session

unset($_SESSION['destroyed']); // Die neue Session braucht keinen Destroy-Zeitstempel

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
 * Wartungsmodus
 */
if ((Env::WARTUNGSMODUS) && !isset($_SESSION['wartungsmodus'])) {
    $_SESSION['error']['text'] = 'Die Seite befindet sich im Wartungsmodus';
    Helper::reload('errors/500.php');
    die(
        "<div style='text-align:center'>"
            . "<h1>" . Env::BASE_URL . " ist im Wartungsmodus.</h1>"
            . "<p>Ligaausschuss:<br> " . Env::LAMAIL . "</p>"
            . "<p>Technikausschuss:<br> " . Env::TECHNIKMAIL . "</p>"
        . "</div>"
    );
}

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
        !(
            file_exists(__DIR__ . '/../nur_localhost_nicht_hochladen.php')
            && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])
        )
        // Es lag ein Fehler vor
        && ($error = error_get_last()) !== null
    ) {

        if ($error['type'] === E_USER_ERROR) {
            $_SESSION['error']['text'] = $error['message'];
            $_SESSION['error']['url'] = $_SERVER['REQUEST_URI'];
            Helper::reload("/errors/409.php");
        } else if (
            $error['type'] === E_ERROR
        ) {
            $_SESSION['error']['url'] = $_SERVER['REQUEST_URI'];
            Helper::reload("/errors/500.php");
        }

    }
    // Logs der Besucher

    // Referrer falls relevant
    if (strpos(Env::BASE_URL, $_SERVER['SERVER_NAME']) > 0) {
        $referrer = " | " . ($_SERVER['HTTP_REFERER'] ?? '') . " (Referrer)";
    } else {
        $referrer = '';
    }

    // Logs schreiben
    Helper::log("user.log",
        $_SERVER['REQUEST_URI']
        . " | " . round(microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"], 3) . " s (Load)"
        . " | " . dbWrapper::$query_count . " (Querys)"
        . $referrer);

});


/**
 * Verbindung zur Datenbank
 */
dbi::initialize(); // Neue DB-Verbindung mit Prepared-Statements

