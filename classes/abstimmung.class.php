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
        "Ja" => "Ja",
        "Nein" => "Nein",
        "Keine_Angabe" => "Keine Angabe",
    ];

    public const OPTIONS_WICHTIGKEIT = [
        "Trainingsmaterialien" => "Trainingsmaterialien für den A- und B-Kader",
        "Trikots" => "Trikots und/oder Trainingsjacken für den A- und B-Kader",
        "Hallenkosten" => "Hallenkosten bei Einradhockey-Veranstaltungen (Trainingslager, 24 Stunden Turniere etc.)",
        "Verpflegung" => "Verpflegung für Teilnehmende bei Einradhockey-Veranstaltungen",
        "Uebernachtungskosten" => "Übernachtungskosten bei Einradhockey-Veranstaltungen",
        "Aufwandsentschaedigung" => "Aufwandsentschädigung von Übungsleitern bei Einradhockey-Veranstaltungen (z.B. Trainingslager)"
    ];

    public static function selected(string $name, string $value, ?string $value_chosen): string
    {
        if ($value_chosen != null) {
            return ($value === $value_chosen) ? "selected checked" : "";
        }
        return (($_POST[$name] ?? "") == $value) ? "selected checked" : "";
    }

    /**
     * Frühstmöglicher Zeitpunkt der Stimmabgabe
     */
    public const BEGINN = "17.03.2025 00:00";
    /**
     * Letztmöglicher Zeitpunkt der Stimmabgabe
     */
    public const ENDE = "13.04.2025 23:59";
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
     * @return array
     */
    public function get_stimme(string $crypt): array
    {
        $sql = "
            SELECT stimme 
            FROM abstimmung_ergebnisse
            WHERE crypt = ?
            ";
        $stimme =  db::$db->query($sql, $crypt)->fetch_one() ?? '';
        return json_decode($stimme, associative: true) ?? [];
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
                VALUES (?, ?)
                ";
            db::$db->query($sql, $stimme, $crypt)->log(true);
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
        $sql = 'select * from abstimmung_ergebnisse';
        $stimmen = db::$db->query($sql)->list('stimme');

        $ergebnisse = array();
        foreach ($stimmen as $key => $stimme) {
            $decode = json_decode($stimme, associative: true);
            foreach ($decode as $key => $value) {
                $ergebnisse[$key][$value] = ($ergebnisse[$key][$value] ?? 0) + 1;
            }
        }

        return $ergebnisse;
    }

    /**
     * Gibt die Anzahl der abgegebenen Stimmen zurück.
     *
     * @return int
     */
    public static function get_anzahl_stimmen(): int
    {
        $sql = 'select count(*) from abstimmung_ergebnisse';
        return db::$db->query($sql)->fetch_one();
    }

}