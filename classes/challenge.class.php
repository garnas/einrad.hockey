<?php

class Challenge {
    
    public $challenge_start = "13.11.2020";
    public $challenge_end = "20.12.2020";
    public $ziel_kilometer = 16098.4;

    // Erhalte den aktuellen km-Stand
    function get_stand(){
        $sql = "
        SELECT ROUND(SUM(kilometer), 1) AS kilometer
        FROM `oeffi_challenge`
        WHERE count = TRUE
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        ";

        $result = db::readdb($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten['kilometer']);
    }
    
    // Erhalte die Ergebnisliste aufgeschlüsselt nach Teams
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
        ORDER BY kilometer DESC, RAND()
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

    // Erhalte die Ergebnisliste aufgeschlüsselt nach Spieler
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
        ORDER BY kilometer DESC, RAND()
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

    // Erhalte die einzelnen Einträge
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
    
    // Erhalte alle einzelnen Einträge für ein Team
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

    // Erhalte Ergebnisse der Spieler für das einzelne Team (inkl. Platzierung)
    function get_team_spieler($team_id) {
        $sql = "
        SELECT platz, vorname, kilometer
        FROM(
	        SELECT (@row_number:=@row_number + 1) AS platz, sp.*
	        FROM ( 
		        SELECT sp.team_id, sp.vorname, ROUND(SUM(kilometer), 1) AS kilometer
		        FROM `oeffi_challenge` ch, spieler sp, teams_liga t
		        WHERE ch.spieler_id = sp.spieler_id
		        AND sp.team_id = t.team_id
                AND ch.count = TRUE
                AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
                AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
		        GROUP BY sp.spieler_id 
		        ORDER BY kilometer DESC
		        ) AS sp, (SELECT @row_number:= 0) AS t
	        ) AS neu
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

    // Erhalte den erfolgreichsten Spieler unter 16 Jahren
    function get_alter_jung() {
        $sql = "
        SELECT IF (jahrgang > 2004, TRUE, FALSE) AS spieleralter, sp.vorname, te.teamname, ROUND(SUM(ch.kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, `spieler` sp, `teams_liga` te
        WHERE sp.spieler_id = ch.spieler_id
        AND sp.team_id = te.team_id
        AND ch.count = TRUE
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        GROUP BY sp.spieler_id
        ORDER BY spieleralter DESC, kilometer DESC
        LIMIT 1
        ";
        $result = db::readdb($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten);
    }

    // Erhalte den erfolgreichsten Spieler älter oder gleich 50 Jahre
    function get_alter_alt() {
        $sql = "
        SELECT IF (jahrgang <= 1970, TRUE, FALSE) AS spieleralter, sp.vorname, te.teamname, ROUND(SUM(ch.kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, `spieler` sp, `teams_liga` te
        WHERE sp.spieler_id = ch.spieler_id
        AND sp.team_id = te.team_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND ch.count = TRUE
        GROUP BY sp.spieler_id
        ORDER BY spieleralter DESC, kilometer DESC
        LIMIT 1
        ";
        $result = db::readdb($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten);
    }

    // Erhalte den Spieler, der die meisten Kilometer auf einem Rad zurück gelegt hat, welches für Einradhockey zugelassen ist
    function get_einradhockey_rad() {
        $sql = "
        SELECT ch.radgröße, sp.vorname, te.teamname, ROUND(SUM(ch.kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, `spieler` sp, `teams_liga` te
        WHERE sp.spieler_id = ch.spieler_id
        AND sp.team_id = te.team_id
        AND ch.radgröße <= 24
        AND ch.count = TRUE
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        GROUP BY sp.spieler_id
        ORDER BY kilometer DESC, radgröße ASC
        LIMIT 1
        ";
        $result = db::readdb($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten);
    }
    
    // Erhalte das Gesamtergebnis für einen einzelnen Spieler
    function get_spieler_result($spieler_id){
        $sql = "
        SELECT platz, vorname, nachname, geschlecht, teamname, kilometer
        FROM (
            SELECT (@row_number:=@row_number + 1) AS platz, sp.*
	        FROM ( 
		        SELECT sp.spieler_id, sp.vorname, sp.nachname, sp.geschlecht, te.teamname, ROUND(SUM(kilometer), 1) AS kilometer
        		FROM `oeffi_challenge` ch, spieler sp, teams_liga te
                WHERE ch.spieler_id = sp.spieler_id
                AND sp.team_id = te.team_id
		        AND ch.count = TRUE
		        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
		        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
    	        GROUP BY sp.spieler_id
                ORDER BY kilometer DESC
	        ) AS sp, (SELECT @row_number:= 0) AS t
        ) AS neu
        WHERE spieler_id = '" . $spieler_id . "'
        ";
        $result = db::readdb($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten);
    }

    //Erhalte das Gesamtergebnis für ein einziges Team
    function get_team_result($team_id){
        $sql = "
        SELECT platz, teamname, kilometer
        FROM(
            SELECT (@row_number:=@row_number + 1) AS platz, te.*
            FROM(
	            SELECT te.team_id, te.teamname, ROUND(SUM(kilometer), 1) AS kilometer
	            FROM `oeffi_challenge` ch, spieler sp, teams_liga te
	            WHERE ch.spieler_id = sp.spieler_id
	            AND sp.team_id = te.team_id
	            AND ch.count = TRUE
	            AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
	            AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
	            GROUP BY te.team_id
	            ORDER BY kilometer DESC
            ) AS te, (SELECT @row_number:= 0) AS t
        ) AS neu
        WHERE team_id = '" . $team_id . "'
        ";
        $result = db::readdb($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten);
    }

    // Schreibe einen Eintrag in die Datenbank
    public static function set_data($spieler, $distanz, $radgroesse, $datum){
        $sql = "
        INSERT INTO `oeffi_challenge`(`spieler_id`, `kilometer`, `radgröße`, `datum`) VALUES ('$spieler', '$distanz', '$radgroesse', '$datum')
        ";
        db::writedb($sql);
        return true;
    }

    // Flagge einen Datensatz als gelöscht
    public static function update_data($id) {
        $sql = "
        UPDATE `oeffi_challenge` SET `count` = FALSE WHERE `id` = '$id';  
        ";
        db::writedb($sql);
        return true;
    }
}

?>