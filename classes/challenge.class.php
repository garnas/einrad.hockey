<?php

class Challenge {
    
    public $challenge_start = "13.11.2020";
    public $challenge_end = "20.12.2020";

    function get_teams(){
        $sql = "
        SELECT t.team_id, teamname, COUNT(DISTINCT(sp.spieler_id)) AS mitglieder, COUNT(ch.id) AS einträge, ROUND(SUM(kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, spieler sp, teams_liga t
        WHERE ch.spieler_id = sp.spieler_id
        AND sp.team_id = t.team_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND ch.count = TRUE
        GROUP BY teamname
        ORDER BY kilometer DESC
        ";
        $result = db::readdb($sql);

        $daten = [];
        $index = 1;
        while($row = mysqli_fetch_assoc($result)){
            $row += ["platz" => $index];
            array_push($daten, $row);
            $index++;
        }

        return db::escape($daten);
    }

    function get_spieler(){
        $sql = "
        SELECT sp.vorname, sp.nachname, t.teamname, t.team_id, COUNT(ch.id) AS einträge, ROUND(SUM(kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, spieler sp, teams_liga t
        WHERE ch.spieler_id = sp.spieler_id
        AND sp.team_id = t.team_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND ch.count = TRUE
        GROUP BY sp.spieler_id
        ORDER BY kilometer DESC
        ";
        $result = db::readdb($sql);

        $daten = [];
        $index = 1;
        while($row = mysqli_fetch_assoc($result)){
            $row += ["platz" => $index];
            array_push($daten, $row);
            $index++;
        }
        
        return db::escape($daten);
    }

    function get_eintraege() {
        $sql = "
        SELECT ch.id, sp.team_id, ch.datum, sp.vorname, sp.nachname, ch.kilometer, ch.radgröße
        FROM `oeffi_challenge` ch, `spieler` sp
        WHERE ch.spieler_id = sp.spieler_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND ch.count = TRUE
        ORDER BY ch.datum DESC, ch.timestamp DESC
        ";
        $result = db::readdb($sql);

        $daten = [];
        while($row = mysqli_fetch_assoc($result)){
            array_push($daten, $row);
        }

        return db::escape($daten);
    }
    
    function get_team_eintraege($team_id) {
        $sql = "
        SELECT ch.id, sp.team_id, ch.datum, sp.vorname, sp.nachname, ch.kilometer, ch.radgröße
        FROM `oeffi_challenge` ch, `spieler` sp
        WHERE ch.spieler_id = sp.spieler_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND sp.team_id = '" . $team_id . "'
        AND ch.count = TRUE
        ORDER BY ch.datum DESC, ch.timestamp DESC
        ";
        $result = db::readdb($sql);

        $daten = [];
        while($row = mysqli_fetch_assoc($result)){
            array_push($daten, $row);
        }

        return db::escape($daten);
    }

    function get_team_spieler($team_id) {
        $sql = "
        SELECT platz, vorname, kilometer
        FROM (
            SELECT ROW_NUMBER() OVER (ORDER BY kilometer DESC) AS platz, sp.team_id, sp.vorname, ROUND(SUM(kilometer), 1) AS kilometer
            FROM `oeffi_challenge` ch, spieler sp, teams_liga t
            WHERE ch.spieler_id = sp.spieler_id
            AND sp.team_id = t.team_id
            AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
            AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
            AND ch.count = TRUE
            GROUP BY sp.spieler_id
        ) AS spieler
        WHERE team_id = '" . $team_id . "'
        ORDER BY kilometer DESC
        ";
        $result = db::readdb($sql);

        $daten = [];
        while($row = mysqli_fetch_assoc($result)){
            array_push($daten, $row);
        }

        return db::escape($daten);
    }

    public static function set_data($spieler, $distanz, $radgroesse, $datum){
        $sql = "
        INSERT INTO `oeffi_challenge`(`spieler_id`, `kilometer`, `radgröße`, `datum`) VALUES ('$spieler', '$distanz', '$radgroesse', '$datum')
        ";
        db::writedb($sql);
        return true;
    }

    public static function update_data($id) {
        $sql = "
        UPDATE `oeffi_challenge` SET `count` = FALSE WHERE `id` = '$id';  
        ";
        db::writedb($sql);
        return true;
    }
}

?>