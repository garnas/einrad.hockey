<?php
class spiel {
    public $teamA_ID;
    public $teamB_ID;
    public $turnierID;
    public $schiriA_ID;
    public $schiriB_ID;
    public $toreA;
    public $toreB;
    public $spielzeit;
    public $halbzeitPause;
    public $spielPause;
    public $startzeit;
    private $spielid;
    private $spielIndex;
    private $penaltyA=0;
    private $penaltyB=0;
    private $gruppe=0;
    /**
     * @return mixed
     */
    public function getPenaltyA()
    {
        return $this->penaltyA;
    }

    /**
     * @param mixed $penaltyA
     */
    public function setPenaltyA($penaltyA): void
    {
        $this->penaltyA = $penaltyA;
    }

    /**
     * @return mixed
     */
    public function getPenaltyB()
    {
        return $this->penaltyB;
    }

    /**
     * @param mixed $penaltyB
     */
    public function setPenaltyB($penaltyB): void
    {
        $this->penaltyB = $penaltyB;
    }

    function __construct($spielid){
        $this->spielid=$spielid;
        $sql="SELECT *  FROM spiele WHERE spiel_ID='$spielid'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        $this->teamA_ID=$result['teamA_ID'];
        $this->teamB_ID=$result['teamB_ID'];
        $this->turnierID=$result['turnier_ID'];
        $this->schiriA_ID=$result['schiriA_Team_ID'];
        $this->schiriB_ID=$result['schiriB_Team_ID'];
        $this->schiriB_ID=$result['schiriB_Team_ID'];
        $this->schiriB_ID=$result['schiriB_Team_ID'];
        $this->toreA=$result['toreA'];
        $this->toreB=$result['toreB'];
        $this->spielIndex=$result['spiel_Index'];
        $this->penaltyA=$result["penaltyA"];
        $this->penaltyB=$result["penaltyB"];
        $this->gruppe=$result["gruppe"];
    }
    static function createSpiel($teamA_ID,$teamB_ID,$turnierID, $schiriA_ID,$schiriB_ID,$toreA,$toreB,$spiel_Index,$gruppe=0){
        //test if already exists
        $sql="SELECT spiel_ID FROM spiele WHERE turnier_ID='$turnierID' AND spiel_Index='$spiel_Index'";
        $result=db::readdb($sql);
        if($result=mysqli_fetch_assoc($result)){
           // echo "Spiel existiert bereits!!!!!!!!!!!!!!!";
            return $result["spiel_ID"];
        }
        $sql="INSERT INTO spiele (teamA_ID, teamB_ID, turnier_ID, schiriA_Team_ID, schiriB_Team_ID,toreA,toreB,spiel_Index,gruppe) 
            VALUES ('$teamA_ID','$teamB_ID','$turnierID','$schiriA_ID','$schiriB_ID','$toreA','$toreB','$spiel_Index', '$gruppe')";
        db::writedb($sql);
        $sql="SELECT spiel_ID FROM spiele WHERE spiel_Index='$spiel_Index' AND turnier_ID='$turnierID' AND gruppe='$gruppe'";
        $result=db::readdb($sql);
        $result=mysqli_fetch_assoc($result);
        $id=$result["spiel_ID"];
        return $id;
    }
    public function update_tore($toreA,$toreB){
        $this->toreA=$toreA;
        $this->toreB=$toreB;
        $sql="UPDATE spiele SET toreA='$toreA', toreB='$toreB' WHERE spiel_ID='$this->spielid'";
        db::writedb($sql);
    }
    public function update_penalty($penaltyA,$penaltyB){
        $this->penaltyA=$penaltyA;
        $this->penaltyB=$penaltyB;
        $sql="UPDATE spiele SET penaltyA='$penaltyA', penaltyB='$penaltyB' WHERE spiel_ID='$this->spielid'";
        db::writedb($sql);
    }
    public function update_All_Tore($toreA,$toreB,$penaltyA,$penaltyB){
        $this->toreA=$toreA;
        $this->toreB=$toreB;
        $this->penaltyA=$penaltyA;
        $this->penaltyB=$penaltyB;
        $sql="UPDATE spiele SET toreA='$toreA', toreB='$toreB',penaltyA='$penaltyA', penaltyB='$penaltyB' WHERE spiel_ID='$this->spielid'";
        db::writedb($sql);
    }

    /**
     * @return mixed|string
     */
    public function getTeamAID()
    {
        return $this->teamA_ID;
    }

    /**
     * @param mixed|string $teamA_ID
     */
    public function setTeamAID($teamA_ID): void
    {
        $this->teamA_ID = $teamA_ID;
    }

    /**
     * @return mixed|string
     */
    public function getTeamBID()
    {
        return $this->teamB_ID;
    }

    /**
     * @param mixed|string $teamB_ID
     */
    public function setTeamBID($teamB_ID): void
    {
        $this->teamB_ID = $teamB_ID;
    }

    /**
     * @return mixed|string
     */
    public function getTurnierID()
    {
        return $this->turnierID;
    }

    /**
     * @param mixed|string $turnierID
     */
    public function setTurnierID($turnierID): void
    {
        $this->turnierID = $turnierID;
    }

    /**
     * @return mixed|string
     */
    public function getSchiriAID()
    {
        return $this->schiriA_ID;
    }

    /**
     * @param mixed|string $schiriA_ID
     */
    public function setSchiriAID($schiriA_ID): void
    {
        $this->schiriA_ID = $schiriA_ID;
    }

    /**
     * @return mixed|string
     */
    public function getSchiriBID()
    {
        return $this->schiriB_ID;
    }

    /**
     * @param mixed|string $schiriB_ID
     */
    public function setSchiriBID($schiriB_ID): void
    {
        $this->schiriB_ID = $schiriB_ID;
    }

    /**
     * @return mixed|string
     */
    public function getToreA()
    {
        return $this->toreA;
    }

    /**
     * @param mixed|string $toreA
     */
    public function setToreA($toreA): void
    {
        $this->toreA = $toreA;
    }

    /**
     * @return mixed|string
     */
    public function getToreB()
    {
        return $this->toreB;
    }

    /**
     * @param mixed|string $toreB
     */
    public function setToreB($toreB): void
    {
        $this->toreB = $toreB;
    }

    /**
     * @return mixed
     */
    public function getSpielzeit()
    {
        return $this->spielzeit;
    }

    /**
     * @param mixed $spielzeit
     */
    public function setSpielzeit($spielzeit): void
    {
        $this->spielzeit = $spielzeit;
    }

    /**
     * @return mixed
     */
    public function getHalbzeitPause()
    {
        return $this->halbzeitPause;
    }

    /**
     * @param mixed $halbzeitPause
     */
    public function setHalbzeitPause($halbzeitPause): void
    {
        $this->halbzeitPause = $halbzeitPause;
    }

    /**
     * @return mixed
     */
    public function getSpielPause()
    {
        return $this->spielPause;
    }

    /**
     * @param mixed $spielPause
     */
    public function setSpielPause($spielPause): void
    {
        $this->spielPause = $spielPause;
    }

    /**
     * @return mixed
     */
    public function getStartzeit()
    {
        return $this->startzeit;
    }

    /**
     * @param mixed $startzeit
     */
    public function setStartzeit($startzeit): void
    {
        $this->startzeit = $startzeit;
    }

    /**
     * @return mixed
     */
    public function getSpielid()
    {
        return $this->spielid;
    }

    /**
     * @param mixed $spielid
     */
    public function setSpielid($spielid): void
    {
        $this->spielid = $spielid;
    }

    /**
     * @return mixed|string
     */
    public function getSpielIndex()
    {
        return $this->spielIndex;
    }

    /**
     * @param mixed|string $spielIndex
     */
    public function setSpielIndex($spielIndex): void
    {
        $this->spielIndex = $spielIndex;
    }
    public function getWinnerID(){
        $alleA=$this->toreA+$this->penaltyA;
        $alleB=$this->toreB+$this->penaltyB;
        if($alleA>$alleB){
            return $this->teamA_ID;
        }else if($alleB>$alleA){
            return $this->teamB_ID;
        }else{
            return -1;
        }
    }
    public function getLooserID(){
        $alleA=$this->toreA+$this->penaltyA;
        $alleB=$this->toreB+$this->penaltyB;
        if($alleA>$alleB){
            return $this->teamB_ID;
        }else if($alleB>$alleA){
            return $this->teamA_ID;
        }else{
            return -1;
        }
    }
}
