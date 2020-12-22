<?php

class Challenge {
    
    public $challenge_start = "13.11.2020";
    public $challenge_end = "20.12.2020";
    public $challenge_end_time = "20:00:00";
    public $ziel_kilometer = 16098.4;

    // Erhalte den aktuellen km-Stand

    public static function set_data($spieler, $distanz, $radgroesse, $datum): bool
    {
        $sql = "
        INSERT INTO `oeffi_challenge`(`spieler_id`, `kilometer`, `radgröße`, `datum`) VALUES ('$spieler', '$distanz', '$radgroesse', '$datum')
        ";
        db::write($sql);
        return true;
    }

    // Erhalte die Ergebnisliste aufgeschlüsselt nach Teams

    public static function update_data($id): bool
    {
        $sql = "
        UPDATE `oeffi_challenge` SET `count` = FALSE WHERE `id` = '$id';  
        ";
        db::write($sql);
        return true;
    }

    // Erhalte die Ergebnisliste aufgeschlüsselt nach Spieler

    function get_stand(): array
    {
        $sql = "
        SELECT ROUND(SUM(kilometer), 1) AS kilometer
        FROM `oeffi_challenge`
        WHERE count = TRUE
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
        ";

        $result = db::read($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten['kilometer']);
    }

    // Erhalte die einzelnen Einträge

    function get_teams(): array
    {
        $sql = "
        SELECT t.team_id, teamname, COUNT(DISTINCT(sp.spieler_id)) AS mitglieder, COUNT(ch.id) AS einträge, ROUND(SUM(kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, spieler sp, teams_liga t
        WHERE ch.spieler_id = sp.spieler_id
        AND sp.team_id = t.team_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
        AND ch.count = TRUE
        GROUP BY teamname
        ORDER BY kilometer DESC, RAND()
        ";
        $result = db::read($sql);

        $daten = [];
        $index = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $row += ["platz" => $index];
            array_push($daten, $row);
            $index++;
        }

        return db::escape($daten);
    }

    // Erhalte alle einzelnen Einträge für ein Team

    function get_spieler(): array
    {
        $sql = "
        SELECT sp.vorname, sp.nachname, t.teamname, t.team_id, COUNT(ch.id) AS einträge, ROUND(SUM(kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, spieler sp, teams_liga t
        WHERE ch.spieler_id = sp.spieler_id
        AND sp.team_id = t.team_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
        AND ch.count = TRUE
        GROUP BY sp.spieler_id
        ORDER BY kilometer DESC, RAND()
        ";
        $result = db::read($sql);

        $daten = [];
        $index = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $row += ["platz" => $index];
            array_push($daten, $row);
            $index++;
        }

        return db::escape($daten);
    }

    // Erhalte Ergebnisse der Spieler für das einzelne Team (inkl. Platzierung)

    function get_eintraege(): array
    {
        $sql = "
        SELECT ch.id, sp.team_id, ch.datum, sp.vorname, sp.nachname, ch.kilometer, ch.radgröße
        FROM `oeffi_challenge` ch, `spieler` sp
        WHERE ch.spieler_id = sp.spieler_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
        AND ch.count = TRUE
        ORDER BY ch.datum DESC, ch.timestamp DESC
        ";
        $result = db::read($sql);

        $daten = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($daten, $row);
        }

        return db::escape($daten);
    }

    // Erhalte den erfolgreichsten Spieler unter 16 Jahren

    function get_team_eintraege($team_id): array
    {
        $sql = "
        SELECT ch.id, sp.team_id, ch.datum, sp.vorname, sp.nachname, ch.kilometer, ch.radgröße
        FROM `oeffi_challenge` ch, `spieler` sp
        WHERE ch.spieler_id = sp.spieler_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
        AND sp.team_id = '" . $team_id . "'
        AND ch.count = TRUE
        ORDER BY ch.datum DESC, ch.timestamp DESC
        ";
        $result = db::read($sql);

        $daten = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($daten, $row);
        }

        return db::escape($daten);
    }

    // Erhalte den erfolgreichsten Spieler älter oder gleich 50 Jahre

    function get_team_spieler($team_id): array
    {
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
                AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
		        GROUP BY sp.spieler_id 
		        ORDER BY kilometer DESC
		        ) AS sp, (SELECT @row_number:= 0) AS t
	        ) AS neu
        WHERE team_id = '" . $team_id . "'
        ORDER BY kilometer DESC
        ";
        $result = db::read($sql);

        $daten = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($daten, $row);
        }

        return db::escape($daten);
    }

    // Erhalte den Spieler, der die meisten Kilometer auf einem Rad zurück gelegt hat, welches für Einradhockey zugelassen ist

    function get_alter_jung(): array
    {
        $sql = "
        SELECT IF (jahrgang > 2004, TRUE, FALSE) AS spieleralter, sp.vorname, te.teamname, ROUND(SUM(ch.kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, `spieler` sp, `teams_liga` te
        WHERE sp.spieler_id = ch.spieler_id
        AND sp.team_id = te.team_id
        AND ch.count = TRUE
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
        GROUP BY sp.spieler_id
        ORDER BY spieleralter DESC, kilometer DESC
        LIMIT 1
        ";
        $result = db::read($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten);
    }

    // Schreibe einen Eintrag in die Datenbank

    function get_alter_alt(): array
    {
        $sql = "
        SELECT IF (jahrgang <= 1970, TRUE, FALSE) AS spieleralter, sp.vorname, te.teamname, ROUND(SUM(ch.kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, `spieler` sp, `teams_liga` te
        WHERE sp.spieler_id = ch.spieler_id
        AND sp.team_id = te.team_id
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
        AND ch.count = TRUE
        GROUP BY sp.spieler_id
        ORDER BY spieleralter DESC, kilometer DESC
        LIMIT 1
        ";
        $result = db::read($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten);
    }

    // Flagge einen Datensatz als gelöscht

    function get_einradhockey_rad(): array
    {
        $sql = "
        SELECT ch.radgröße, sp.vorname, te.teamname, ROUND(SUM(ch.kilometer), 1) AS kilometer
        FROM `oeffi_challenge` ch, `spieler` sp, `teams_liga` te
        WHERE sp.spieler_id = ch.spieler_id
        AND sp.team_id = te.team_id
        AND ch.radgröße <= 24
        AND ch.count = TRUE
        AND datum >= '" . date("Y-m-d", strtotime($this->challenge_start)) . "'
        AND datum <= '" . date("Y-m-d", strtotime($this->challenge_end)) . "'
        AND timestamp <= '" . date("Y-m-d", strtotime($this->challenge_end)) . " " . $this->challenge_end_time . "'
        GROUP BY sp.spieler_id
        ORDER BY kilometer DESC, max(ch.radgröße)
        LIMIT 1
        "; //max da radgröße gruppiert wird und somit unterschiedliche werte beinhalten kann
        $result = db::read($sql);
        $daten = mysqli_fetch_assoc($result);

        return db::escape($daten);
    }
}