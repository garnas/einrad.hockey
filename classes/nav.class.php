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
            [Env::BASE_URL . "/liga/ligaleitung.php", "Ligaleitung"],
            [Env::BASE_URL . "/liga/ligakarte.php", "Ligakarte"],
            [Env::BASE_URL . "/liga/kader.php", "Nationalkader"],
            [Env::LINK_DISCORD, "Discord"]
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
            [Env::BASE_URL . "/teamcenter/tc_terminseite_erstellen.php", "Teamtermine", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_turnierliste_anmelden.php", "Turnieranmeldung", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_turnier_erstellen.php", "Turnier erstellen", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_turnierliste_verwalten.php", "Eigene Turniere", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_neuigkeit_eintragen.php", "Neuigkeiten eintragen", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_neuigkeit_liste.php", "Neuigkeit bearbeiten", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_kontaktcenter.php", "Kontaktcenter", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_teamdaten.php", "Teamdaten", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_kader.php", "Kader", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_antrag.php", "Fördermittel", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_pw_aendern.php", "Passwort ändern", $class_text_color],
        ];
        if (isset($_SESSION['logins']['team'])) {
            $links[] =
                [Env::BASE_URL . "/teamcenter/tc_logout.php", Html::icon("logout") . " Logout", $class_text_color];
        } else {
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
            [Env::BASE_URL . "/liga/tippspiel.php", "Tippspiel"],
            [Env::BASE_URL . "/liga/archiv.php", "Archiv"],
            [Env::BASE_URL . "/login.php", "Ausschusslogin"],
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
        return array(
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
            [Env::BASE_URL . "/ligacenter/lc_checkmail.php", "Mail-Bot", "w3-blue"],
            [Env::BASE_URL . "/ligacenter/lc_logout.php", "Logout", "w3-grey"],
        );
    }

    /**
     * Downloadlinks für die Startseite des Ligacenters
     * 
     * @return string[][]
     */
    public static function get_lc_downloads(): array
    {
        return array(
            [Env::BASE_URL . "/ligacenter/lc_turnierstats.php", "turniere.xlsx", "w3-light-grey"],
            [Env::BASE_URL . "/ligacenter/lc_spielerstats.php", "spieler.xlsx", "w3-light-grey"],
            [Env::BASE_URL . "/ligacenter/lc_teamstats.php", "teams.xlsx", "w3-light-grey"],
            [Env::BASE_URL . "/ligacenter/lc_leihestats.php", "leihe.xlsx", "w3-light-grey"],
            [Env::BASE_URL . "/schiricenter/schiritest_stats.php", "schiritest.xlsx", "w3-light-grey"],
        );
    }

    /**
     * Links für die Startseite des Teamcenters
     *
     * @return string[][]
     */
    public static function get_tc_start(): array
    {
        $links = array(
            [Env::BASE_URL . "/teamcenter/tc_terminseite_erstellen.php", "Teamtermine", "w3-green"],
            [Env::BASE_URL . "/teamcenter/tc_turnierliste_anmelden.php", "Turnieranmeldung", "w3-primary"],
            [Env::BASE_URL . "/teamcenter/tc_turnier_erstellen.php", "Turnier erstellen", "w3-primary"],
            [Env::BASE_URL . "/teamcenter/tc_turnierliste_verwalten.php", "Eigene Turniere", "w3-primary"],
            [Env::BASE_URL . "/teamcenter/tc_turnier_report_liste.php", "Turnierreports", "w3-primary"],
            [Env::BASE_URL . "/teamcenter/tc_neuigkeit_eintragen.php", "Neuigkeiten eintragen", "w3-tertiary"],
            [Env::BASE_URL . "/teamcenter/tc_neuigkeit_liste.php", "Neuigkeit bearbeiten", "w3-tertiary"],
            [Env::BASE_URL . "/teamcenter/tc_kontaktcenter.php", "Kontaktcenter", "w3-tertiary"],
            [Env::BASE_URL . "/teamcenter/tc_teamdaten.php", "Teamdaten", "w3-green"],
            [Env::BASE_URL . "/teamcenter/tc_kader.php", "Kader", "w3-green"],
            [Env::BASE_URL . "/teamcenter/tc_antrag.php", "Fördermittel", "w3-purple"],
            [Env::BASE_URL . "/teamcenter/tc_pw_aendern.php", "Passwort ändern", "w3-grey"],
            [Env::BASE_URL . "/teamcenter/tc_logout.php", "Logout", "w3-grey"],
        );
        return $links;
    }

    /**
     *  Ligalinks
     */
    public const LINK_TERMINPLANER = 'https://team.einrad.hockey';
    public const LINK_FORUM = 'https://discord.gg/jQrFefqz';
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

    public const LINK_SCHIRI_CHECKLIST = Env::BASE_URL . '/dokumente/schiri_checkliste.pdf';
    public const LINK_SCHIRI_LEITLINIE = Env::BASE_URL . '/dokumente/schiri_leitlinie.pdf';
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

    /**
     * Dokumente für das Tippspiel 2025
     */
    public const LINK_TIPPSPIEL_BM_XLSX = Env::BASE_URL . '/dokumente/tippspiel/Tippspiel_B-Meisterschaft_Borken.xlsx';
    public const LINK_TIPPSPIEL_BM_PDF = Env::BASE_URL . '/dokumente/tippspiel/Tippspiel_B-Meisterschaft_Borken.pdf';
    public const LINK_TIPPSPIEL_DM_XLSX = Env::BASE_URL . '/dokumente/tippspiel/Tippspiel_Deutsche_Meisterschaft_Dresden.xlsx';
    public const LINK_TIPPSPIEL_DM_PDF = Env::BASE_URL . '/dokumente/tippspiel/Tippspiel_Deutsche_Meisterschaft_Dresden.pdf';
    public const LINK_TIPPSPIEL_ANLEITUNG_BM = Env::BASE_URL . '/dokumente/tippspiel/Erklaerung_Tippspiel_B-Meisterschaft_Borken.pdf';
    public const LINK_TIPPSPIEL_ANLEITUNG_DM = Env::BASE_URL . '/dokumente/tippspiel/Erklaerung_Tippspiel_Deutsche_Meisterschaft_Dresden.pdf';

    public static function get_oc_start(): array
    {
        return array(
            [Env::BASE_URL . "/oefficenter/oc_kontaktcenter.php", "Kontaktcenter", "w3-tertiary"],
            [Env::BASE_URL . "/oefficenter/oc_neuigkeit_eintragen.php", "Neuigkeit eintragen", "w3-tertiary"],
            [Env::BASE_URL . "/liga/neues.php", "Neuigkeit bearbeiten", "w3-tertiary"],
            [Env::BASE_URL . "/oefficenter/oc_pw_aendern.php", "Passwort ändern", "w3-grey"],
            [Env::BASE_URL . "/oefficenter/oc_logout.php", "Logout", "w3-grey"],
        );
    }
}
