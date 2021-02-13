<?php

class Config
{
    /**
     *  Webroot fürs Erstellen von absoluten Links
     */
    const BASE_LINK = 'http://localhost/einrad.hockey/public';
    /**
     *  Wurzelverzeichnis in welchem einrad.hockey liegt
     */
    const BASE_PATH = 'C:\xampp\htdocs\einrad.hockey';

    /**
     * SQL-Datenbank Zugangsdaten
     */
    const HOST_NAME = 'localhost';
    const DATABASE = 'dummy-db';
    const USER_NAME = 'root';
    const PASSWORD = '';

    /**
     * Mailserver
     */
    const ACTIVATE_EMAIL = false; // Bei True, werden Emails tatsächlich versendet, bei false debugging
    const SMTP_HOST = 'HOST';
    const SMTP_USER = 'test@einrad.hockey';
    const SMTP_PW = 'PW';
    const SMTP_PORT = 666;

    /**
     * Mailadressen
     */
    const LAMAIL = 'liga@einrad.hockey';
    const LAMAIL_ANTWORT = 'la2021@einrad.hockey'; // Wird im BCC gesetzt, bei Mails vom Ligaausschuss
    const TECHNIKMAIL = 'technik@einrad.hockey';
    const SCHIRIMAIL = 'schiri@einrad.hockey';
    const OEFFIMAIL = 'oeffentlichkeitsausschuss@einrad.hockey';

    /**
     * Ligablöcke
     *
     * Reihenfolge bei den Blöcken muss immer hoch -> niedrig sein
     * Für die Block und Wertzuordnung in der Rangtabelle siehe Tabelle::platz_to_block und Tabelle::platz_to_wertigkeit
     */

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
     * Saison
     */
    const SAISON = 26; // Saison 0 = Jahr 1995;
    const SAISON_ANFANG = '15.08.2020';
    const SAISON_ENDE = '31.10.2021';

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
    const LINK_MODUS = '../dokumente/ligamodus.pdf';
    const LINK_REGELN = '../dokumente/regelwerk.pdf';
    const LINK_MODUS_KURZ = '../dokumente/zusammenfassung_modus.pdf';
    const LINK_REGELN_KURZ = '../dokumente/zusammenfassung_regeln.pdf';
    const LINK_MODUS_KURZ_ENG = '../dokumente/summary_modus.pdf';
    const LINK_REGELN_IUF = '../dokumente/iuf-rulebook-2019.pdf';
    const LINK_TURNIER = '../dokumente/turniermodi.pdf';
    const LINK_DSGVO = '../dokumente/datenschutz-hinweise.pdf';
    const LINK_SPIELPLAENE_ALT = '../dokumente/alte_spielplan_vorlagen.pdf';

    /**
     * Authentification
     */
    public static bool $ligacenter = false; // Befindet sich der Ligaausschuss im Ligacenter?
    public static bool $teamcenter = false; // Befindet sich das Team im Teamcenter?

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