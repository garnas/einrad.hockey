<?php
class Spieler {
    public int $spieler_id;

    function __construct($spieler_id)
    {
        $this->spieler_id = $spieler_id;
    }

    //Erstellt einen neuen Spieler in der Datenbank
    public static function create_new_spieler($vorname,$nachname,$jahrgang,$geschlecht,$team_id): bool
    {
        $saison = Config::SAISON;
        //Es wird getestet, ob der Spieler bereits existiert:
        $sql="SELECT spieler_id, team_id, letzte_saison FROM spieler WHERE vorname='$vorname' AND nachname='$nachname' AND jahrgang='$jahrgang' AND geschlecht='$geschlecht'";
        $result=db::read ($sql);
        $result=mysqli_fetch_assoc($result);
        $spieler_id = $result['spieler_id'] ?? 0;
        
        if ($spieler_id>0){ //Testen ob der Spieler schon existiert
            if ($result['letzte_saison'] <  $saison){ //Testet ob der Spieler aus der Datenbank übernommen werden kann
                $sql="UPDATE spieler SET team_id = '$team_id', letzte_saison = ' $saison' WHERE spieler_id = '$spieler_id'";
                db::write($sql);
                Form::affirm ("Der Spieler wurde vom Team ".Team::teamid_to_teamname($result['team_id'])." übernommen.");
                return true;
            }else{
                Form::error ("Der Spieler steht bereits im Kader für folgendes Team: " . Team::teamid_to_teamname($result['team_id']) . "<br> Bitte wende dich an den Ligaausschuss (" . Form::mailto(Config::LAMAIL) . ")");
                return false;
            }
        }else{
            //Spieler wird in Spieler-Datenbank eingetragen
            $sql="INSERT INTO spieler(vorname, nachname, jahrgang, geschlecht, team_id, letzte_saison) VALUES ('$vorname','$nachname','$jahrgang','$geschlecht','$team_id','".Config::SAISON."')";
            db::write ($sql);
            return true;
        } 
    }

    //Check ob der Spieler dem Kader hinzugefügt werden darf
    public static function check_timing(): bool
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
    public static function get_teamkader($team_id, $saison = Config::SAISON) :array
    {
        $sql = "SELECT *  
            FROM spieler 
            WHERE team_id='$team_id' 
            AND letzte_saison = '$saison'
            ORDER BY letzte_saison DESC, vorname";
        $result = db::read($sql);
        while ($x = mysqli_fetch_assoc($result)){
            if (strtotime($x['zeit']) < 0){
                $x['zeit'] = '--'; //Diese Funktion wurde erst am später hinzugefügt.
            }
            $return[$x['spieler_id']] = $x;

        }
        return db::escape($return ?? array()); //Array 
    }

    //Ausnahme für die Saison 20/21, da viele Teams ihre Spieler in der Corona_Saison nicht zurückgemeldet haben
    public static function get_teamkader_vorsaison($team_id): array
    {   
        $kader_vorsaison = self::get_teamkader($team_id, Config::SAISON - 1);
        $kader_vorvorsaison = self::get_teamkader($team_id, Config::SAISON - 2);
        $return = $kader_vorsaison + $kader_vorvorsaison;
        return db::escape($return ?? array()); //Array 
    }

    //Anzahl der Spieler in der Datenbank
    public static function count_spieler(): int
    {   
        $saison = Config::SAISON - 1; //Zählt die Spieler welche in dieser oder in der letzten Saison in einem Kader waren
        $sql = "SELECT count(*) FROM spieler WHERE letzte_saison >= '$saison'";
        $result = db::read($sql);
        $return = mysqli_fetch_assoc($result);
        return db::escape($return['count(*)']);
    }

    //Gibt ein Spielerlisten Array aus mit [0] => Vorname Nachname  und [1] => Spieler_id
    public static function get_spielerliste(): array
    {
        $sql = "SELECT vorname,nachname,spieler_id FROM spieler ORDER BY vorname";
        $result = db::read($sql);
        while ($x = mysqli_fetch_assoc($result)){
            $spielerliste[$x['spieler_id']] = $x['vorname']." ".$x['nachname'];
        }
        return db::escape($spielerliste ?? array()); //Array
    }
    
    //Alle Details eines Spielers werden in einem Array zusammen übergeben
    function get_spieler_details(): array
    {
        $spieler_id = $this->spieler_id;
        $sql = "SELECT *  FROM spieler WHERE spieler_id='$spieler_id'";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result);
    }

    //Ein Spieler Detail verändern: $entry -> Spaltenname in der Datenbank, $value->Wert der in die Datenbank eingetragen werden soll
    function set_spieler_detail($entry, $value)
    {
        $spieler_id = $this->spieler_id;
        if ($entry == 'team_id' or $entry == 'letzte_saison'){
            $zeit = '';
        }else{
            $zeit = ', zeit=zeit';
        }
        $sql = "UPDATE spieler SET $entry = '$value'$zeit WHERE spieler_id='$spieler_id'";
        db::write($sql);
    }

    //Der Spieler wird aus der Datenbank gelöscht
    function delete_spieler()
    {
        $spieler_id = $this->spieler_id;
        $sql = "DELETE FROM spieler WHERE spieler_id='$spieler_id'";
        db::write($sql);
    }
    public static function get_anz_schiris()
    {   
        $saison = Config::SAISON;
        $sql = "SELECT count(*) 
            FROM `spieler` 
            INNER JOIN teams_liga 
            ON teams_liga.team_id = spieler.team_id 
            WHERE teams_liga.aktiv = 'Ja' 
            AND spieler.schiri >= '$saison' OR spieler.schiri = 'Ausbilder/in'";
        return mysqli_fetch_assoc(db::read($sql))['count(*)'];
    }
}