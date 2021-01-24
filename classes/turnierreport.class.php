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
        $turnier_id = $this->turnier_id;
        $sql = "
                SELECT * 
                FROM spieler_zeitstrafen 
                WHERE turnier_id = $turnier_id
                ";
        $result = db::read($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            $zeitstrafen[$x['zeitstrafe_id']] = $x;
        }
        return db::escape($zeitstrafen ?? []);
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
        $turnier_id = $this->turnier_id;
        $sql = "
                INSERT INTO spieler_zeitstrafen (turnier_id, spieler, dauer, team_a, team_b, grund) 
                VALUES ('$turnier_id', '$spieler_name', '$dauer', '$team_a', '$team_b', '$grund')
                ";
        db::write($sql);
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
                WHERE zeitstrafe_id = '$zeitstrafe_id' 
                AND turnier_id = $this->turnier_id
                ";
        db::write($sql);
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
        $result = db::read($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            $return[$x['ausleihe_id']] = $x;
        }
        return db::escape($return ?? []);
    }

    /**
     * Neue Spielerausleihe eintragen
     *
     * @param string $spieler
     * @param string $team_auf
     * @param string $team_ab
     */
    function new_spieler_ausleihe(string $spieler, string $team_auf, string $team_ab)
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                INSERT INTO spieler_ausleihen (turnier_id, spieler, team_auf, team_ab) 
                VALUES ('$turnier_id', '$spieler', '$team_auf', '$team_ab')
                ";
        db::write($sql);
    }

    /**
     * Löscht eine Spielerausleihe aus der DB
     *
     * @param int $ausleihe_id
     */
    function delete_spieler_ausleihe(int $ausleihe_id)
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                DELETE FROM spieler_ausleihen 
                WHERE ausleihe_id = '$ausleihe_id' 
                AND turnier_id = '$turnier_id'
                ";
        db::write($sql);
    }

    /**
     * Get Turnierbericht aus DB
     *
     * @return string
     */
    function get_turnier_bericht(): string
    {
        $turnier_id = $this->turnier_id;
        $sql = "
            SELECT bericht 
            FROM turniere_berichte 
            WHERE turnier_id = '$turnier_id'
            ";
        $return = db::read($sql);
        $return = mysqli_fetch_assoc($return);
        return db::escape($return['bericht'] ?? '');
    }

    /**
     * Checkt, ob das "Kader überprüft"-Häkchen in der DB vermerkt wurde
     *
     * @return bool
     */
    function kader_check(): bool
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                SELECT kader_ueberprueft
                FROM turniere_berichte 
                WHERE turnier_id = '$turnier_id'
                ";
        $return = db::read($sql);
        $return = mysqli_fetch_assoc($return);
        if (!empty($return) && $return['kader_ueberprueft'] == 'Ja') {
            return true;
        }
        return false;
    }

    /**
     * Turnierbericht in die Datenbank schreiben
     *
     * @param string $bericht
     * @param string $kader_check
     */
    function set_turnier_bericht(string $bericht, string $kader_check = 'Nein')
    {
        $turnier_id = $this->turnier_id;
        // Existiert bereits ein Turnierbericht?
        $check = db::read("
                                SELECT * FROM turniere_berichte 
                                WHERE turnier_id = '$turnier_id'
                                ");
        if (mysqli_num_rows($check) == 0) {
            $sql = "
                    INSERT INTO turniere_berichte (turnier_id, bericht, kader_ueberprueft)
                    VALUES ('$turnier_id', '$bericht', '$kader_check')
                    ";
        } else {
            $sql = "
                    UPDATE turniere_berichte 
                    SET bericht='$bericht', kader_ueberprueft = '$kader_check' 
                    WHERE turnier_id = '$turnier_id'
                    ";
        }
        db::write($sql);
    }
}