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
    /**
     * Frühstmöglicher Zeitpunkt der Stimmabgabe
     */
    const BEGINN = "18.01.2021 00:06";
    /**
     * Letztmöglicher Zeitpunkt der Stimmabgabe
     */
    const ENDE = "19.01.2021 00:00";
    /**
     * Verschlüsselungsverfahren für die TeamIDs
     */
    const CIPHER = 'AES-256-CBC';
    /**
     * Verhindert, dass bei einer neuen Abstimmung der gleiche Crypt bei gleichen Passwort und TeamID entsteht.
     * Muss für jede Liga-Abstimmung erneuert werden.
     */
    const IV = 'RUEvrH/ovYG9t8bA';
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
    function __construct($team_id)
    {
        $this->team_id = $team_id;
        // Details aus abstimmung_teams bekommen
        $sql = "
                SELECT *
                FROM abstimmung_teams
                WHERE team_id = $this->team_id
                ";
        $result = db::readdb($sql);
        $this->team = mysqli_fetch_assoc($result) ?? [];

        // Beim ersten Mal Abstimmen den für die Verschlüsselung benutzen Passwort-Hash speichern.
        if (empty($this->team)) {
            $this->passwort_hash = (new Team($team_id))->get_passwort();
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
    function crypt_to_teamid($passwort, $crypt): string
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
    function teamid_to_crypt(string $passwort): string
    {
        // Verwendete password-based key derivation, das User-Passwort kein geeigner Key ist zum verschlüsseln.
        $key = hash_pbkdf2("sha256", $passwort, $this->passwort_hash, 8000);
        return openssl_encrypt($this->team_id, self::CIPHER, $key, 0, self::IV);
    }

    /**
     * Gibt die einem $crypt zugeordneten Stimme aus.
     *
     * @param $crypt
     * @return string
     */
    function get_stimme($crypt): string
    {
        $sql = "
            SELECT stimme 
            FROM abstimmung_ergebnisse
            WHERE crypt = '$crypt'
            ";
        $result = db::readdb($sql);
        return mysqli_fetch_assoc($result)['stimme'] ?? 'none';
    }

    /**
     * Schreibt eine Stimme in die Datenbank.
     * Wenn die Stimme bereits gezählt wurde, dann wird diese aktualisiert.
     * Erstellt Log und Affirm-Meldung.
     *
     * @param $stimme
     * @param $crypt // Verschlüsselte TeamID
     */
    function set_stimme($stimme, $crypt)
    {
        if (empty($this->team) xor $this->get_stimme($crypt) === 'none'){
            Form::error("Fehler, bitte melde dich bei " . Form::mailto(Config::TECHNIKMAIL));
            return;
        }
        if (empty($this->team)) { // Team stimmt zum ersten mal ab.
            $sql = "
                INSERT INTO abstimmung_teams (team_id, passwort)
                VALUES ($this->team_id, '$this->passwort_hash')";
            db::writedb($sql);
            $sql = "
                INSERT INTO abstimmung_ergebnisse (stimme, crypt) 
                VALUES ('$stimme', '$crypt')
                ";
            db::writedb($sql, true);
            Form::log("abstimmung.log", "$this->team_id hat seine Stimme abgegeben");
            Form::affirm("Dein Team hat erfolgreich abgestimmt. Vielen Dank!");
        } else { // Team korrigiert seine Stimme.
            $sql = "
                UPDATE abstimmung_ergebnisse
                SET stimme = '$stimme'
                WHERE crypt = '$crypt'
                ";
            db::writedb($sql, true);
            $sql = "
                UPDATE abstimmung_teams
                SET aenderungen = aenderungen + 1
                WHERE team_id = $this->team_id
                ";
            db::writedb($sql);
            Form::log("abstimmung.log", "$this->team_id hat seine Stimme geändert");
            Form::affirm("Dein Team hat erfolgreich neu abgestimmt. Vielen Dank!");
        }
    }

    /**
     * Gibt das Gesamt-Ergebnis der Abstimmung aus.
     *
     * @param int $min Minimale anzahl an Stimmen bis zur Veröffentlichung
     * @return array Array mit den Ergebnissen der Abstimmung
     */
    static function get_ergebnisse($min = 0): array
    {
        $sql = "
            SELECT stimme, COUNT(stimme) AS stimmen 
            FROM abstimmung_ergebnisse
            GROUP BY stimme
            ";
        $result = db::readdb($sql);
        $ergebnisse['gesamt'] = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $ergebnisse[$row['stimme']] = $row['stimmen'];
            $ergebnisse['gesamt'] += $row['stimmen'];
        }
        // Mindest anzahl an Stimmen muss erreicht werden
        if ($ergebnisse['gesamt'] < $min){
            $array['gesamt'] = $ergebnisse['gesamt'];
            return $array;
        } else {
            return $ergebnisse;
        }
    }
}