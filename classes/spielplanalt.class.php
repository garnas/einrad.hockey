<?php 
class spielplan{
    private $plaetze;
    private $spielplan;
    private $teams;
    private $plan;
    private $turnier_ID;
    private $spiele=array();
    private $anzahlSpiele;
    private $pause;
    private $halbzeitPause;
    private $spielZeit;
    private $schiri;
    private $startzeit;
    private $ligaPunkte;
    private $turnierFactor;
    private $penWarning;
    private $penaltyNecessary=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
    private $gruppenPhase=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
    private $defaultTeams=array(array("Sieger Spiel 1"),array("Verlierer Spiel 1"));
    private $groupA;
    private $groupB;

    function __construct($turnier_ID)
    {
        $this->turnier_ID=$turnier_ID;
        $this->teams=array();
        //$sql="SELECT team_id FROM turniere_liste WHERE turnier_id='$turnier_ID' AND liste='spiele'";
        $sql="SELECT rangtabelle.team_id, turniere_liste.turnier_id, rangtabelle.wertigkeit, turniere_liste.liste FROM turniere_liste INNER JOIN rangtabelle ON turniere_liste.team_id=rangtabelle.team_id ORDER BY wertigkeit DESC";
        $result=db::readdb($sql);
        $i=0;
        //echo "$result->num_rows <br>";
        if(!$result){
            echo "Turnier exisitiert nicht <br>";
            return ;
        }

        while($dsatz=mysqli_fetch_assoc($result)){
            if($dsatz["liste"]=="spiele"&&$dsatz["turnier_id"]==$turnier_ID){
                $this->teams[$i]=$dsatz["team_id"];

                echo " $i .Team.". $this->teams[$i]." added<br>";
                $i++;
            }
        }
        $sql="SELECT * FROM turniere_details WHERE turnier_id='$turnier_ID'";
        $result=db::readdb($sql);
        $result=mysqli_fetch_assoc($result);
        $this->plaetze=$result['plaetze'];
        $this->spielplan=$result['spielplan'];
        $this->startzeit=$result['startzeit'];
        //echo "Startzeit $this->startzeit";
       // echo "$this->plaetze<br>$this->spielplan<br>$this->startzeit<br>";
        $this->constructSpiele();
        switch ($this->plaetze){
            case 5:
            case 4:
                $this->turnierFactor=6.0/5.0;
                break;
            case 6:
                $this->turnierFactor=1.0;
                break;
            case 7:
                $this->turnierFactor=6.0/7.0;
                break;
            case 8:
                $this->turnierFactor=6.0/8.0;
                break;
        }
    }

    private function constructSpiele(){
        switch ($this->plaetze){
            case 4:
                $this->anzahlSpiele=12;
                $this->pause=3;
                $this->halbzeitPause=0;
                $this->spielZeit=12;
                $this->plan=array(array(4,2,4,4,3,3,2,1,1,3,2,1),array(3,1,2,1,1,2,4,3,4,4,3,2));
                $this->schiri=array(array(2,4,1,3,2,1,3,4,2,1,4,3),array(1,3,3,2,4,4,1,2,3,2,1,4));
                break;
            case 5:
               // echo "in case 5<br>";
                $this->anzahlSpiele=10;
                $this->pause=3;
                $this->halbzeitPause=2;
                $this->spielZeit=30;
                $this->plan=array(array(2,1,3,2,1,4,2,1,3,1),array(5,4,5,4,3,5,3,5,4,2));
                $this->schiri=array(array(4,5,2,5,2,1,4,2,1,3),array(1,2,1,3,4,3,5,3,5,4));
                break;
            case 6:
                $this->anzahlSpiele=15;
                $this->pause=3;
                $this->halbzeitPause=3;
                $this->spielZeit=24;
                $this->plan=array(array(3,2,1,2,4,1,2,3,1,4,2,1,5,3,1),array(6,5,4,3,6,5,4,5,6,5,6,3,6,4,2));
                $this->schiri=array(array(2,3,5,1,2,4,5,4,5,1,5,2,1,5,3),array(1,6,2,4,3,6,1,2,3,6,4,6,3,6,4));
                break;
            case 7:
                $this->anzahlSpiele=21;
                $this->pause=2;
                $this->halbzeitPause=2;
                $this->spielZeit=18;
                $this->plan=array(array(2,1,3,5,2,1,3,4,2,1,3,4,2,1,6,4,2,1,5,3,1),array(7,4,6,7,4,6,5,7,6,5,7,6,5,3,7,5,3,7,6,4,2));
                $this->schiri=array(array(1,7,2,6,7,2,6,3,7,2,5,7,6,2,3,6,5,2,7,6,3),array(4,2,1,3,5,4,1,5,4,6,1,3,4,5,1,7,4,3,1,5,4));
                break;
            case 8:
                if($this->spielplan=="dko"){

                }else{
                    //8er Gruppe
                    $this->anzahlSpiele=12+8;
                    $this->pause=3;
                    $this->halbzeitPause=0;
                    $this->spielZeit=12;
                    $this->plan=array(array(1,4,2,3,1,4,2,3,6,1,5,2),array(6,7,5,8,7,6,8,5,7,4,8,3));
                    $this->schiri=array(array(2,3,1,4,8,5,7,4,5,2,1,4),array(5,8,6,7,2,3,6,1,8,3,6,7));
                    //TODO create weitere Spiele wenn andere Spiele fertig ausgefüllt
                    //TODO -> wenn 12. oder das 16.  Spiel eingetragen weitere 4 erstelln
                    $this->groupA=array(0,3,5,6);
                    $this->groupB=array(1,2,4,7);
                    $this->defaultTeams[0]=array("4. Gruppe A", "3. Gruppe B");
                    $this->defaultTeams[1]=array("4. Gruppe A", "3. Gruppe B");
                    $this->defaultTeams[2]=array("4. Gruppe A", "3. Gruppe B");
                    $this->defaultTeams[3]=array("4. Gruppe A", "3. Gruppe B");
                    $this->defaultTeams[4]=array("4. Gruppe A", "3. Gruppe B");
                    $this->defaultTeams[5]=array("4. Gruppe A", "3. Gruppe B");
                    $this->defaultTeams[6]=array("4. Gruppe A", "3. Gruppe B");
                    $this->defaultTeams[7]=array("4. Gruppe A", "3. Gruppe B");
                }
                break;
        }
        $games=$this->anzahlSpiele;
        if($this->plaetze==8&&$this->spielplan!="dko"){
            $games=12;
            $this->gruppenPhase =array(1,1,2,2,1,1,2,2,1,1,2,2,0,0,0,0,0,0,0,0,0,0,0);
            echo "in Test !!!!!!!!!!!!!!!!!!!!!!!<br>";
        }
        echo " count teams ".count($this->teams)." Teams <br>";
        echo " count plan ".count($this->plan)." ".count($this->plan[0])." Teams <br>";
        for ($i=0;$i<$games;$i++){
            $this->spiele[$i]=spiel::createSpiel($this->teams[$this->plan[0][$i]-1],$this->teams[$this->plan[1][$i]-1]
            ,$this->turnier_ID,$this->teams[$this->schiri[0][0]-1],$this->teams[$this->schiri[1][0]-1],-1,-1,$i,$this->gruppenPhase[$i]);
            echo " Spiel für Gruppe ".$this->gruppenPhase[$i]." erstellt<br>";
        }
        if($this->plaetze==8&&$this->spielplan!="dko"){
            $this->createGamesfor8Gruppe();
        }
    }

    private function createGamesfor8Gruppe(){
        //testen ob 12. Spiel schon bearbeitet
        if(count($this->plan[0])==12){
            $sql="SELECT toreA FROM spiele WHERE turnier_ID='$this->turnier_ID' AND spiel_Index='11'";
            $result=db::readdb($sql);
            $result=mysqli_fetch_assoc($result);
            if($result["toreA"]>=0){
                $erg1=$this->getTurnierTeamResults(1);
                $erg2=$this->getTurnierTeamResults(2);
                //TODO spiel 12-15 anhängen!!
                $this->spiele[12]=spiel::createSpiel($erg1[3]["team_id"],$erg2[2]["team_id"],$this->turnier_ID,$erg1[0]["team_id"],$erg2[3]["team_id"],-1,-1,12);
                $this->spiele[13]=spiel::createSpiel($erg1[2]["team_id"],$erg2[4]["team_id"],$this->turnier_ID,$erg1[3]["team_id"],$erg2[0]["team_id"],-1,-1,13);
                $this->spiele[14]=spiel::createSpiel($erg1[1]["team_id"],$erg2[0]["team_id"],$this->turnier_ID,$erg1[2]["team_id"],$erg2[1]["team_id"],-1,-1,14);
                $this->spiele[15]=spiel::createSpiel($erg1[0]["team_id"],$erg2[1]["team_id"],$this->turnier_ID,$erg1[1]["team_id"],$erg2[2]["team_id"],-1,-1,15);

            }
        }else if(isset($spiele[15])){
            $sql="SELECT toreA FROM spiele WHERE turnier_ID='$this->turnier_ID' AND spiel_Index='15'";
            $result=db::readdb($sql);
            if($result["toreA"]>=0){
                $wL=$this->getSiegerHalbfinale();
                $this->spiele[16]=spiel::createSpiel($wL[0]["l"],$wL[1]["l"],$this->turnier_ID,$wL[0]["w"],$wL[1]["w"],-1,-1,16);
                $this->spiele[17]=spiel::createSpiel($wL[0]["w"],$wL[1]["w"],$this->turnier_ID,$wL[0]["l"],$wL[1]["l"],-1,-1,17);
                $this->spiele[18]=spiel::createSpiel($wL[2]["l"],$wL[3]["l"],$this->turnier_ID,$wL[2]["w"],$wL[3]["w"],-1,-1,18);
                $this->spiele[19]=spiel::createSpiel($wL[2]["w"],$wL[3]["w"],$this->turnier_ID,$wL[2]["l"],$wL[3]["l"],-1,-1,19);
            }

        }

    }

    public function getSiegerHalbfinale(){
         $winnerLooser=0;
         $n=0;
        for($i=12;$i<16;$i++){
            $spiel=new spiel($this->spiele[$i]);
            $winnerLooser[$n]["w"]=$spiel->getWinnerID();
            $winnerLooser[$n]["l"]=$spiel->getLooserID();
            $n++;
        }
        return $winnerLooser;
    }
    /**
     * @param $index
     * @return string
     */
    public function getSchiriString($index){
        return $this->schiri[0][$index].",".$this->schiri[1][$index];
    }

    /**
     * @return mixed
     */
    public function getAnzahlSpiele()
    {
        return $this->anzahlSpiele;
    }

    /**
     * returns spiel_ID!!
     * @return mixed
     */
    public function getSpiel_ID($index)
    {
       if(isset($this->spiele[$index])){
           return $this->spiele[$index];
       }
       return -1;
    }
    public function getTeamAString($index){
        return Team::teamid_to_teamname($this->teams[$this->plan[0][$index]-1]);
    }
    public function getTeamBString($index){
        return Team::teamid_to_teamname($this->teams[$this->plan[1][$index]-1]);
    }

    /**
     * @return mixed|string
     */
    public function getPlaetze()
    {
        return $this->plaetze;
    }
    public function getTeam_Info($index,$group=0){
        $teamIndex=$index;
        if($group==1){//->Gruppe A eines 8erGruppe Turnier
            switch($index){
                case 0:
                   $teamIndex=0;
                   break;
                case 1:
                    $teamIndex=3;
                    break;
                case 2:
                    $teamIndex=5;
                    break;
                case 3:
                    $teamIndex=6;
                    break;
            }
        }else if($group==2){
            switch($index){
                case 0:
                    $teamIndex=1;
                    break;
                case 1:
                    $teamIndex=2;
                    break;
                case 2:
                    $teamIndex=4;
                    break;
                case 3:
                    $teamIndex=7;
                    break;
            }
        }
        echo "getTeamInfo: count Teams".count($this->teams)." teamIndex: ".$teamIndex." <br>";
        $result["teamname"]=Team::teamid_to_teamname($this->teams[$teamIndex]);
        $result["wertigkeit"]=$this->getTeamWertigkeit($teamIndex);
        return $result;
    }
    public function getTeam_ID($index){
        return $this->teams[$index];
    }
    public function getTeamWertigkeit($index){
        $teamID=$this->teams[$index];
        $sql="SELECT wertigkeit FROM rangtabelle Where team_id='$teamID'";
        $result=db::readdb($sql);
        $result=mysqli_fetch_assoc($result);
        return $result["wertigkeit"];
    }
    public function createTurnierTeams($gruppe=0){
        $sql="SELECT spiel_Index FROM spiele WHERE toreA!='-1'  ORDER BY spiel_Index DESC LIMIT 1";
        $result=db::readdb($sql);
        $result=mysqli_fetch_assoc($result);
        $index=-1;
        if(!empty($result["spiel_Index"])){
            $index=$result["spiel_Index"];//TODO Wenn aktuelles Spiel 0:0 ausgeht wird es nicht in die LiveTabelle eingearbeitet
        }
        
        echo "Letzt eingetragenes Spiel ist ".$index."<br>";
        if($index==$this->anzahlSpiele-1){
            $index=100;//Dadurch am Ende des Turnieres auf jeden Fall richtiger Spielstand
        }
        if($gruppe<=0){
            for($i=0;$i<$this->plaetze;$i++){
                $turnierTeam=new turnierTeam($this->turnier_ID,$this->teams[$i],$gruppe);
                $turnierTeam->updateTurnierTeam($index);
            }
        }else if($gruppe==1){
            for($i=0;$i<4;$i++){
                $turnierTeam=new turnierTeam($this->turnier_ID,$this->teams[$this->groupA[$i]],$gruppe);
                $turnierTeam->updateTurnierTeam($index);
            }
        }else if($gruppe==2){
            for($i=0;$i<4;$i++){
                $turnierTeam=new turnierTeam($this->turnier_ID,$this->teams[$this->groupB[$i]],$gruppe);
                $turnierTeam->updateTurnierTeam($index);
            }
        }

    }

    public function get8erGruppeEndResultat(){
        $turnierId=$this->turnier_ID;
        $sql="SELECT team_id, tore, gegentore, turnier_punkte FROM turnier_teams WHERE turnier_id='$turnierId' AND gruppe='-1' ORDER By turnier_punkte DESC, tore-gegentore DESC ";
        $result=db::readdb($sql);

        for($i=0;$i<$this->plaetze;$i++){
            echo "get8erGruppeEndResultat <br>";
            $data=mysqli_fetch_assoc($result);
            $erg[$i]["team_id"]=$data["team_id"];
            $erg[$i]["teamname"]=teamid_to_teamname($data["team_id"]);
            $erg[$i]["tore"]=$data["tore"];
            $erg[$i]["gegentore"]=$data["gegentore"];
            $erg[$i]["diff"]=$data["tore"]-$data["gegentore"];
            $erg[$i]["turnierPunkte"]=0;
            $erg[$i]["index"]=$i+1;
            $erg[$i]["value"]=0;
        }
        //switch teams
        if(isset($this->spiele[19])){
            $n=19;
            for($i=0;$i<4;$i++){
                $spiel=new spiel($this->spiele[$n]);
                if($spiel->getWinnerID()!=$erg[2*$i]["team_id"]){
                    $this->switchRowErg($erg,2*$i,2*$i+1);
                }elseif ($spiel->getLooserID()!=$erg[2*$i+1]["team_id"]){
                    $this->switchRowErg($erg,2*$i+1,2*$i+2);
                }
                $n--;
            }
        }
        $dummy=0;
        //TurnierPunke berechnen von unten nach oben
        for($i=$this->plaetze-1;$i>=0;$i--){
            $team_id=$erg[$i]["team_id"];
            $sql="SELECT wertigkeit FROM rangtabelle Where team_id='$team_id'";
            $result=db::readdb($sql);
            $result=mysqli_fetch_assoc($result);
            $wertigkeit=$result["wertigkeit"];
            $dummy+=$this->turnierFactor*$wertigkeit;
            $erg[$i]["ligaPunkte"]=$dummy;
        }
        return $erg;
    }
    public function getTurnierTeamResults($gruppe=0){
        $turnierId=$this->turnier_ID;
        $sql="SELECT team_id, tore, gegentore, turnier_punkte FROM turnier_teams WHERE turnier_id='$turnierId' AND gruppe='$gruppe' ORDER By turnier_punkte DESC, tore-gegentore DESC ";
        $result=db::readdb($sql);
        $number=$this->plaetze;
        
        if($this->spielplan="gru"&&$gruppe!=0){
            echo "____________________-- gru detecdet-------------------------<br>";
            $number=4;
        }
        
        for($i=0;$i<$number;$i++){
            $data=mysqli_fetch_assoc($result);
            $erg[$i]["team_id"]=$data["team_id"];
            echo "getTurnierTeamResults TeamID ".$erg[$i]["team_id"]." <br>";
            $erg[$i]["teamname"]=Team::teamid_to_teamname($data["team_id"]);
            $erg[$i]["tore"]=$data["tore"];
            $erg[$i]["gegentore"]=$data["gegentore"];
            $erg[$i]["diff"]=$data["tore"]-$data["gegentore"];
            $erg[$i]["turnierPunkte"]=$data["turnier_punkte"];
            $erg[$i]["index"]=$i+1;
            $erg[$i]["value"]=0;
        }
        if($this->getActiveGame()>-3){
        $switched=array(0,0,0,0,0,0,0,0,0,0,0,0,0);
        //TODO TESTEN  Mannschaften mit gleich vielen Punkten muessen evtl die Platzierung tauschen
        for($num=$number;$num>0;$num--){
            for($t=0;$t<$number-$num;$t++){
                //echo "t $t , t+num $t+$num";
                if($erg[$t]["turnierPunkte"]==$erg[$t+$num]["turnierPunkte"]&&!$switched[$t]&&!$switched[$t+$num]){
                    $erg=$this->fixSpecialCases($erg,$t,$t+$num);
                    for($x=$t;$x<=$t+$num;$x++){
                        $switched[$x]=1;
                    }
                }
            }
        }
       
        //Warnung generieren
        $switched=array(0,0,0,0,0,0,0,0,0,0,0,0,0);
        $this->penWarning="";
        $penTeams=array();
        $index=0;
        for($num=$number;$num>0;$num--){
            for($t=0;$t<$number-$num;$t++){
                //echo "<br>in warnung t $t , t+num $t+$num <br>";
                if($erg[$t]["turnierPunkte"]==$erg[$t+$num]["turnierPunkte"]&&$erg[$t]["value"]==$erg[$t+$num]["value"]&&!$switched[$t]&&!$switched[$t+$num]){
                    $this->penWarning=$this->penaltyWarning() . "Achtung: Penalty zwischen den Mannschaften ";
                    for($x=$t;$x<=$t+$num;$x++){
                        if($x>$t){
                            $this->penWarning=$this->penWarning . ", ";
                        }
                        $switched[$x]=1;
                        $teamName=$erg[$x]["teamname"];
                        $penTeams[$index]=$erg[$x]["team_id"];
                        $index++;
                        $this->penWarning=$this->penWarning . "$teamName";
                    }
                    $this->penWarning=$this->penWarning . "!!!";
                }
            }
        }
        
        $this->setNecessaryPenalty($erg,$penTeams,$index,$gruppe);
        //echo " Penalty Warning: ". $this->penWarning;
        $dummy=0;
        //TurnierPunke berechnen von unten nach oben
        for($i=$number-1;$i>=0;$i--){
            $team_id=$erg[$i]["team_id"];
            $sql="SELECT wertigkeit FROM rangtabelle Where team_id='$team_id'";
            $result=db::readdb($sql);
            $result=mysqli_fetch_assoc($result);
            $wertigkeit=$result["wertigkeit"];
            $dummy+=$this->turnierFactor*$wertigkeit;
            $erg[$i]["ligaPunkte"]=$dummy;
            echo " ligaPunkte = ".$erg[$i]["ligaPunkte"]."<br>";
        }
         
    }
       
        return $erg;
    }

    /**
     * Berechnet welche Spiele durch ein Penalty entschieden werden müssen
     * es steht schon fest welche Teams am Penalty teilnehmen müssen
     * @param $erg
     * @param $penTeams  Teams zwischen denen ein Penalty schießen stattfinden muessen
     * @param $numberOfTeams
     * @return int  liefert -1 falls Turnier noch im Anfangszustand
     */
    private function setNecessaryPenalty($erg,$penTeams, $numberOfTeams, $gruppe=0){
        //if($this->getActiveGame()<$this->anzahlSpiele/2){
        //    return -1;
        //}
        //Spiele suchen
        $sql="SELECT * FROM spiele WHERE gruppe='$gruppe' AND (";
        for($m=0;$m<$numberOfTeams;$m++){
            for($n=$m+1;$n<$numberOfTeams;$n++){
                if($m>0||$n>$m+1){
                    $sql=$sql . " OR ";
                }
                $team1=$penTeams[$m];
                $team2=$penTeams[$n];
                //echo "penTeams[$m] ".$penTeams[$m]." <br>";
                $sql=$sql . "((teamA_ID=".$team1." AND teamB_ID='$team2') OR (teamA_ID='$team2' AND teamB_ID='$team1'))";
            }
        }
        $sql=$sql .")";
        //echo "$sql";
        $result=db::readdb($sql);
        if($result) {
            while ($data = mysqli_fetch_assoc($result)) {
                $this->penaltyNecessary[$data["spiel_Index"]] = 1.0;
            }
        }
        //echo "Benötigte Penalties: <br>";
        //for($i=0;$i<16;$i++){
            //echo $this->penaltyNecessary[$i]." ,  ";
        //}
    }
    /*
     * loest Sonderfaelle in der Turnierbewertung wenn mehrere Mannschaften gleich viele Punkte haben,
     * wenn mind. 2 mannschaften gleich viele Punkte haben wird ein virtuell ein Miniturnier aus den gespielten Spielen
     * nur zwischen den beteiligten Teams berechenet und es wird nach Punkten, Torverhältnis, geschossenen Toren sortiert
     * Sollte trotzdem die Bewertung uneindeutig sein wird ein Penaltyschießen zwischen den Teams veranstaltet
     */
    private function fixSpecialCases($erg,$i,$j, $gruppe=0){
        //relevante Spiele laden
        //echo "in fixSpecialCases: i $i, j $j <br>";
        $sql="SELECT * FROM spiele WHERE gruppe='$gruppe' AND (";

        for($m=$i;$m<$j;$m++){
            for($n=$i+1;$n<=$j;$n++){
                if($m>$i||$n>$i+1){
                  $sql=$sql . " OR ";
                }
                $team1=$erg[$m]["team_id"];
                $team2=$erg[$n]["team_id"];
                $sql=$sql . "((teamA_ID='$team1' AND teamB_ID='$team2') OR (teamA_ID='$team2' AND teamB_ID='$team1'))";
            }
        }
        $sql=$sql . ")";
        //echo "$sql";
        $result=db::readdb($sql);
        $vtour=0;
        for($n=$i;$n<=$j;$n++){
            //$vtour[$n]["team_id"]=erg[$n]["team_id"];
            //$vtour[$n]["value"]=0;
            //$vtour["$erg[$n]['team_id']"]=0;
            $erg[$n]["value"]=0;
        }

        while($data=mysqli_fetch_assoc($result)){

            $punktFactor=10000000;
            $tordiffFactor=1000;
            //Spiel für Spiel druchgehen
            $toreA=$data["toreA"]+$data["penaltyA"];
            $toreB=$data["toreB"]+$data["penaltyB"];
            $teamA=$data['teamA_ID'];
            $teamB=$data['teamB_ID'];
            //echo " Spiel: ".$teamA.": ".$teamB." Tore: ".$toreA." : ".$toreB." <br>";
            if($toreA>$toreB){
                $erg[$this->getErgIndex($teamA,$erg)]["value"]+=3*$punktFactor+($toreA-$toreB+100)*$tordiffFactor+$toreA;
                $erg[$this->getErgIndex($teamB,$erg)]["value"]+=0*$punktFactor+($toreB-$toreA+100)*$tordiffFactor+$toreB;
            }elseif ($toreA==$toreB){
                $erg[$this->getErgIndex($teamA,$erg)]["value"]+=1*$punktFactor+($toreA-$toreB+100)*$tordiffFactor+$toreA;
                $erg[$this->getErgIndex($teamB,$erg)]["value"]+=1*$punktFactor+($toreB-$toreA+100)*$tordiffFactor+$toreB;
            }else{
                $erg[$this->getErgIndex($teamA,$erg)]["value"]+=0*$punktFactor+($toreA-$toreB+100)*$tordiffFactor+$toreA;
                $erg[$this->getErgIndex($teamB,$erg)]["value"]+=3*$punktFactor+($toreB-$toreA+100)*$tordiffFactor+$toreB;
            }
        }//nachValue sortieren


        $unorderd=true;
        while($unorderd){
            $unorderd=false;
            for($n=$i;$n<$j;$n++){
                if($erg[$n]["value"]<$erg[$n+1]["value"]){
                    echo "tausche ".$erg[$n]["teamname"] ." mit ".$erg[$n+1]["teamname"];
                    //n und n+1 tauschen
                   $this->switchRowErg($erg,$n,$n+1);
                    $unorderd=true;
                }
            }
        }
        return $erg;

    }

    /**
     * Hilfsfunktion die zwei Teams mit ihren Daten in der erg Strucktur tauscht
     * @param $erg
     * @param $i
     * @param $j
     */
    private function switchRowErg($erg,$i,$j){
        $dummy["team_id"]=$erg[$i]["team_id"];
        $dummy[$i]["teamname"]=$erg[$i]["teamname"];
        $dummy["tore"]=$erg[$i]["tore"];
        $dummy["gegentore"]=$erg[$i]["gegentore"];
        $dummy["diff"]=$erg[$i]["diff"];
        $dummy["turnierPunkte"]=$erg[$i]["turnierPunkte"];
        $dummy["index"]=$erg[$i]["index"];
        $dummy["value"]=$erg[$i]["value"];

        $erg[$i]["team_id"]=$erg[$j]["team_id"];
        $erg[$i]["teamname"]=$erg[$j]["teamname"];
        $erg[$i]["tore"]=$erg[$j]["tore"];
        $erg[$i]["gegentore"]=$erg[$j]["gegentore"];
        $erg[$i]["diff"]=$erg[$j]["diff"];
        $erg[$i]["turnierPunkte"]=$erg[$j]["turnierPunkte"];
        $erg[$i]["index"]=$erg[$j]["index"];
        $erg[$i]["value"]=$erg[$j]["value"];

        $erg[$j]["team_id"]=$dummy["team_id"];
        $erg[$j]["teamname"]=$dummy["teamname"];
        $erg[$j]["tore"]=$dummy["tore"];
        $erg[$j]["gegentore"]=$dummy["gegentore"];
        $erg[$j]["diff"]=$dummy["diff"];
        $erg[$j]["turnierPunkte"]=$dummy["turnierPunkte"];
        $erg[$j]["index"]=$dummy["index"];
        $erg[$j]["value"]=$dummy["value"];
    }
    private function getErgIndex($teamid,$erg){
        for($i=0;$i<8;$i++){
            if($erg[$i]["team_id"]==$teamid) return $i;
        }
    }

    public function updateTore($spiel_index, $toreA,$toreB, $penA,$penB){
        if(isset($this->spiele[$spiel_index])){
            $spiel=new spiel($this->spiele[$spiel_index]);
            $toreA=$this->makeNumber($toreA);
            $toreB=$this->makeNumber($toreB);
            $penA=$this->makeNumber($penA);
            $penB=$this->makeNumber($penB);
            $spiel->update_All_Tore($toreA,$toreB, $penA,$penB);
        }
    }
    public function makeNumber($var){
        if(!is_numeric($var)){
            $var=-1;
        }
        return $var;
    }
    public function getZeit($index){
        if($this->plaetze<8||$this->spielplan=="dko"){
            $minuten=$index*($this->pause+$this->halbzeitPause+$this->spielZeit);
        }else{
            //8er Gruppe bleibt übrig
            if($index<12){
                $minuten=$index*($this->pause+$this->halbzeitPause+$this->spielZeit);
            }else{
                $minuten=12*($this->pause+$this->halbzeitPause+$this->spielZeit);
                $minuten+=($index-12)*35;

            }
        }
        $zeit=strtotime($this->startzeit ." 14 April 2014");
        $zeit=strtotime("+ $minuten Minutes", $zeit);
        //$zeit=date("H:i", strtotime($startzeit) + $minuten * 60);
        return date("H:i",$zeit);
        }

    public function penaltyWarning(){//TODO TESTEN mehr als zwei gleich bepunktete Teams
        //echo "Penalty Warning in Outputfunktion". $this->penWarning." <br>";
        return $this->penWarning;
    }
    public function getNecessaryPenalties(){
        return $this->penaltyNecessary;
    }

    /**
     * @return mixed|string
     */
    public function getSpielplan()
    {
        return $this->spielplan;
    }

    /**
     * liefert das unterste Spiel zurück dem bereits Tore zugewiesen worden sind
     * also evtl. nicht das tatsächlich aktive, besonders wenn Spiele getauscht worden sind
     */
    public function getActiveGame(){
        $sql="SELECT spiel_Index FROM spiele WHERE toreA!='-1' ORDER BY spiel_Index DESC LIMIT 1"; //todo and turnier_ID = turnier_ID
        $result=db::readdb($sql);
        $result=mysqli_fetch_assoc($result);
        $index=0;
        if(!empty($result["spiel_Index"])){
            $index=$result["spiel_Index"];//TODO Wenn aktuelles Spiel 0:0 ausgeht wird es nicht in die LiveTabelle eingearbeitet
        }
        return $index;
    }
    public function getDefaultTeams($index){
        return $this->defaultTeams[$index];
    }

}
