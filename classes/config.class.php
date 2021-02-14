<?php

class Config
{
    /**
     * Variablen aus Env.php aus dem Root Verzeichnis
     * Diese stehen nur noch hier für die Kompatibilität
     */
    const BASE_URL = Env::BASE_URL;
    const BASE_PATH = Env::BASE_PATH;
    const HOST_NAME = Env::HOST_NAME;
    const DATABASE = Env::DATABASE;
    const USER_NAME = Env::USER_NAME;
    const PASSWORD = Env::PASSWORD;
    const ACTIVATE_EMAIL = Env::HOST_NAME; // Bei True, werden Emails tatsächlich versendet, bei false debugging
    const SMTP_HOST = Env::SMTP_HOST;
    const SMTP_USER = Env::SMTP_USER;
    const SMTP_PW = Env::SMTP_PW;
    const SMTP_PORT = Env::SMTP_PORT;
    const LAMAIL = Env::LAMAIL;
    const LAMAIL_ANTWORT = Env::LAMAIL_ANTWORT; // Wird im BCC gesetzt, bei Mails vom Ligaausschuss
    const TECHNIKMAIL = Env::TECHNIKMAIL;
    const SCHIRIMAIL = Env::SCHIRIMAIL;
    const OEFFIMAIL = Env::OEFFIMAIL;

    /**
     * Ligablöcke
     *
     * Reihenfolge bei den Blöcken muss immer hoch -> niedrig sein
     * Für die Block und Wertzuordnung in der Rangtabelle siehe Tabelle::platz_to_block und Tabelle::platz_to_wertigkeit
     */

    /**
     * Saison
     */
    const SAISON = 26; // Saison 0 = Jahr 1995;
    const SAISON_ANFANG = '15.08.2020';
    const SAISON_ENDE = '31.10.2021';

    /**
     * Mögliche Team-Blöcke
     */
    const BLOCK = ['A', 'AB', 'BC', 'CD', 'DE', 'EF', 'F'];

    /**
     * Mögliche Turnier-Blöcke
     */
    const BLOCK_ALL = ["ABCDEF", 'A', 'AB', 'ABC', 'BC', 'BCD', 'CD', 'CDE', 'DE', 'DEF', 'EF', 'F'];

    /**
     * Ligagebühr
     */
    const LIGAGEBUEHR = "30&nbsp;€";

    /**
     *  Ligalinks
     */
    const LINK_FORUM = 'https://forum.einrad.hockey/';
    const LINK_ARCHIV = 'https://archiv.einrad.hockey/archiv/index.html';
    const LINK_INSTA = 'https://www.instagram.com/einradhockeyde/';
    const LINK_FACE = 'https://www.facebook.com/DeutscheEinradhockeyliga';

    /**
     * Andere Ligen
     */
    const LINK_AUSTRALIA = 'https://hockey.unicycling.org.au/';
    const LINK_SWISS = 'https://www.swiss-iuc.ch/Wettkaempfe/Einradhockey';
    const LINK_FRANCE = 'https://monocycle.info/ligue-de-monocycle-basket-remaniement-co/';
    const LINK_IUF = 'https://unicycling.org/';

    /**
     * Einradverbände
     */
    const LINK_EV = 'https://www.einradverband.de/';
    const LINK_EV_SH = 'https://www.einradverband-sh.de/';
    const LINK_EV_BY = 'http://einradverband-bayern.de/';

    /**
     * Dokumente
     */
    const LINK_MODUS = Env::BASE_URL . '/dokumente/ligamodus.pdf';
    const LINK_REGELN = Env::BASE_URL . '/dokumente/regelwerk.pdf';
    const LINK_MODUS_KURZ = Env::BASE_URL . '/dokumente/zusammenfassung_modus.pdf';
    const LINK_REGELN_KURZ = Env::BASE_URL . '/dokumente/zusammenfassung_regeln.pdf';
    const LINK_MODUS_KURZ_ENG = Env::BASE_URL . '/dokumente/summary_modus.pdf';
    const LINK_REGELN_IUF = Env::BASE_URL . '/dokumente/iuf-rulebook-2019.pdf';
    const LINK_TURNIER = Env::BASE_URL . '/dokumente/turniermodi.pdf';
    const LINK_DSGVO = Env::BASE_URL . '/dokumente/datenschutz-hinweise.pdf';
    const LINK_SPIELPLAENE_ALT = Env::BASE_URL . '/dokumente/alte_spielplan_vorlagen.pdf';

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