<?php
/**
 * Diese Datei soll nicht auf dem Server liegen.
 * Sie überschreibt PHP-Einstellungen in init.php
 */
ini_set('session.cookie_secure', '0'); // $_SESSION Funktioniert auch ohne https
ini_set('display_errors', 'On'); // Fehler werden angzeigt und nicht nur geloggt