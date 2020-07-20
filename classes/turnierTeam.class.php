<?php
class turnierTeam {
    private $tore=0;
    private $gegenTore=0;
    private $turnierPunkte=0;
    private $turnier_id;
    private $team_id;
    //private $wertigkeit;
    private $gruppe;

    //private $ligaPunkte=0;

    function __construct($turnier_id, $team_id,$gruppe=0)
    {
        $this->team_id=$team_id;
        $this->turnier_id=$turnier_id;
        $this->gruppe=$gruppe;
        //$sql="SELECT wertigkeit FROM rangtabelle Where team_id='$team_id'";
        //$result=db::readdb($sql);
        //$result=mysqli_fetch_assoc($result);
        //$this->wertigkeit=$result["wertigkeit"];
        $sql="SELECT plaetze FROM turniere_details WHERE turnier_id='$turnier_id'";
        $result=db::readdb($sql);
        $result=mysqli_fetch_assoc($result);
        $plaetze=$result["plaetze"];

        //echo "Turnierteam konstruiert $team_id <br>";
        $this->createIfNotExistent();
    }

    /**
     *testet obt turneirteams schon existieren und fügt diese evtl. der Datenbank hinzu
     */
    private function createIfNotExistent(){
        $sql="SELECT turnier_team_id FROM turnier_teams WHERE turnier_id='$this->turnier_id' AND team_id='$this->team_id'AND gruppe='$this->gruppe'";
        $result=db::readdb($sql);
        if(!$result->num_rows>0){
           // echo "TurnierTeam kreieren";
            $sql="INSERT INTO turnier_teams (turnier_id, team_id, tore, gegentore,turnier_punkte, gruppe) 
                VALUES ('$this->turnier_id','$this->team_id','$this->tore','$this->gegenTore','$this->turnierPunkte','$this->gruppe') ";
            db::writedb($sql);
        }else{
          // echo  "Turnierteam existiert bereits";
        }
    }

    /**
     * updatet die Tore, Gegentore und Turnierpunkte, und updatet automatisch die Datenbank
     * nur nötig wenn sich Spiele geändert haben!
     *
     */
    public function updateTurnierTeam($index){
        if($this->gruppe<0){
            $sql="SELECT toreA,toreB,penaltyA,penaltyB FROM spiele WHERE turnier_ID='$this->turnier_id'AND teamA_ID='$this->team_id' AND spiel_Index<='$index'";
        }else{
            $sql="SELECT toreA,toreB,penaltyA,penaltyB FROM spiele WHERE turnier_ID='$this->turnier_id'AND teamA_ID='$this->team_id' AND spiel_Index<='$index' AND gruppe='$this->gruppe'";
        }
        $result=db::readdb($sql);
        while($dsatz=mysqli_fetch_assoc($result)){
            $dsatz=$this->filterWrongDsatz($dsatz);
            $toreA=$dsatz["toreA"]+$dsatz["penaltyA"];
            $toreB=$dsatz["toreB"]+$dsatz["penaltyB"];
            $this->tore+=$toreA;
            $this->gegenTore+=$toreB;
            if($toreA>$toreB){
                $this->turnierPunkte+=3;
            }else if($toreA==$toreB){
                $this->turnierPunkte+=1;
            }
        }
        if($this->gruppe<0){
            $sql="SELECT toreA,toreB,penaltyA,penaltyB FROM spiele WHERE turnier_ID='$this->turnier_id'AND teamB_ID='$this->team_id' AND spiel_Index<='$index'";
        }else{
            $sql="SELECT toreA,toreB,penaltyA,penaltyB FROM spiele WHERE turnier_ID='$this->turnier_id'AND teamB_ID='$this->team_id' AND spiel_Index<='$index'AND gruppe='$this->gruppe'";
        }
        $sql="SELECT toreA,toreB,penaltyA,penaltyB FROM spiele WHERE turnier_ID='$this->turnier_id'AND teamB_ID='$this->team_id' AND spiel_Index<='$index'AND gruppe='$this->gruppe'";
        $result=db::readdb($sql);
        while($dsatz=mysqli_fetch_assoc($result)){
            $dsatz=$this->filterWrongDsatz($dsatz);
            $toreA=$dsatz["toreA"]+$dsatz["penaltyA"];
            $toreB=$dsatz["toreB"]+$dsatz["penaltyB"];
            $this->tore+=$toreB;
            $this->gegenTore+=$toreA;
            if($toreA<$toreB){
                $this->turnierPunkte+=3;
            }else if($toreA==$toreB){
                $this->turnierPunkte+=1;
            }
        }
        $this->updateDatenbank();
    }

    /**
     * wird automatisch von updateTurnierTeams aufgerufen
     */
    private function updateDatenbank(){
        $team=Team::teamid_to_teamname($this->team_id);
       // echo "<br> _____________________________ $team mit $this->tore und $this->gegenTore <br>";
        $sql="UPDATE turnier_teams SET tore='$this->tore', gegentore='$this->gegenTore', turnier_punkte='$this->turnierPunkte'
             WHERE turnier_id='$this->turnier_id' AND team_id='$this->team_id' AND gruppe='$this->gruppe'";
        db::writedb($sql);

    }
    private function filterWrongDsatz($dsatz){
        $dsatz["toreA"]=$this->filterWrong($dsatz["toreA"]);
        $dsatz["toreB"]=$this->filterWrong($dsatz["toreB"]);
        $dsatz["penaltyA"]=$this->filterWrong($dsatz["penaltyA"]);
        $dsatz["penaltyB"]=$this->filterWrong($dsatz["penaltyB"]);
        return $dsatz;
    }
    private function filterWrong($tor){
        if($tor<0){
            $tor=0;
        }
        return $tor;
    }


}