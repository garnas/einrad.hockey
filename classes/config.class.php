<?php

class Config
{
    /**
     * Saison
     */
    public const SAISON = 28; // Saison 0 = Jahr 1995;
    public const SAISON_WECHSEL = "28.06.2022"; // Wichtig für zweites Freilos
    public const SAISON_ANFANG = '15.08.2022';
    public const SAISON_ENDE = '28.05.2023';

    /**
     * Saisontermine
     */
    public const FINALE_EINS = '10.06.2023';
    public const FINALE_ZWEI = '11.06.2023';
    public const FINALE_DREI = '17.06.2023';
    public const FINALE_VIER = '18.06.2023';

    /**
     * Log-Files
     */
    public const LOG_LOGIN = "login.log";
    public const LOG_DB = "db.log";
    public const LOG_KONTAKTFORMULAR = "kontakt.log";
    public const LOG_EMAILS = "emails.log";
    public const LOG_USER = "user.log";
    public const LOG_SCHIRI_UEBUNGSTEST = "schiri_uebungstest.log";
    public const LOG_SCHIRI_PRUEFUNG = "schiri_pruefung.log";


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
     * Reihenfolge ist wichtig!
     */
    public const BLOCK_ALL = ['ABCDEF', 'A', 'AB', 'ABC', 'BC', 'BCD', 'CD', 'CDE', 'DE', 'DEF', 'EF', 'F'];

    /**
     * Mögliche Finalturnier-Blöcke
     * Reihenfolge ist wichtig!
     */
    public const BLOCK_FINALE = ['Finale', 'B-Finale', 'C-Finale', 'Saisonschlussturnier'];

    /**
     * Rangtabellen-Zuordnung
     */
    public const RANG_TO_BLOCK = [
        "A" => [1, 8],
        "AB" => [9, 16],
        "BC" => [17, 24],
        "CD" => [25, 34],
        "DE" => [35, 46],
        "EF" => [47, 58],
        "F" => [59, INF]
    ];

    /**
     * Turnierarten
     */
    public const TURNIER_ARTEN = ['I', 'II'];

    /**
     * Ligagebühr
     */
    public const LIGAGEBUEHR = "30&nbsp;€";

    /**
     * Kontaktcenter
     */
    public const BCC_GRENZE = 12;

}