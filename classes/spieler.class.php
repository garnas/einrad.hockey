<?php
class Spieler {
    function __construct($spieler_id)
    {
        $this->spieler_id = $spieler_id;
    }

    //Erstellt einen neuen Spieler in der Datenbank
    public static function create_new_spieler($vorname,$nachname,$jahrgang,$geschlecht,$team_id)
    {
        $saison = Config::SAISON;
        //Es wird getestet, ob der Spieler bereits existiert:
        $sql="SELECT spieler_id, team_id, letzte_saison FROM spieler WHERE vorname='$vorname' AND nachname='$nachname' AND jahrgang='$jahrgang' AND geschlecht='$geschlecht'";
        $result=db::readdb ($sql);
        $result=mysqli_fetch_assoc($result);
        $spieler_id = $result['spieler_id'] ?? 0;
        
        if ($spieler_id>0){ //Testen ob der Spieler schon existiert
            if ($result['letzte_saison'] <  $saison){ //Testet ob der Spieler aus der Datenbank übernommen werden kann
                $sql="UPDATE spieler SET team_id = '$team_id', letzte_saison = ' $saison' WHERE spieler_id = '$spieler_id'";
                db::writedb($sql);
                Form::affirm ("Der Spieler wurde vom Team ".Team::teamid_to_teamname($result['team_id'])." übernommen.");
                return true;
            }else{
                Form::error ("Der Spieler steht bereits im Kader für folgendes Team: " . Team::teamid_to_teamname($result['team_id']) . "<br> Bitte wende dich an den Ligaausschuss (" . Config::LAMAIL . ")");
                return false;
            }
        }else{
            //Spieler wird in Spieler-Datenbank eingetragen
            $sql="INSERT INTO spieler(vorname, nachname, jahrgang, geschlecht, team_id, letzte_saison) VALUES ('$vorname','$nachname','$jahrgang','$geschlecht','$team_id','".Config::SAISON."')";
            db::writedb ($sql);
            return true;
        } 
    }

    //Check ob der Spieler dem Kader hinzugefügt werden darf
    public static function check_timing()
    {
        //23:59:59 am Saisonende
        $saison_ende = strtotime(Config::SAISON_ENDE) + 25*60*60 - 1;
        $heute = Config::time_offset();
        if ($saison_ende > $heute ){
            return true;
        }
        return false;
    }

    //Gibt den Teamkader des Teams mit der entsprechenden TeamID zurück
    public static function get_teamkader($team_id)
    {
        $sql = "SELECT *  
            FROM spieler 
            WHERE team_id='$team_id' 
            AND letzte_saison='". Config::SAISON . "'
            ORDER BY letzte_saison DESC, vorname ASC";
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            $return[$x['spieler_id']] = $x;
        }
        return db::escape($return); //Array 
    }

    public static function get_teamkader_vorsaison($team_id)
    {   
        $vorsaison = Config::SAISON - 1;
        $vorvorsaison = Config::SAISON - 2;
        $sql = "SELECT *  
            FROM spieler 
            WHERE team_id = '$team_id' 
            AND (letzte_saison = '$vorsaison' OR letzte_saison = '$vorvorsaison')
            ORDER BY letzte_saison DESC, vorname ASC";
        $result = db::readdb($sql);
        db::debug($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            $return[$x['spieler_id']] = $x;
        }
        return db::escape($return ?? array()); //Array 
    }

    //Anzahl der Spieler in der Datenbank
    public static function count_spieler()
    {   
        $saison = Config::SAISON - 1;
        $sql = "SELECT count(*) FROM spieler WHERE letzte_saison >= '$saison'";
        $result = db::readdb($sql);
        $return = mysqli_fetch_assoc($result);
        return db::escape($return['count(*)']);
    }

    //Gibt ein Spielerlisten Array aus mit [0] => Vorname Nachname  und [1] => Spieler_id
    public static function get_spielerliste()
    {
        $sql = "SELECT vorname,nachname,spieler_id  FROM spieler ORDER BY vorname ASC";
        $result = db::readdb($sql);
        while ($x = mysqli_fetch_assoc($result)){
            $spielerliste[$x['spieler_id']] = $x['vorname']." ".$x['nachname'];
        }
        return db::escape($spielerliste); //Array 
    }
    
    //Alle Details eines Spielers werden in einem Array zusammen übergeben
    function get_spieler_details()
    {
        $spieler_id = $this->spieler_id;
        $sql = "SELECT *  FROM spieler WHERE spieler_id='$spieler_id'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result); //array
    }

    //Ein Spieler Detail verändern: $entry -> Spaltenname in der Datenbank, $value->Wert der in die Datenbank eingetragen werden soll
    function set_spieler_detail($entry, $value)
    {
        $spieler_id = $this->spieler_id;
        $sql = "UPDATE spieler SET $entry = '$value' WHERE spieler_id='$spieler_id'";
        db::writedb($sql);
    }

    //Der Spieler wird aus der Datenbank gelöscht
    function delete_spieler()
    {
        $spieler_id = $this->spieler_id;
        $sql = "DELETE FROM spieler WHERE spieler_id='$spieler_id'";
        db::writedb($sql);
    }
}