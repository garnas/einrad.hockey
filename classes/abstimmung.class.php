<?php

/**
 * Class Abstimmung
 *
 * Handling der Abstimmung zum Saisonwechsel
 *
 * Es gibt zwei Tabellen: abstimmung_teams ist eine Tabelle mit allen Teams die bisher Abgestimmt haben und wichtigen
 * Metadaten. abstimmung_ergebnisse ist eine Tabelle mit verschlüsselter TeamID und Stimme des Teams. Durch Verschlüsselung
 * der TeamID mit dem Passwort des Teams, ist die Abstimmung anonym und ein Team kann seine Stimme im Nachhinein noch ändern.
 */
class Abstimmung
{
    public const OPTIONS = [
        "option_1" => "Option 1: Ein Samstag mit 8 Teams, Gruppenphase mit anschließenden Finalspielen",
        "option_2" => "Option 2: Ein Wochenende 1,5 Tage mit 10 Teams, Samstag Gruppenphase, Sonntag Finalspiele",
        "option_3" => "Option 3: Zwei Samstage mit 10 Teams, Quali- und Finalturnier als Standard 6-er Modus (alt)",
        "enthaltung" => "Enthaltung"
    ];

    public const OPTIONS_COLOR = [
        "option_1" => "w3-indigo",
        "option_2" => "w3-red",
        "option_3" => "w3-green",
        "enthaltung" => "w3-grey"
    ];

    public const ANZAHL_TEAMS = 24;

    /**
     * Frühstmöglicher Zeitpunkt der Stimmabgabe
     */
    public const BEGINN = "28.12.2021 00:35";
    /**
     * Letztmöglicher Zeitpunkt der Stimmabgabe
     */
    public const ENDE = "28.12.2025 00:35";
    /**
     * Verschlüsselungsverfahren für die TeamIDs
     */
    private const CIPHER = 'AES-256-CBC';
    /**
     * Verhindert, dass bei einer neuen Abstimmung der gleiche Crypt bei gleichen Passwort und TeamID entsteht.
     * Muss für jede Liga-Abstimmung erneuert werden.
     */
    private const IV = 'ZUEvrHAovY29t/bA';
    /**
     * @var array|string[] Daten aus der DB-Tabelle abstimmung_teams
     */
    public array $team;
    /**
     * @var int Eindeutige ligaweite TeamID
     */
    public int $team_id;
    /**
     * @var string Passwort-Hash, welcher für das Team bei erstmaliger Abstimmung hinterlegt war.
     */
    public string $passwort_hash;

    public static function darf_abstimmen(int $team_id): bool
    {
        return Tabelle::get_team_rang($team_id, 13) <= 24;
    }

    /**
     * Abstimmung constructor.
     *
     * Erstellt eine Klasse für die Abstimmung eines Teams und initiert wichtige Variablen.
     * Enthält statische Methoden für die Einsicht des Abstimmungsergebnisses.
     *
     * @param $team_id
     */
    public function __construct($team_id)
    {
        $this->team_id = $team_id;
        if (!Team::is_ligateam($team_id)) {
            trigger_error("Ungültige Team-ID", E_USER_ERROR);
        }
        // Details aus abstimmung_teams bekommen
        $sql = "
                SELECT *
                FROM abstimmung_teams
                WHERE team_id = ?
                ";
        $this->team = db::$db->query($sql, $this->team_id)->esc()->fetch_row();

        // Beim ersten Mal Abstimmen den für die Verschlüsselung benutzen Passwort-Hash speichern.
        if (empty($this->team)) {
            $this->passwort_hash = (new Team($team_id))->details['passwort'];
        } else {
            $this->passwort_hash = $this->team['passwort'];
        }

    }

    /**
     * Optional: Entschlüsselung einer TeamID
     *
     * @param $passwort
     * @param $crypt
     * @return string
     */
    public function crypt_to_teamid($passwort, $crypt): string
    {
        // Verwendete password-based key derivation, das User-Passwort kein geeigner Key ist zum verschlüsseln.
        $key = hash_pbkdf2("sha256", $passwort, $this->passwort_hash, 8000);
        return openssl_decrypt($crypt, self::CIPHER, $key, 0, self::IV);
    }

    /**
     * Verschlüsselung der TeamID
     *
     * @param string $passwort
     * @return string
     */
    public function teamid_to_crypt(string $passwort): string
    {
        // Verwendete password-based key derivation, das User-Passwort kein geeigner Key ist zum verschlüsseln.
        $key = hash_pbkdf2("sha256", $passwort, $this->passwort_hash, 8000);
        return openssl_encrypt($this->team_id, self::CIPHER, $key, 0, self::IV);
    }

    /**
     * Gibt die einem $crypt zugeordneten Stimme aus.
     *
     * @param string $crypt
     * @return string
     */
    public function get_stimme(string $crypt): string
    {
        $sql = "
            SELECT stimme 
            FROM abstimmung_ergebnisse
            WHERE crypt = ?
            ";
        return db::$db->query($sql, $crypt)->esc()->fetch_one() ?? '';
    }

    /**
     * Schreibt eine Stimme in die Datenbank.
     * Wenn die Stimme bereits gezählt wurde, dann wird diese aktualisiert.
     * Erstellt Log und Affirm-Meldung.
     *
     * @param $stimme
     * @param $crypt // Verschlüsselte TeamID
     */
    public function set_stimme($stimme, $crypt): void
    {
        // Validierung
        if (
            (empty($this->team) xor empty($this->get_stimme($crypt))
            || !(self::darf_abstimmen($this->team_id))
        )){
            Html::error("Fehler, bitte melde dich bei " . Html::mailto(Env::TECHNIKMAIL), esc:false);
            Helper::log("abstimmung.log", "Fehler: $this->team_id | $crypt");
            return;
        }

        if (empty($this->team)) { // Team stimmt zum ersten Mal ab.
            $sql = "
                INSERT INTO abstimmung_teams (team_id, passwort)
                VALUES ($this->team_id, ?)";
            db::$db->query($sql, $this->passwort_hash)->log(true);
            $sql = "
                INSERT INTO abstimmung_ergebnisse (stimme, crypt) 
                VALUES ('$stimme', '$crypt')
                ";
            db::$db->query($sql)->log(true);
            Helper::log("abstimmung.log", "$this->team_id hat seine Stimme abgegeben");
            Html::info("Dein Team hat erfolgreich abgestimmt. Vielen Dank!");
        } else { // Team korrigiert seine Stimme
            $sql = "
                UPDATE abstimmung_ergebnisse
                SET stimme = ?
                WHERE crypt = ?
                ";
            db::$db->query($sql, $stimme, $crypt)->log(true);
            $sql = "
                UPDATE abstimmung_teams
                SET aenderungen = aenderungen + 1
                WHERE team_id = $this->team_id
                ";
            db::$db->query($sql)->log(true);
            Helper::log("abstimmung.log", "$this->team_id hat seine Stimme geändert");
            Html::info("Dein Team hat erfolgreich neu abgestimmt. Vielen Dank!");
        }
    }

    /**
     * Gibt das Gesamt-Ergebnis der Abstimmung aus.
     *
     *
     * @param int $min Minimale anzahl an Stimmen bis zur Veröffentlichung
     * @return array Array mit den Ergebnissen der Abstimmung
     */
    public static function get_ergebnisse(int $min = 0): array
    {
        $sql = "
            SELECT stimme, COUNT(stimme) AS stimmen 
            FROM abstimmung_ergebnisse
            GROUP BY stimme
            ";
        $result = db::$db->query($sql)->esc()->list('stimmen', 'stimme');
        $ergebnisse['gesamt'] = 0;
        foreach ($result as $stimmen) {
            $ergebnisse['gesamt'] += $stimmen;
        }
        $ergebnisse['%'] = round($ergebnisse['gesamt'] / self::ANZAHL_TEAMS * 100);
        foreach (self::OPTIONS as $option => $text) {
            if (
                isset($result[$option])
                && $ergebnisse['gesamt'] >= $min // Mindestanzahl an Stimmen muss erreicht werden, um Ergebnisse anzuzeigen
            ) {
                $ergebnisse[$option]["%"] = round(($result[$option] / $ergebnisse['gesamt']) * 100);
                $ergebnisse[$option]["anzahl"] = $result[$option];
            } else {
                $ergebnisse[$option]["%"] = 0;
                $ergebnisse[$option]["anzahl"] = 0;
            }
        }

        return $ergebnisse;
    }


}