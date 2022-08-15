<?php

class Nav
{

    /**
     * Iterator für die Navigation, um globale Variablen zu verhindern.
     *
     * @var array
     */
    public static array $link;

    /**
     * Alle Links die mit dem Betrieb der Liga zu tun haben.
     *
     * @return string[][]
     */
    public static function get_liga(): array
    {
        return [
            [Env::BASE_URL . "/liga/saisontermine.php", "Saisontermine"],
            [Env::BASE_URL . "/liga/turniere.php", "Turniere"],
            [Env::BASE_URL . "/liga/ergebnisse.php", "Ergebnisse"],
            [Env::BASE_URL . "/liga/teams.php", "Teams"],
            [Env::BASE_URL . "/liga/tabelle.php#meister", "Meisterschaftstabelle"],
            [Env::BASE_URL . "/liga/tabelle.php#rang", "Rangtabelle"],
            [Env::BASE_URL . "/liga/statistik.php", "Statistik"]
        ];
    }

    /**
     * Links für allgemeine Informationen über die Liga
     *
     * @return string[][]
     */
    public static function get_info(): array
    {
        return [
            [Env::BASE_URL . "/liga/neues.php", "Neuigkeiten"],
            [Env::BASE_URL . "/liga/ueber_uns.php", "Interesse?"],
            [Env::BASE_URL . "/liga/teams.php", "Teams"],
            [Env::BASE_URL . "/liga/ligakarte.php", "Ligakarte"],
            [self::LINK_FORUM . "index.php/board,7.0.html", "Rundschreibenarchiv"],
            [self::LINK_FORUM, "Forum"]
        ];
    }

    /**
     * Links für den Modus und die Funktionsweise der Liga
     *
     * @return string[][]
     */
    public static function get_modus(): array
    {
        return [
            [Env::BASE_URL . "/schiricenter/schiri_infos.php", "Schiritest"],
            [Env::BASE_URL . "/liga/dokumente.php", "Dokumente"],
            [Env::BASE_URL . "/liga/ligaleitung.php", "Ligaleitung"],
        ];
    }


    /**
     * Teamcenterlinks für die Navigation
     *
     * @return string[][]
     */
    public static function get_teamcenter(): array
    {
        $class_text_color = (isset($_SESSION['logins']['team'])) ? "" : "w3-text-grey";
        $links = [
            [Env::BASE_URL . "/teamcenter/tc_start.php", "Start", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_terminseite_erstellen.php", Html::icon("fiber_new") . " Teamtermine", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_turnierliste_anmelden.php", "Turnieranmeldung", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_turnier_erstellen.php", "Turnier erstellen", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_turnierliste_verwalten.php", "Eigene Turniere", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_neuigkeit_eintragen.php", "Neuigkeiten eintragen", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_neuigkeit_liste.php", "Neuigkeit bearbeiten", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_kontaktcenter.php", "Kontaktcenter", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_teamdaten.php", "Teamdaten", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_kader.php", "Kader", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_pw_aendern.php", "Passwort ändern", $class_text_color],
        ];
        if (isset($_SESSION['logins']['team'])) {
            $links[] =
                [Env::BASE_URL . "/teamcenter/tc_logout.php", Html::icon("logout") . " Logout", $class_text_color];
        }else{
            array_unshift(
                $links,
                [Env::BASE_URL . "/teamcenter/tc_login.php", Html::icon("login") . " Login", "w3-text-black"]
            );
        }
        return $links;
    }

    /**
     * Sonstige Links
     *
     * @return string[][]
     */
    public static function get_sonstiges(): array
    {
        return [
            [Env::BASE_URL . "/liga/ueber_uns.php", "Über uns"],
            [Env::BASE_URL . "/liga/jubilaeum.php", "25 Jahre Liga"],
            [Env::BASE_URL . "/liga/archiv.php", "Archiv"],
            [Env::BASE_URL . "/ligacenter/lc_login.php", "Ligacenter"],
            [Env::BASE_URL . "/liga/kontakt.php", "Kontakt"],
            [Env::BASE_URL . "/liga/datenschutz.php", "Datenschutz"],
            [Env::BASE_URL . "/liga/impressum.php", "Impressum"],
        ];
    }

    /**
     * Links für die Startseite des Ligacenters
     *
     * @return string[][]
     */
    public static function get_lc_start(): array
    {
        return [
            [Env::BASE_URL . "/schiricenter/schiritest_erstellen.php", "Schiritest", "w3-secondary"],
            [Env::BASE_URL . "/ligacenter/lc_turnierliste.php", "Turniere verwalten", "w3-primary"],
            [Env::BASE_URL . "/ligacenter/lc_turnier_erstellen.php", "Turnier erstellen", "w3-primary"],
            [Env::BASE_URL . "/ligacenter/lc_kontaktcenter.php", "Kontaktcenter", "w3-tertiary"],
            [Env::BASE_URL . "/ligacenter/lc_neuigkeit_eintragen.php", "Neuigkeit eintragen", "w3-tertiary"],
            [Env::BASE_URL . "/liga/neues.php", "Neuigkeit bearbeiten", "w3-tertiary"],
            [Env::BASE_URL . "/ligacenter/lc_teams_uebersicht.php", "Teams-Übersicht", "w3-green"],
            [Env::BASE_URL . "/ligacenter/lc_team_erstellen.php", "Team erstellen", "w3-green"],
            [Env::BASE_URL . "/ligacenter/lc_teamdaten_aendern.php", "Teamdaten verwalten", "w3-green"],
            [Env::BASE_URL . "/ligacenter/lc_teamstrafe.php", "Teamstrafen", "w3-green"],
            [Env::BASE_URL . "/ligacenter/lc_kader.php", "Teamkader verwalten", "w3-green"],
            [Env::BASE_URL . "/ligacenter/lc_spieler_aendern.php", "Spieler verwalten", "w3-green"],
            [Env::BASE_URL . "/ligacenter/lc_pw_aendern.php", "Passwort ändern", "w3-grey"],
            [Env::BASE_URL . "/ligacenter/lc_admin.php", "Admin", "w3-grey"],
            [Env::BASE_URL . "/ligacenter/lc_logout.php", "Logout", "w3-grey"]
        ];
    }

    /**
     * Links für die Startseite des Teamcenters
     *
     * @return string[][]
     */
    public static function get_tc_start(): array
    {
        return [
            [Env::BASE_URL . "/teamcenter/tc_terminseite_erstellen.php", "Teamtermine", "w3-green"],
            [Env::BASE_URL . "/teamcenter/tc_turnierliste_anmelden.php", "Turnier- anmeldung", "w3-primary"],
            [Env::BASE_URL . "/teamcenter/tc_turnier_erstellen.php", "Turnier erstellen", "w3-primary"],
            [Env::BASE_URL . "/teamcenter/tc_turnierliste_verwalten.php", "Eigene Turniere", "w3-primary"],
            [Env::BASE_URL . "/teamcenter/tc_neuigkeit_eintragen.php", "Neuigkeiten eintragen", "w3-tertiary"],
            [Env::BASE_URL . "/teamcenter/tc_neuigkeit_liste.php", "Neuigkeit bearbeiten", "w3-tertiary"],
            [Env::BASE_URL . "/teamcenter/tc_kontaktcenter.php", "Kontaktcenter", "w3-tertiary"],
            [Env::BASE_URL . "/teamcenter/tc_teamdaten.php", "Teamdaten", "w3-green"],
            [Env::BASE_URL . "/teamcenter/tc_kader.php", "Kader", "w3-green"],
            [Env::BASE_URL . "/teamcenter/tc_pw_aendern.php", "Passwort ändern", "w3-grey"],
            [Env::BASE_URL . "/teamcenter/tc_logout.php", "Logout", "w3-grey"],
        ];
    }

    /**
     *  Ligalinks
     */
    public const LINK_TERMINPLANER = 'https://team.einrad.hockey';
    public const LINK_FORUM = 'https://forum.einrad.hockey/';
    public const LINK_FACE = 'https://www.facebook.com/DeutscheEinradhockeyliga';
    public const LINK_GIT = 'https://github.com/garnas/einrad.hockey';
    /**
     * Andere Ligen
     */
    public const LINK_AUSTRALIA = 'https://hockey.unicycling.org.au/';
    public const LINK_REGELN_IUF = Env::BASE_URL . '/dokumente/iuf-rulebook-2019.pdf';
    public const LINK_REGELN = Env::BASE_URL . '/dokumente/regelwerk.pdf';
    /**
     * Einradverbände
     */
    public const LINK_EV = 'https://www.einradverband.de/';
    public const LINK_ARCHIV = 'https://archiv.einrad.hockey/archiv/index.html';
    public const LINK_INSTA = 'https://www.instagram.com/einradhockeyde/';
    public const LINK_EV_BY = 'http://einradverband-bayern.de/';
    public const LINK_REGELN_KURZ = Env::BASE_URL . '/dokumente/zusammenfassung_regeln.pdf';
    public const LINK_MODUS_KURZ = Env::BASE_URL . '/dokumente/zusammenfassung_modus.pdf';
    public const LINK_TURNIER = Env::BASE_URL . '/dokumente/turniermodi.pdf';
    /**
     * Dokumente
     */
    public const LINK_MODUS = Env::BASE_URL . '/dokumente/ligamodus.pdf';
    public const LINK_SCHIRIWESEN = Env::BASE_URL . '/dokumente/schiriwesen.pdf';
    public const LINK_MODUS_ENTWURF = Env::BASE_URL . '/dokumente/ligamodus_entwurf.pdf';
    public const LINK_FINALE = Env::BASE_URL . '/dokumente/finalturnier.pdf';
    public const LINK_FINALE_MODI = Env::BASE_URL . '/dokumente/finalturnier_spielmodi.pdf';
    public const LINK_CHECK_XLSX = Env::BASE_URL . '/dokumente/checkliste_einradhockeyturnier_fuer_ausrichter.xlsx';
    public const LINK_CHECK_PDF = Env::BASE_URL . '/dokumente/checkliste_einradhockeyturnier_fuer_ausrichter.pdf';
    public const LINK_HYGIENE = Env::BASE_URL . '/dokumente/empfohlenes_hygienekonzept.pdf';
    public const LINK_DSGVO = Env::BASE_URL . '/dokumente/datenschutz-hinweise.pdf';
    public const LINK_FRANCE = 'https://monocycle.info/ligue-de-monocycle-basket-remaniement-co/';
    public const LINK_SPIELPLAENE_ALT = Env::BASE_URL . '/dokumente/alte_spielplan_vorlagen.zip';
    public const LINK_EV_SH = 'https://www.einradverband-sh.de/';
    public const LINK_IUF = 'https://unicycling.org/';
    public const LINK_MODUS_KURZ_ENG = Env::BASE_URL . '/dokumente/summary_modus.pdf';
    public const LINK_SWISS = 'https://www.swiss-iuc.ch/Wettkaempfe/Einradhockey';
}
