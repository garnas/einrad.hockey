<?php

class Challenge {
    
    public $challenge_start = "2020-11-01";
    public $challenge_end = "2020-11-30";

    function get_teams(){
        $sql = "
        SELECT t.team_id, teamname, COUNT(sp.spieler_id) AS mitglieder, COUNT(ch.id) AS einträge, ROUND(SUM(kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, spieler sp, teams_liga t
        WHERE ch.spieler_id = sp.spieler_id
        AND sp.team_id = t.team_id
        AND datum >= '" . $this->challenge_start . "'
        AND datum <= '" . $this->challenge_end . "'
        GROUP BY teamname
        ORDER BY kilometer DESC
        ";
        $result = db::readdb($sql);

        $daten=[];
        while($row=mysqli_fetch_assoc($result)){
            array_push($daten, $row);
        }

        return db::escape($daten);
    }

    function get_spieler(){
        $sql = "
        SELECT sp.vorname, t.teamname, COUNT(ch.id) AS einträge, ROUND(SUM(kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, spieler sp, teams_liga t
        WHERE ch.spieler_id = sp.spieler_id
        AND sp.team_id = t.team_id
        AND datum >= '" . $this->challenge_start . "'
        AND datum <= '" . $this->challenge_end . "'
        GROUP BY sp.spieler_id
        ORDER BY kilometer DESC
        ";
        $result = db::readdb($sql);

        $daten=[];
        while($row=mysqli_fetch_assoc($result)){
            array_push($daten, $row);
        }

        return db::escape($daten);
    }

    function set_data($spieler, $distanz, $datum){
        $sql = "
        INSERT INTO `oeffi_challenge`(`spieler_id`, `kilometer`, `datum`) VALUES ('$spieler', '$distanz', '$datum')
        ";
        $result = db::writedb($sql);
    }

}

?>