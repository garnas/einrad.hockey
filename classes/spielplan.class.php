<?php 
class spielplan{
    public $turnier_id;
    public $akt_turnier;
    public $teamliste;
    public $penalty_warning="";


    function __construct($turnier_id)
    {
        $this->turnier_id = $turnier_id;
        $this->akt_turnier = new Turnier($turnier_id);
        $this->teamliste = $this->akt_turnier->get_liste_spielplan();
    }

    function create_spielplan_jgj(){
        //TESTEN OB SPIELE SCHON EXISTIEREN
        $sql="SELECT *FROM spiele WHERE turnier_id=$this->turnier_id";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        if (empty($result)){
            db::debug($this->akt_turnier->daten);
            $plaetze = $this->akt_turnier->daten["plaetze"];
            $spielplan=$this->akt_turnier->daten["spielplan"];
            $sql = "SELECT * FROM spielplan_paarungen WHERE plaetze='$plaetze' AND spielplan='$spielplan'";
            $result = db::readdb($sql);
            $sqlinsert="";
            $sqlinsert="INSERT INTO spiele VALUES ";
            while($spiel=mysqli_fetch_assoc($result)){
                $sqlinsert= $sqlinsert."(" .$this->turnier_id.",". $spiel["spiel_id"].", " .
                $this->teamliste[intval($spiel["team_a"])]["team_id"].",".
                $this->teamliste[intval($spiel["team_b"])]["team_id"].",".
                $this->teamliste[intval($spiel["schiri_a"])]["team_id"].",".
                $this->teamliste[intval($spiel["schiri_b"])]["team_id"].", NULL,NULL,NULL,NULL),";
            }
            $sqlinsert=rtrim($sqlinsert,",");
            $sqlinsert= $sqlinsert.";";
            db::writedb($sqlinsert);
           
        }
    }
    function update_spiel($spiel_id, $tore_a,$tore_b,$penalty_a,$penalty_b){
        if(!is_numeric($penalty_a)){
            $sql="UPDATE spiele SET tore_a=$tore_a, tore_b=$tore_b
        WHERE turnier_id=$this->turnier_id AND spiel_id=$spiel_id;";
        }else{
            $sql="UPDATE spiele SET tore_a=$tore_a, tore_b=$tore_b, penalty_a=$penalty_a, penalty_b=$penalty_b
        WHERE turnier_id=$this->turnier_id AND spiel_id=$spiel_id;";
        }
        db::writedb($sql);
    }

    function get_turnier_tabelle(){
        $teams=[];
        for($i=1 ;$i<=sizeof($this->teamliste);$i++){
            array_push($teams,$this->teamliste[$i]["team_id"]);
        }
        $daten=$this->sqlQuery(FALSE,$teams);
        //auf punktgleichheit testen und richtig sortieren
        $index=0;
        for ($i=0;$i<$this->akt_turnier->daten["plaetze"]-1;$i++){
            if($daten[$i]["punkte"]==$daten[$i+1]["punkte"]){
                $index=$index;
            }elseif($i!=$index){
                 //sortiere teams index bis i
                $daten=$this->sort_teams($daten,$index,$i);

                 $index=$i+1;
            }else{
                $index=$i+1;
            }
        }
        return $daten;
    }
    function sort_teams($daten,$begin,$end){
        //rufen sql auf mit sortierung nach punken, diff, geschossenen Toren
        //ergebnis testen ob alle gleich ->Penalty oder Teil gleich -> teilweiser direkter Vergleich
        //in daten reihen swapen
        $teams=[];
        db::debug($daten);
        foreach(range($begin,$end) as $number){
            array_push($teams, $daten[$number]["team_id_a"]);
        }
        db::debug($teams);
        $subdaten=$this->sqlQuery(TRUE,$teams);
        //alle gleich
        $last=$end-$begin;
        db::debug($subdaten);
        echo "----------------------last".$last;
        if($subdaten[0]["punkte"]==$subdaten[$last]["punkte"]&& $subdaten[0]["diff"]==$subdaten[$last]["diff"]&& $subdaten[0]["tore"]==$subdaten[$last]["tore"]){
            //penalty zwischen allen nötig
            $this->penalty_warning="Achtung Penalty";
        }else{
            $index=0;
            for($i=0;$i<$end-$begin;$i++){
                //testen ob aktuelle Zeile gleich zur nächsten 
                //bei ungleichheit daten swapen
                //bei gleichheit erneuter direkter vergleich
                if($subdaten[$i]["punkte"]==$subdaten[$i+1]["punkte"]&& $subdaten[$i]["diff"]==$subdaten[$i+1]["diff"]&& $subdaten[$i]["tore"]==$subdaten[$i+1]["tore"]){
                    $index=$index;
                }elseif($i!=$index){
                    $daten=$this->sort_teams($daten,$index,$i);
                    $index=$i+1;
                }else{
                    $index=$index+1;
                    //swapen
                    $ex=$this->getDatenIndexByTeamID($daten,$subdaten[$i+1]["team_id_a"]);
                    $in=$this->getDatenIndexByTeamID($daten,$subdaten[$i]["team_id_a"]);
                    $temp=$daten[$ex];
                    $daten[$ex]=$daten[$in];
                    $daten[$in]=$temp;
                } 
            }
            //evtl letzten Gleich
            if($i!=$index){ //TODO zaehlt php for nach ende noch eins weiter -> Ja
                $daten=$this->sort_teams($daten,$index,$i);
            }else{
                //swapen, muesste schon geswappt sein
            }
            return $daten;
        }
        
    }

    function getDatenIndexByTeamID($daten, $team_id){
        for($i=0;$i<sizeof($daten);$i++){
            if($daten[$i]["team_id_a"]==$team_id){
                return $i;
            }
        }
    }


    function sqlQuery($isDirektVergleich, $active_teams){
        $where_a= "(";
        $where_b="(";
        foreach ($active_teams as $team){
            $where_a .=" team_id_a ='$team' OR";
            $where_b .=" team_id_b ='$team' OR";
        }
        $where_a=rtrim($where_a,"OR");
        $where_b=rtrim($where_b,"OR");
        $where=$where_a.") AND ".$where_b.")";
        $order=" `punkte`  DESC";
        if($isDirektVergleich){
            $order .=", diff DESC, tore DESC";
        }else{
            $order .=", penaltytore DESC";
        }
        $sql="
        SELECT team_id_a, COUNT(team_id_a) AS spiele, SUM(points) AS punkte, SUM(tore) as tore, SUM(gegentore) as gegentore, (SUM(tore) - SUM(gegentore)) as diff, SUM(penaltytore) as penaltytore
        FROM(
            SELECT team_id_a,
                CASE 
                    WHEN `tore_a`> `tore_b` THEN 3
                    WHEN `tore_a` = `tore_b` THEN 1
                    ELSE 0
                END AS points, tore_a as tore, tore_b as gegentore, penalty_a AS penaltytore
            FROM spiele
            WHERE".$where."
            AND tore_a IS NOT NULL
            AND tore_b IS NOT NULL
        
            UNION ALL
        
            SELECT team_id_b,
                CASE 
                    WHEN `tore_a` < `tore_b` THEN 3
                    WHEN `tore_a` = `tore_b` THEN 1
                    ELSE 0
                END AS points, tore_b as tore, tore_a as gegentore, penalty_b AS penaltytore
            FROM spiele
            WHERE ".$where."
            AND tore_a IS NOT NULL
            AND tore_b IS NOT NULL
            ) AS t
        GROUP BY team_id_a
        ORDER BY".$order;
        $result = db::readdb($sql);
        echo $sql;
        $daten=[];
        while($row=mysqli_fetch_assoc($result)){
            //echo $row["team_id_a"]." ".$row["punkte"]." ".$row["tore"]." ".$row["gegentore"]." ".$row["diff"]." ".$row["penaltytore"];
            array_push($daten, $row);
        }
        return $daten;
    }

}