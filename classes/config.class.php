<?php

class Config
{
    /**
     * Variablen aus Env.php aus dem Root Verzeichnis
     * Diese stehen nur noch hier für die Kompatibilität
     */
    public const BASE_URL = Env::BASE_URL;
    public const BASE_PATH = Env::BASE_PATH;
    public const HOST_NAME = Env::HOST_NAME;
    public const DATABASE = Env::DATABASE;
    public const USER_NAME = Env::USER_NAME;
    public const PASSWORD = Env::PASSWORD;
    public const ACTIVATE_EMAIL = Env::HOST_NAME; // Bei True, werden Emails tatsächlich versendet, bei false debugging
    public const SMTP_HOST = Env::SMTP_HOST;
    public const SMTP_USER = Env::SMTP_USER;
    public const SMTP_PW = Env::SMTP_PW;
    public const SMTP_PORT = Env::SMTP_PORT;
    public const LAMAIL = Env::LAMAIL;
    public const LAMAIL_ANTWORT = Env::LAMAIL_ANTWORT; // Wird im BCC gesetzt, bei Mails vom Ligaausschuss
    public const TECHNIKMAIL = Env::TECHNIKMAIL;
    public const SCHIRIMAIL = Env::SCHIRIMAIL;
    public const OEFFIMAIL = Env::OEFFIMAIL;

    /**
     * Saison
     */
    public const SAISON = 26; // Saison 0 = Jahr 1995;
    public const SAISON_ANFANG = '15.08.2020';
    public const SAISON_ENDE = '31.10.2021';

    /**
     * Log-Files
     */
    public const LOG_LOGIN = "login.log";
    public const LOG_DB = "db.log";
    public const LOG_KONTAKTFORMULAR = "kontakt.log";
    public const LOG_EMAILS = "emails.log";


    /**
     * Ligablöcke
     *
     * Reihenfolge bei den Blöcken muss immer hoch -> niedrig sein
     * Für die Block und Wertzuordnung in der Rangtabelle siehe Tabelle::rang_to_block und Tabelle::rang_to_wertigkeit
     *
     */

     /**
     * Mögliche Team-Blöcke
     */
    public const BLOCK = ['A', 'AB', 'BC', 'CD', 'DE', 'EF', 'F'];

    /**
     * Mögliche Turnier-Blöcke
     */
    public const BLOCK_ALL = ["ABCDEF", 'A', 'AB', 'ABC', 'BC', 'BCD', 'CD', 'CDE', 'DE', 'DEF', 'EF', 'F'];

    /**
     * Rangtabellen-Zuordnung
     */
    public const RANG_TO_BLOCK = [
        "A" => [1, 6],
        "AB" => [7, 13],
        "BC" => [14, 21],
        "CD" => [22, 31],
        "DE" => [32, 43],
        "EF" => [44, 57],
        "F" => [58, INF]
    ];

    /**
     * Ligagebühr
     */
    public const LIGAGEBUEHR = "30&nbsp;€";

}