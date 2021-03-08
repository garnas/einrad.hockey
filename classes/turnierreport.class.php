<?php

/**
 * Class TurnierReport
 *
 * Anzeigen und Verwalten des Turnierreports
 */
class TurnierReport
{

    public int $turnier_id;

    /**
     * TurnierReport constructor.
     * @param $turnier_id
     */
    function __construct(int $turnier_id)
    {
        $this->turnier_id = $turnier_id;
    }


    /**
     * Zeitstrafen des Turniers aus der DB
     * @return array
     */
    function get_zeitstrafen(): array
    {
        $sql = "
                SELECT * 
                FROM spieler_zeitstrafen 
                WHERE turnier_id = $this->turnier_id
                ";
        return db::$db->query($sql)->esc()->fetch('zeitstrafe_id');
    }

    /**
     * Trägt eine Zeitstrafe in die DB ein
     *
     * @param string $spieler_name
     * @param string $dauer
     * @param string $team_a
     * @param string $team_b
     * @param string $grund
     */
    function new_zeitstrafe(string $spieler_name, string $dauer, string $team_a, string $team_b, string $grund)
    {
        $sql = "
                INSERT INTO spieler_zeitstrafen (turnier_id, spieler, dauer, team_a, team_b, grund) 
                VALUES ($this->turnier_id, ?, ?, ?, ?, ?)
                ";
        $params = [$spieler_name, $dauer, $team_a, $team_b, $grund];
        db::$db->query($sql,$params)->log();
    }

    /**
     * Zeitstrafe aus der DB entfernen
     *
     * @param int $zeitstrafe_id
     */
    function delete_zeitstrafe(int $zeitstrafe_id)
    {
        $sql = "
                DELETE FROM spieler_zeitstrafen
                WHERE zeitstrafe_id = ? 
                AND turnier_id = $this->turnier_id
                ";
        db::$db->query($sql, $zeitstrafe_id)->log();
    }

    /**
     * Get Spielerausleihen als Array
     * @return array
     */
    function get_spieler_ausleihen(): array
    {
        $sql = "
                SELECT * 
                FROM spieler_ausleihen 
                WHERE turnier_id = $this->turnier_id
                ";
        return db::$db->query($sql)->esc()->fetch('ausleihe_id');

    }

    /**
     * Neue Spielerausleihe eintragen
     *
     * @param string $spieler
     * @param string $team_auf
     * @param string $team_ab
     */
    function set_spieler_ausleihe(string $spieler, string $team_auf, string $team_ab)
    {
        $sql = "
                INSERT INTO spieler_ausleihen (turnier_id, spieler, team_auf, team_ab) 
                VALUES ($this->turnier_id, ?, ?, ?)
                ";
        db::$db->query($sql,$spieler, $team_auf, $team_ab)->log();
    }

    /**
     * Löscht eine Spielerausleihe aus der DB
     *
     * @param int $ausleihe_id
     */
    function delete_spieler_ausleihe(int $ausleihe_id)
    {
        $sql = "
                DELETE FROM spieler_ausleihen 
                WHERE ausleihe_id = ?
                AND turnier_id = $this->turnier_id
                ";
        db::$db->query($sql, $ausleihe_id)->log();
    }

    /**
     * Get Turnierbericht aus DB
     *
     * @return string
     */
    function get_turnier_bericht(): string
    {
        $sql = "
            SELECT bericht 
            FROM turniere_berichte 
            WHERE turnier_id = $this->turnier_id
            ";
        return db::$db->query($sql)->esc()->fetch_one() ?? '';
    }

    /**
     * Checkt, ob das "Kader überprüft"-Häkchen in der DB vermerkt wurde
     *
     * @return bool
     */
    function kader_check(): bool
    {
        $sql = "
                SELECT kader_ueberprueft
                FROM turniere_berichte 
                WHERE turnier_id = $this->turnier_id
                ";
        return db::$db->query($sql)->fetch_one() === "Ja";
    }

    /**
     * Turnierbericht in die Datenbank schreiben
     *
     * @param string $bericht
     * @param bool $kader_check
     */
    function set_turnier_bericht(string $bericht, bool $kader_check)
    {
        $kader_check = ($kader_check) ? 'Ja' : 'Nein';
        // Existiert bereits ein Turnierbericht?
        $sql = "
                SELECT * FROM turniere_berichte 
                WHERE turnier_id = $this->turnier_id
                ";
        if (db::$db->query($sql)->num_rows() === 0) {
            $sql = "
                    INSERT INTO turniere_berichte (turnier_id, bericht, kader_ueberprueft)
                    VALUES ($this->turnier_id, ?, ?)
                    ";
        } else {
            $sql = "
                    UPDATE turniere_berichte 
                    SET bericht = ?, kader_ueberprueft = ? 
                    WHERE turnier_id = $this->turnier_id
                    ";
        }
        $params = [$bericht, $kader_check];
        db::$db->query($sql, $params)->log();
    }
}