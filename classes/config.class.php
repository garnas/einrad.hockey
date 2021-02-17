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


    /**
     * Ligablöcke
     *
     * Reihenfolge bei den Blöcken muss immer hoch -> niedrig sein
     * Für die Block und Wertzuordnung in der Rangtabelle siehe Tabelle::platz_to_block und Tabelle::platz_to_wertigkeit
     *
     * Mögliche Team-Blöcke
     */
    public const BLOCK = ['A', 'AB', 'BC', 'CD', 'DE', 'EF', 'F'];
    /**
     * Mögliche Turnier-Blöcke
     */
    public const BLOCK_ALL = ["ABCDEF", 'A', 'AB', 'ABC', 'BC', 'BCD', 'CD', 'CDE', 'DE', 'DEF', 'EF', 'F'];

    /**
     * Ligagebühr
     */
    public const LIGAGEBUEHR = "30&nbsp;€";

    /**
     *  Ligalinks
     */
    public const LINK_FORUM = 'https://forum.einrad.hockey/';
    public const LINK_ARCHIV = 'https://archiv.einrad.hockey/archiv/index.html';
    public const LINK_INSTA = 'https://www.instagram.com/einradhockeyde/';
    public const LINK_FACE = 'https://www.facebook.com/DeutscheEinradhockeyliga';

    /**
     * Andere Ligen
     */
    public const LINK_AUSTRALIA = 'https://hockey.unicycling.org.au/';
    public const LINK_SWISS = 'https://www.swiss-iuc.ch/Wettkaempfe/Einradhockey';
    public const LINK_FRANCE = 'https://monocycle.info/ligue-de-monocycle-basket-remaniement-co/';
    public const LINK_IUF = 'https://unicycling.org/';

    /**
     * Einradverbände
     */
    public const LINK_EV = 'https://www.einradverband.de/';
    public const LINK_EV_SH = 'https://www.einradverband-sh.de/';
    public const LINK_EV_BY = 'http://einradverband-bayern.de/';

    /**
     * Dokumente
     */
    public const LINK_MODUS = Env::BASE_URL . '/dokumente/ligamodus.pdf';
    public const LINK_REGELN = Env::BASE_URL . '/dokumente/regelwerk.pdf';
    public const LINK_MODUS_KURZ = Env::BASE_URL . '/dokumente/zusammenfassung_modus.pdf';
    public const LINK_REGELN_KURZ = Env::BASE_URL . '/dokumente/zusammenfassung_regeln.pdf';
    public const LINK_MODUS_KURZ_ENG = Env::BASE_URL . '/dokumente/summary_modus.pdf';
    public const LINK_REGELN_IUF = Env::BASE_URL . '/dokumente/iuf-rulebook-2019.pdf';
    public const LINK_TURNIER = Env::BASE_URL . '/dokumente/turniermodi.pdf';
    public const LINK_DSGVO = Env::BASE_URL . '/dokumente/datenschutz-hinweise.pdf';
    public const LINK_SPIELPLAENE_ALT = Env::BASE_URL . '/dokumente/alte_spielplan_vorlagen.pdf';

    /**
     * Authentification
     * $teamcenter und $ligacenter werden in session_*.logic.php ggf überschrieben
     */
    public static bool $ligacenter = false; // Befindet sich der Ligaausschuss im Ligacenter?
    public static bool $teamcenter = false; // Befindet sich das Team im Teamcenter?
    /**
     * Teamcenter freischalten? (PW geändert, Ligavertreter angegeben?)
     * Ansonsten redirect zu Passwort ändern bzw. Ligavertreter eintragen in session_team.logic.php
     */
    public static bool $teamcenter_no_redirect = false;


    /**
     * HTML-Anzeige
     */
    public static string $page_width = "1020px";
    public static string $titel = 'Deutsche Einradhockeyliga';
    public static string $content
        = 'Jeder Einradhockeybegeisterte soll in der Deutschen Einradhockeyliga die Möglichkeit haben, sein Hobby in '
        . 'einem sportlichen Rahmen auszuüben. Die Einradhockeyliga hat maßgeblich zur Verbreitung von Einradhockey '
        . 'beigetragen und ist in ihrer Art und Konstanz weltweit einzigartig.';
}