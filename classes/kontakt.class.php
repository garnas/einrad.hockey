<?php
class Kontakt {
    public int $team_id;

    function __construct($team_id)
    {
        $this->team_id = $team_id; 
    }
    
    //Sucht alle public emails und sortiert diese anschließend in das return array ein BSP: team_id 12 --> 12 => email1,email2,email3
    public static function get_all_public_emails_per_team(): array
    {
        $sql = "SELECT teams_kontakt.email, teams_kontakt.team_id  
            FROM teams_kontakt
            INNER JOIN teams_liga
            ON teams_liga.team_id = teams_kontakt.team_id 
            WHERE public='Ja' AND teams_liga.aktiv = 'Ja'";
        $result = db::readdb($sql);
        $liste=array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($liste,$x);
        }
        $return = array();
        foreach ($liste as $entry){
            if (isset($return[$entry['team_id']])){
                $return[$entry['team_id']] .= ','. $entry['email'];
            }else{
                $return[$entry['team_id']] = $entry['email'];
            }
        }
        return db::escape($return);//array
    }

    public static function get_emails_rundmail(): array
    {
        $sql = "SELECT DISTINCT teams_kontakt.email
            FROM teams_kontakt
            INNER JOIN teams_liga
            ON teams_liga.team_id = teams_kontakt.team_id 
            WHERE teams_liga.aktiv = 'Ja'";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x['email']);
        }
        return db::escape($return);//array
    }

    public static function get_emails_turnier($turnier_id): array
    {   
        //distinct email funktioniert nicht, da sonst teamnamen fehlen oder doppelt vorkommen. 
        $sql = "SELECT teams_kontakt.email, teams_liga.teamname 
        FROM teams_kontakt
        INNER JOIN teams_liga
        ON teams_liga.team_id = teams_kontakt.team_id
        INNER JOIN
        turniere_liste
        ON turniere_liste.team_id = teams_kontakt.team_id
        WHERE teams_liga.aktiv = 'Ja' AND turniere_liste.turnier_id = '$turnier_id'";

        $result = db::readdb($sql);
        $return['emails'] = $return['teamnamen'] = array();
        while ($x = mysqli_fetch_assoc($result)){
            if (!in_array($x['teamname'],$return['teamnamen'])){
                array_push($return['teamnamen'],$x['teamname']);
            }
            if (!in_array($x['email'],$return['emails'])){
                array_push($return['emails'],$x['email']);
            }
        }
        return db::escape($return);//array
    }

    //Erstellt einen neuen Teamkontakteintrag in der Datenbank
    function create_new_team_kontakt($email,$public,$infomail)
    {
        $team_id = $this->team_id;
        $email = strtolower($email);
        $sql="INSERT INTO teams_kontakt (team_id, email, public, get_info_mail) VALUES ('$team_id','$email','$public','$infomail')";
        db::writedb($sql);
    }

    function get_emails(): array
    {
        $team_id = $this->team_id;
        $sql = "SELECT email  FROM teams_kontakt WHERE team_id='$team_id'";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x['email']);
        }
        return db::escape($return);//array
    }

    function get_emails_public(): array
    {
        $team_id = $this->team_id;
        $sql = "SELECT email  FROM teams_kontakt WHERE team_id='$team_id' AND public='Ja'";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x['email']);
        }
        return db::escape($return);//array
    }

    function get_emails_info(): array
    {
        $team_id = $this->team_id;
        $sql = "SELECT email  FROM teams_kontakt WHERE team_id='$team_id' AND get_info_mail='Ja'";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x['email']);
        }
        return db::escape($return);//array
    }

    //Funktioniert nicht mit Form::mailto()
    function get_all_emails(): array
    {
        $team_id = $this->team_id;
        $sql = "SELECT *  FROM teams_kontakt WHERE team_id = '$team_id'";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x);
        }
        return db::escape($return);//array
    }

    function set_public($teams_kontakt_id, $value)
    {
        $sql = "UPDATE teams_kontakt SET public = '$value' WHERE teams_kontakt_id = '$teams_kontakt_id'";
        db::writedb($sql);
    }

    function set_info($teams_kontakt_id, $value)
    {
        $sql = "UPDATE teams_kontakt SET get_info_mail = '$value' WHERE teams_kontakt_id = '$teams_kontakt_id'";
        db::writedb($sql);
    }

    function delete_email($teams_kontakt_id): bool
    {
        if (count($this->get_emails()) > 1){
            $sql = "DELETE FROM teams_kontakt WHERE teams_kontakt_id = '$teams_kontakt_id'";
            db::writedb($sql);
            return true;
        }
        return false;
    }
}