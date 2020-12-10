<?php
class TurnierReport {

    public $turnier_id;
    function __construct($turnier_id)
    {
        $this->turnier_id = $turnier_id;
    }
    //Zeitstrafen
    function get_zeitstrafen(){
        $turnier_id = $this->turnier_id;
        $sql = "SELECT * FROM spieler_zeitstrafen WHERE turnier_id = $turnier_id";
        $result = db::read($sql);
        while ($x = mysqli_fetch_assoc($result)){
            $zeitstrafen[$x['zeitstrafe_id']] = $x;
        }
        return db::escape($zeitstrafen ?? array());
    }
    function new_zeitstrafe($spieler, $dauer, $team_a, $team_b, $grund){
        $turnier_id = $this->turnier_id;
        $sql = "INSERT INTO spieler_zeitstrafen (turnier_id, spieler, dauer, team_a, team_b, grund) 
            VALUES ('$turnier_id', '$spieler', '$dauer', '$team_a', '$team_b', '$grund')";
        db::write($sql);
    }
    function delete_zeitstrafe($zeitstrafe_id){
        $turnier_id = $this->turnier_id;
        $sql = "DELETE FROM spieler_zeitstrafen WHERE zeitstrafe_id = '$zeitstrafe_id' AND turnier_id = '$turnier_id'";
        db::write($sql);
    }

    //Spielerausleihe
    function get_spieler_ausleihen(){
        $turnier_id = $this->turnier_id;
        $sql = "SELECT * FROM spieler_ausleihen WHERE turnier_id = $turnier_id";
        $result = db::read($sql);
        while ($x = mysqli_fetch_assoc($result)){
            $return[$x['ausleihe_id']] = $x;
        }
        return db::escape($return ?? array());
    }
    function new_spieler_ausleihe($spieler, $team_auf, $team_ab){
        $turnier_id = $this->turnier_id;
        $sql = "INSERT INTO spieler_ausleihen (turnier_id, spieler, team_auf, team_ab) 
            VALUES ('$turnier_id', '$spieler', '$team_auf', '$team_ab')";
        db::write($sql);
        //db::debug($sql);
    }
    function delete_spieler_ausleihe($ausleihe_id){
        $turnier_id = $this->turnier_id;
        $sql = "DELETE FROM spieler_ausleihen WHERE ausleihe_id = '$ausleihe_id' AND turnier_id = '$turnier_id'";
        db::write($sql);
    }
    
    //Turnierbericht
    function get_turnier_bericht()
    {
        $turnier_id = $this->turnier_id;
        $sql = "SELECT bericht FROM turniere_berichte WHERE turnier_id = '$turnier_id'";
        $return = db::read($sql);
        $return = mysqli_fetch_assoc($return);
        return db::escape($return['bericht'] ?? '');
    }
    function kader_check()
    {
        $turnier_id = $this->turnier_id;
        $sql = "SELECT kader_ueberprueft FROM turniere_berichte WHERE turnier_id = '$turnier_id'";
        $return = db::read($sql);
        $return = mysqli_fetch_assoc($return);
        if (!empty($return) && $return['kader_ueberprueft'] == 'Ja'){
            return true;
        }
        return false;
    }
    function set_turnier_bericht($bericht, $kader_check = 'Nein'){
        $turnier_id = $this->turnier_id;
        $check = db::read("SELECT * FROM turniere_berichte WHERE turnier_id = '$turnier_id'");
        if(mysqli_num_rows($check) == 0){
            $sql = "INSERT INTO turniere_berichte (turnier_id, bericht, kader_ueberprueft) VALUES ('$turnier_id', '$bericht', '$kader_check')";
        }else{
            $sql = "UPDATE turniere_berichte SET bericht='$bericht', kader_ueberprueft = '$kader_check' WHERE turnier_id = '$turnier_id'";
        }
        db::write($sql);
    }
}