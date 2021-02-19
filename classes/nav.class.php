<?php

class Nav
{
    public static function get_liga(): array
    {
        return [
            [Env::BASE_URL . "/liga/jubilaeum.php", "25 Jahre Liga"],
            [Env::BASE_URL . "/liga/turniere.php", "Turniere"],
            [Env::BASE_URL . "/liga/ergebnisse.php", "Ergebnisse"],
            [Env::BASE_URL . "/liga/teams.php", "Teams"],
            [Env::BASE_URL . "/liga/tabelle.php#meister", "Meisterschaftstabelle"],
            [Env::BASE_URL . "/liga/tabelle.php#rang", "Rangtabelle"]

        ];
    }

    public static function get_info(): array
    {
        return [
            [Env::BASE_URL . "/liga/neues.php", "Neuigkeiten"],
            [Env::BASE_URL . "/liga/ueber_uns.php", "Interesse?"],
            [Env::BASE_URL . "/liga/teams.php", "Teams"],
            [Env::BASE_URL . "/liga/ligakarte.php", "Ligakarte"],
            [Config::LINK_FORUM, "Forum"]
        ];
    }

    public static function get_modus(): array
    {
        return [
            [Env::BASE_URL . "/liga/dokumente.php", "Dokumente"],
            [Env::BASE_URL . "/liga/ligaleitung.php", "Ligaleitung"],
        ];
    }


    public static function get_teamcenter(): array
    {
        $class_text_color = (isset($_SESSION['logins']['team'])) ? "" : "w3-text-grey";
        $links = [
            [Env::BASE_URL . "/teamcenter/tc_start.php", "Start", $class_text_color],
            [Env::BASE_URL . "/teamcenter/tc_abstimmung.php", "Abstimmung", $class_text_color],
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
                [Env::BASE_URL . "/teamcenter/tc_logout.php", Form::icon("logout") . " Logout", $class_text_color];
        }else{
            array_unshift(
                $links,
                [Env::BASE_URL . "/teamcenter/tc_login.php", Form::icon("login") . " Login", "w3-text-black"]
            );
        }
        return $links;
    }

    public static function get_sonstiges(): array
    {
        return [
            [Env::BASE_URL . "/liga/ueber_uns.php", "Über uns"],
            [Env::BASE_URL . "/liga/archiv.php", "Archiv"],
            [Env::BASE_URL . "/ligacenter/lc_login.php", "Ligacenter"],
            [Env::BASE_URL . "/liga/kontakt.php", "Kontakt"],
            [Env::BASE_URL . "/liga/datenschutz.php", "Datenschutz"],
            [Env::BASE_URL . "/liga/impressum.php", "Impressum"],
        ];
    }

    public static function get_lc_start(): array
    {
        return [
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

    public static function get_tc_start(): array
    {
        return [
            [Env::BASE_URL . "/teamcenter/tc_abstimmung.php", "Abstimmung", "w3-teal"],
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

}