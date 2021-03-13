<?php

/**
 * Klasse für Enviroment-Variablen
 * wird in init.php vor dem Autoloader required
 *
 * ANLEITUNG
 * (1) Erstelle eine Datei env.php im selben Verzeichnis wie diese Datei (dies ist unser Root-Verzeichnis)
 * (2) Kopiere den Code dieser Datei hier in deine neue env.php
 * (3) Passe die Einstellungen in env.php entsprechend deiner Entwicklungsumgebung an (Mailsachen erstmal unwichtig).
 * (4) Deine Einstellungen in env.php liegen im Gitignore und werden nicht auf Github hochgeladen.
 */
class env
{
    /**
     * Webadresse für Verlinkungen, unabhängig der Ordnerstruktur.
     * Auf dem Server ist der public-Ordner mit der Domain verknüpft.
     */
    public const BASE_URL = 'http://localhost/einrad.hockey/public';

    /**
     * Basispfad für includes, requires
     * Root-Pfad von deinem System in den Ordner dieser Datei
     */
    public const BASE_PATH = __DIR__;

    /**
     * SQL-Datenbank Zugangsdaten
     */
    public const HOST_NAME = 'localhost';
    public const DATABASE = 'db_localhost';
    public const USER_NAME = 'root';
    public const PASSWORD = '';

    /**
     * Mailserver
     *
     * Bei ACTIVATE_EMAIL = true wird versucht Emails zu versenden, bei false werden Debugging-Infos ausgegeben.
     * Dies ist zum Testen der Mailfunktion auf test.einrad.hockey.
     */
    public const ACTIVATE_EMAIL = false;
    public const SMTP_HOST = '--';
    public const SMTP_USER = '--';
    public const SMTP_PW = '--';
    public const SMTP_PORT = 0;

    /**
     * Mailadressen
     */
    public const LAMAIL = 'test@einrad.hockey';
    public const LAMAIL_ANTWORT = 'test@einrad.hockey'; // Wird im BCC gesetzt, bei Mails vom Ligaausschuss
    public const TECHNIKMAIL = 'test@einrad.hockey';
    public const SCHIRIMAIL = 'test@einrad.hockey';
    public const OEFFIMAIL = 'test@einrad.hockey';

    /**
     * Wartungsmodus
     *
     * Bei true wird die Seite wird für Besucher gesperrt. Wenn du freeme.php im public-Ordner aufrufst, wird die
     * Webseite über die Session für dich freigeschaltet.
     */
    public const WARTUNGSMODUS = false;
}