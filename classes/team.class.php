<?php

class Team {
    public $team_id;

    function __construct($team_id)
    {
        $this->team_id = $team_id;
    }

    //Erstellt ein neues Team in der Datenbank
    //Hinweis: Nichtligateam werden bei der Anmeldung in der Klasse Turniere erstellt!
    public static function create_new_team($teamname,$passwort,$email) 
    {
        //eintrag in teams_liga
        $passwort = password_hash($passwort, PASSWORD_DEFAULT);

        //Teamids werden über die Sql-Funktion auto increment vergeben
        $team_id = db::get_auto_increment("teams_liga");

        $sql="INSERT INTO teams_liga (teamname, passwort, freilose) VALUES ('$teamname','$passwort',2)";
        db::writedb($sql);

        //eintrag in teams_details
        $sql="INSERT INTO teams_details (team_id) VALUES ('$team_id')";
        db::writedb($sql);

        //eintrag in teams_kontakt
        $sql="INSERT INTO teams_kontakt (team_id, email, public, get_info_mail) VALUES ('$team_id','$email','Ja','Nein')";
        db::writedb($sql);
    }

    public static function deactivate_team($team_id)
    {
        $sql = "UPDATE teams_liga SET aktiv='Nein' WHERE team_id='$team_id'";
        db::writedb($sql);
    }

    public static function get_deactive_teams()
    {
        $sql = "SELECT * FROM teams_liga WHERE aktiv='Nein' AND ligateam='Ja' ORDER BY teamname";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x);
        }
        return db::escape($return); //Array
    }

    public static function activate_team($team_id)
    {
        $sql = "UPDATE teams_liga SET aktiv='Ja' WHERE team_id='$team_id'";
        db::writedb($sql);
    }

    //Wandelt den Teamnamen in die Teamid um. Gibt leer zurück, wenn es die TeamID nicht gibt.
    public static function teamname_to_teamid ($teamname)
    {
        $teamname = htmlspecialchars_decode($teamname);
        $sql = "SELECT team_id FROM teams_liga WHERE teamname = '$teamname'";
        $result = mysqli_fetch_assoc(db::readdb($sql));
        return db::escape($result['team_id'] ?? '');
    }

    //Wandelt die TeamID in den Teamnamen um. Gibt leer zurück, wenn es den Teamnamen nicht gibt.
    public static function teamid_to_teamname ($team_id)
    {
        $sql = "SELECT teamname FROM teams_liga WHERE team_id = '$team_id'";
        $result = mysqli_fetch_assoc(db::readdb($sql));
        return db::escape($result['teamname'] ?? '');
    }

    //Prüft ob die TeamID zu einem aktiven Ligateam gehört
    public static function is_ligateam ($team_id)
    {
        $sql = "SELECT team_id FROM teams_liga WHERE team_id = '$team_id' AND ligateam='Ja' AND aktiv='Ja'";
        $result = mysqli_fetch_assoc(db::readdb($sql));
        if (!empty($result['team_id'])){
            return true;
        }
        return false;
    }

    //Gibt ein Array mit allen Teamnamen zurück
    public static function list_of_all_teams()
    {
        $sql = "SELECT teamname FROM teams_liga WHERE ligateam = 'Ja' AND aktiv = 'Ja' ORDER BY teamname ASC";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x['teamname']);
        }
        return db::escape($return); //Array 
    }

    //Array aller IDs von Ligateams
    public static function get_all_teamids()
    {
        $sql = "SELECT team_id FROM teams_liga WHERE ligateam = 'Ja' AND aktiv = 'Ja' ORDER BY RAND()";
        $result = db::readdb($sql);
        $return = array();
        while ($eintrag = mysqli_fetch_assoc($result)){
            array_push($return,$eintrag['team_id']);
        }
        return db::escape($return);
    }

    //Gibt ein Array mit allen Teamdaten aller Teams zurück
    public static function get_all_teamdata()
    {
        $sql = 
        "SELECT * 
        FROM teams_liga
        INNER JOIN teams_details
        ON teams_liga.team_id = teams_details.team_id
        WHERE teams_liga.ligateam = 'Ja' AND teams_liga.aktiv = 'Ja'
        ORDER BY teams_liga.teamname ASC";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            $return[$x['team_id']] = $x;
        }
        return db::escape($return); //Array 
    }

    //Ein Array aller Daten, welche man brauchen könnte
    function daten()
    {
        $team_id = $this->team_id;
        $sql = 
        "SELECT *  
        FROM teams_liga 
        INNER JOIN teams_details
        ON teams_details.team_id=teams_liga.team_id
        WHERE teams_liga.team_id='$team_id'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result); //Array
    }

    function set_teamname($name)
    {
        $team_id = $this->team_id;
        $sql = "UPDATE teams_liga SET teamname = '$name' WHERE team_id='$team_id'";
        db::writedb($sql);
    }
    function get_passwort()
    {
        $team_id = $this->team_id;
        $sql = "SELECT passwort  FROM teams_liga WHERE team_id='$team_id'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        return $result['passwort'];
    }

    function set_passwort($passwort, $pw_geaendert = 'Ja')
    {
        $team_id = $this->team_id;
        $passwort = password_hash($passwort, PASSWORD_DEFAULT);
        $sql = "UPDATE teams_liga SET passwort = '$passwort', passwort_geaendert = '$pw_geaendert'  WHERE team_id='$team_id'";
        db::writedb($sql);
    }

    function get_freilose()
    {
        $team_id = $this->team_id;
        $sql = "SELECT freilose  FROM teams_liga WHERE team_id='$team_id'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result['freilose']);
    }

    function set_freilose($anzahl)
    {
        $team_id = $this->team_id;
        $sql = "UPDATE teams_liga SET freilose='$anzahl' WHERE team_id='$team_id'";
        db::writedb($sql);
    }

    public static function add_freilos($team_id)
    {
        $sql = "UPDATE teams_liga SET freilose=freilose+1 WHERE team_id='$team_id'";
        db::writedb($sql);
    }

    function set_team_detail($entry, $value)
    {
        //SQL INJECTION GEFAHR BEI UMSTELLUNG AUF PREPARE?
        $team_id = $this->team_id;
        $sql = "UPDATE teams_details SET $entry = '$value' WHERE team_id='$team_id'";
        db::writedb($sql); 
    }

    //Eine Liste aller TurnierIDs zu dem die TeamID angemeldet ist
    function get_turniere_angemeldet()
    {
        $team_id = $this->team_id;
        $sql = "SELECT turnier_id, liste FROM turniere_liste WHERE team_id = $team_id";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            $return[$x['turnier_id']] = $x['liste'];
        }
        return db::escape($return);
    }

    //Teamstrafen
    public static function strafe_eintragen($team_id, $verwarnung, $turnier_id=NULL, $grund, $prozentsatz , $saison = Config::SAISON)
    {
        $sql="INSERT INTO teams_strafen (team_id, verwarnung, turnier_id, grund, prozentsatz, saison)
            VALUES ('$team_id','$verwarnung','$turnier_id','$grund','$prozentsatz', '$saison')";
            db::writedb($sql);
    }
    public static function strafe_loeschen($strafe_id){
        $sql="DELETE FROM teams_strafen WHERE strafe_id = '$strafe_id'";
        db::writedb($sql);
    }
    public static function get_all_strafen(){
        $sql="SELECT teams_strafen.*, teams_liga.teamname, turniere_details.ort, turniere_liga.datum 
            FROM teams_strafen
            INNER JOIN teams_liga
            ON teams_liga.team_id = teams_strafen.team_id
            LEFT JOIN turniere_liga
            ON turniere_liga.turnier_id = teams_strafen.turnier_id
            LEFT JOIN turniere_details
            ON turniere_details.turnier_id = teams_strafen.turnier_id
            WHERE teams_strafen.saison = '".Config::SAISON."'
            AND teams_liga.aktiv = 'Ja'";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            $return[$x['strafe_id']] = $x;
        }
        return db::escape($return);
    }

    function teamfoto($target)
    {
        $team_id = $this->team_id;
        $sql="UPDATE teams_details SET teamfoto = '$target' WHERE team_id='$team_id'";
        //db::debug($sql);
        db::writedb($sql);
    }

    function delete_teamfoto($target)
    {
        $team_id = $this->team_id;
        //Foto löschen
        if (file_exists($target)){
            unlink ($target);
        }
        //Fotolink aus der Datenbank entfernen
        $sql="UPDATE teams_details SET teamfoto = '' WHERE team_id='$team_id'";
        db::writedb($sql);
    }
}