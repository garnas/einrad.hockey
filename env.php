<?php

/**
 * Klasse für Enviroment-Variablen
 * wird in first.logic vor dem Autoloader required
 */
class env
{
    /**
     * Webadresse für Verlinkungen, unabhängig der Ordnerstruktur
     * Auf dem Server ist der Public Ordner mit der Domain verknüpft
     */
    const BASE_URL = 'http://localhost/einrad.hockey/public';

    /**
     * Basispfad für includes, requires
     */
    const BASE_PATH = __DIR__; // Das Verzeichnis dieser Datei ist der BASE_PATH

    /**
     * SQL-Datenbank Zugangsdaten
     */
    const HOST_NAME = 'localhost';
    const DATABASE = 'dummy-db';
    const USER_NAME = 'root';
    const PASSWORD = '';

    /**
     * Mailserver
     *
     * Bei ACTIVATE_EMAIL = true wird versucht Emails zu versenden, bei false werden Debugging-Infos ausgegeben
     */
    const ACTIVATE_EMAIL = false;
    const SMTP_HOST = '--';
    const SMTP_USER = '--';
    const SMTP_PW = '--';
    const SMTP_PORT = 0;

    /**
     * Mailadressen
     */
    const LAMAIL = 'test@einrad.hockey';
    const LAMAIL_ANTWORT = 'test@einrad.hockey'; // Wird im BCC gesetzt, bei Mails vom Ligaausschuss
    const TECHNIKMAIL = 'test@einrad.hockey';
    const SCHIRIMAIL = 'test@einrad.hockey';
    const OEFFIMAIL = 'test@einrad.hockey';
}