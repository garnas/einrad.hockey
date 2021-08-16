<?php
/**
 * Diese Datei überschreibt PHP-Einstellungen in init.php für den Localhost, wenn Env::IS_LOCALHOST true ist.
 */
ini_set('session.cookie_secure', '0'); // $_SESSION Funktioniert auch ohne https
ini_set('display_errors', 'On'); // Fehler werden angzeigt und nicht nur geloggt