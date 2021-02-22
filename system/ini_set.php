<?php

// Session-Sicherheit
ini_set('session.cookie_lifetime', '1200');
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite ', '"Strict"');
ini_set('session.cookie_secure', '1');
// Quellen
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
ini_set('error_log', __DIR__ . '/logs/php_error.log');

// Sprache für Zeitformate in Deutsch --> strftime()
setlocale(LC_TIME, 'de_DE@euro', 'de_DE', 'de', 'ge');

// Nur für Localhost-Einstellungen
if (
    file_exists(__DIR__ . '/../ini_set_localhost_nicht_hochladen.php')
    && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])
) {
    require_once __DIR__ . '/../ini_set_localhost_nicht_hochladen.php';
}