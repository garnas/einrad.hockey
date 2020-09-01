<?php
//TODO
//+2 Vergleich alles gleich nach Penalty sortieren !!Fertig
//plaetze != anzahl teams !!FERTIG
//layout formular zum eintragen !!Ferig
//8 er Gruppe 
//4 er Spielplan Pause
//keine Spiele eingetragen Fehler in Zeile 65, keine rangtabelle möglich da alles NULL !! FERTIG
//testen, testen, testen
//ergebnisse in datenbank speichern, datum dabei testen !! Fertig außer DAtum test
//cronjob dienstags spielplan erstellen -> ansgar 
class Spielplan{
    public $turnier_id;
    public $akt_turnier;
    public $teamliste;
    public $penalty_warning="";
    public $anzahl_teams;
    public $anzahl_spiele;
    //public $datum;
    //public $ort;

    function __construct($turnier_id)
    {
        $this->turnier_id = $turnier_id;
        $this->akt_turnier = new Turnier($turnier_id);
        $this->teamliste = $this->akt_turnier->get_liste_spielplan();
        $this->anzahl_teams=sizeof($this->teamliste);
        //$this->getOrtDatum();
    }

    function create_spielplan_jgj(){
        //TESTEN OB SPIELE SCHON EXISTIEREN
        $sql="SELECT * FROM spiele WHERE turnier_id=$this->turnier_id";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        $this->anzahl_spiele=0;
        if (empty($result)){
            //db::debug($this->akt_turnier->daten);
            //db::debug($this->teamliste);
            //$plaetze = $this->akt_turnier->daten["plaetze"];
            $spielplan=$this->akt_turnier->daten["spielplan"];
            $sql = "SELECT * FROM spielplan_paarungen WHERE plaetze='$this->anzahl_teams' AND spielplan='$spielplan'";
            $result = db::readdb($sql);
            
            $sqlinsert="";
            $sqlinsert="INSERT INTO spiele VALUES ";
            while($spiel=mysqli_fetch_assoc($result)){
                $sqlinsert= $sqlinsert."(" .$this->turnier_id.",". $spiel["spiel_id"].", " .
                $this->teamliste[intval($spiel["team_a"])]["team_id"].",".
                $this->teamliste[intval($spiel["team_b"])]["team_id"].",".
                $this->teamliste[intval($spiel["schiri_a"])]["team_id"].",".
                $this->teamliste[intval($spiel["schiri_b"])]["team_id"].", NULL,NULL,NULL,NULL),";
                $this->anzahl_spiele += 1;
            }
            $sqlinsert=rtrim($sqlinsert,",");
            $sqlinsert= $sqlinsert.";";
            db::writedb($sqlinsert);
        }else{
            //spiele zaehlen
            $sql="SELECT *FROM spiele WHERE turnier_id=$this->turnier_id";
            $result = db::readdb($sql);
            while($spiel=mysqli_fetch_assoc($result)){
                $this->anzahl_spiele += 1;
            }
            //echo "anzahl spiele ".$this->anzahl_spiele;
        }
    }
    function update_spiel($spiel_id, $tore_a,$tore_b,$penalty_a,$penalty_b){
        //echo "update Tore is penalty numeric?: ".$penalty_a."   ";
        if(!is_numeric($tore_a)){
            $tore_a='NULL';
        }
        if(!is_numeric($tore_b)){
            $tore_b='NULL';
        }
        if(!is_numeric($penalty_a)){
            $penalty_a='NULL';
        }
        if(!is_numeric($penalty_b)){
            $penalty_b='NULL';
        }
        if((is_numeric($tore_a)||$tore_a=="NULL")&&(is_numeric($tore_b)||$tore_b=="NULL")){
            $sql="UPDATE spiele SET tore_a=$tore_a, tore_b=$tore_b, penalty_a=$penalty_a, penalty_b=$penalty_b
            WHERE turnier_id=$this->turnier_id AND spiel_id=$spiel_id;";
            db::writedb($sql);
        }else{
            //Form::error("Bitte Zahlen eintragen");
            //header('Location : ../irgendeine/url.php');
            //die();
        }
       
    }

    function get_turnier_tabelle(){
        $teams=[];
        for($i=1 ;$i<=sizeof($this->teamliste);$i++){
            array_push($teams,$this->teamliste[$i]["team_id"]);
        }
        $daten=$this->sqlQuery(FALSE,$teams);
        //echo " size of daten ".sizeof($daten)."<br>";
        //Teamname hinzufügen
        for($i=0;$i<sizeof($daten);$i++){
            $daten[$i]["teamname"]=$this->getTeamnameByTeamID($daten[$i]["team_id_a"]);
        }
        //auf punktgleichheit testen und richtig sortieren
        //index und i werden gleichmäßig erhöht außer es wird punktgleichheit zwischen 
        //benachbareten Teams festgestellt, dann haben die Teams von index bis i (inklusiv) 
        //gleich viele Punkte und müssen in den Direktvergleich (sort_teams)
        $index=0;
        for ($i=0;$i<$this->anzahl_teams-1;$i++){
            if($daten[$i]["punkte"]==$daten[$i+1]["punkte"]){
                $index=$index;
            }elseif($i!=$index){
                 //sortiere teams index bis i
                //echo "sort teams ".$index." bis ".$i."<br>"; 
                $daten=$this->sort_teams($daten,$index,$i);
                
                $index=$i+1;
            }else{
                $index=$i+1;
            }
        }
        //letzte Teams unterscheiden
        if($i!=$index){
            //echo "sort teams letzte Teams ".$index." bis ".$i."<br>"; 
            $daten=$this->sort_teams($daten,$index,$i);
        }
        //Wertigkeit zuordnen
        $wert=0;
        for($i=$this->anzahl_teams-1;$i>=0;$i--){
            $wert=$this->getWertigkeitByTeamID($daten[$i]["team_id_a"]);
            $daten[$i]["wertigkeit"]=$wert;
        }
        //NL Wertigkeiten ausrechnen
        $daten=$this->setWertigkeitenNL($daten);
        //Ligapunkte ausrechenen Turnierergebnis
        $punkte=0;
        $faktor=$this->getFaktor();
        for($i=sizeof($daten)-1;$i>=0;$i--){
            $punkte += $daten[$i]["wertigkeit"];
            $daten[$i]["ligapunkte"]=round($punkte*(6/$faktor));
        }
        return db::escape($daten);
    }

    function setWertigkeitenNL($daten){
        //db::debug($this->teamliste);
        //letztes Team NL
        if(!is_numeric($daten[sizeof($daten)-1]["wertigkeit"])){
            $j=sizeof($daten)-2;
            while(!is_numeric($daten[$j]["wertigkeit"])&&$j>=0){
                $j--;
            }
            $lastNL=$daten[$j]["wertigkeit"]/2;
            if($lastNL<15){
                $lastNL=15;
            }
            $daten[sizeof($daten)-1]["wertigkeit"]=$lastNL;
            
        }
        $max_wertigkeit=$daten[sizeof($daten)-1]["wertigkeit"];
        for($i=sizeof($daten)-2;$i>=0;$i--){
            if(!is_numeric($daten[$i]["wertigkeit"])){
                $daten[$i]["wertigkeit"]=$max_wertigkeit+1;
            }elseif($daten[$i]["wertigkeit"]>$max_wertigkeit){
                $max_wertigkeit=$daten[$i]["wertigkeit"];
            }
        }
        return db::escape($daten);
    }


    function getFaktor(){
        return db::escape($this->getSpielzeiten()["faktor"]);
    }

    function getWertigkeitByTeamID($team_id){
        for($i=1;$i<=sizeof($this->teamliste);$i++){
            if($team_id==$this->teamliste[$i]["team_id"]){
                return db::escape($this->teamliste[$i]["wertigkeit"]);
            }
        }
    }

    function getTeamnameByTeamID($team_id){
        for($i=1;$i<=sizeof($this->teamliste);$i++){
            if($team_id==$this->teamliste[$i]["team_id"]){
                return db::escape($this->teamliste[$i]["teamname"]);
            }
        }
    }

    function sort_teams($daten,$begin,$end){
        //rufen sql auf mit sortierung nach punken, diff, geschossenen Toren
        //ergebnis testen ob alle gleich ->Penalty oder Teil gleich -> teilweiser direkter Vergleich
        //in daten reihen swapen
        $teams=[];
        foreach(range($begin,$end) as $number){
            array_push($teams, $daten[$number]["team_id_a"]);
        }
        $subdaten=$this->sqlQuery(TRUE,$teams);
        //Teamname hinzufügen
        for($i=0;$i<sizeof($subdaten);$i++){
            $subdaten[$i]["teamname"]=$this->getTeamnameByTeamID($subdaten[$i]["team_id_a"]);
        }
        //alle gleich
        $last=$end-$begin;
        //penalty zwischen allen nötig, evtl. schon stattgefunden
        //db::debug($subdaten);
        if($subdaten[0]["punkte"]==$subdaten[$last]["punkte"]&&
            $subdaten[0]["diff"]==$subdaten[$last]["diff"]&& 
            $subdaten[0]["tore"]==$subdaten[$last]["tore"]&&
            $subdaten[0]["penalty_points"]==$subdaten[$last]["penalty_points"]&&
            $subdaten[0]["penalty_diff"]==$subdaten[$last]["penalty_diff"]&&
            $subdaten[0]["penaltytore"]==$subdaten[$last]["penaltytore"]){
            $this->penalty_warning .="Achtung Penalty zwischen";
            for($i=0;$i<$end-$begin;$i++){
                $this->penalty_warning .=" ".$subdaten[$i]["teamname"]." und";
            }
            $this->penalty_warning .=" ".$subdaten[$end-$begin]["teamname"]."! <br>";
            //echo "<br> Achtung Penalty!!! zwischen".$begin." bis ".$end." <br>";
        }else{
            $index=0;
            //db::debug($daten);
            for($i=0;$i<$end-$begin+1;$i++){
                //echo "subdaten: ".$i.". tes Team  ".$subdaten[$i]["team_id_a"]."<br>";
            }
            for($i=0;$i<$end-$begin;$i++){
                //echo "subdaten: ".$i.". tes Team  ".$subdaten[$i]["team_id_a"];
                //testen ob aktuelle Zeile gleich zur nächsten 
                //bei ungleichheit daten swapen
                //bei gleichheit erneuter direkter vergleich
                if($subdaten[$i]["punkte"]==$subdaten[$i+1]["punkte"]&&
                     $subdaten[$i]["diff"]==$subdaten[$i+1]["diff"]&& 
                     $subdaten[$i]["tore"]==$subdaten[$i+1]["tore"]&&
                     $subdaten[$i]["penalty_points"]==$subdaten[$i+1]["penalty_points"]&&
                     $subdaten[$i]["penalty_diff"]==$subdaten[$i+1]["penalty_diff"]&&
                     $subdaten[$i]["penaltytore"]==$subdaten[$i+1]["penaltytore"]){
                    $index=$index;
                }elseif($i!=$index){
                    //echo "<br> in Vergleich Gleichheit <br>";
                    //im direktvergleich ist wieder Gleichheit aufgetreten -> erneuter direkter Vergleich
                    //so drehen dass die gleichen Teams an der richtigen Stelle im sub array stehen
                    for($j=0;$j<$i-$index+1;$j++){
                        //echo "<br> tauschen <br>";
                        $in=$this->getDatenIndexByTeamID($daten,$subdaten[$index+$j]["team_id_a"]);
                        $ex=$index+$j;
                        $temp=$daten[$ex];
                        $daten[$ex]=$daten[$in];
                        $daten[$in]=$temp;
                    }
                    $daten=$this->sort_teams($daten,$index,$i);
                    $index=$i+1;
                }else{
                    //aktueller Wert(Punkte, Diff, geschossene Tore) ist anders als Wert davor und danach
                    $index=$index+1;
                    //swapen
                    
                    //nur tauschen wenn Team noch nicht am richtigen Platz
                    $in=$this->getDatenIndexByTeamID($daten,$subdaten[$i]["team_id_a"]);
                    if($in!=$begin+$i){
                        //echo "<br> swap ".$subdaten[$i]["team_id_a"]." mit ".$subdaten[$i+1]["team_id_a"]."<br>";
                        $ex=$begin+$i;
                        $temp=$daten[$ex];
                        $daten[$ex]=$daten[$in];
                        $daten[$in]=$temp;
                    }else{
                        //echo "<br>  KEIN swap ".$subdaten[$i]["team_id_a"]." mit ".$subdaten[$i+1]["team_id_a"]."<br>";
                    }
                    
                    //db::debug($daten);
                } 
            }
            //evtl letzten Gleich
            if($i!=$index){ //TODO zaehlt php for nach ende noch eins weiter -> Ja
                $daten=$this->sort_teams($daten,$index,$i);
            }else{
                //swapen, muesste schon geswappt sein
            }
            
        }
        return db::escape($daten);
        
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
        $where=$where_a.") AND ".$where_b.") AND turnier_id='$this->turnier_id'";
        $order=" `punkte`  DESC";
        if($isDirektVergleich){
            $order .=", diff DESC, tore DESC, penalty_points DESC, penalty_diff DESC, penaltytore DESC";
        }else{
            $order .=", penaltytore DESC";
        }
        $sql="
        SELECT team_id_a, SUM(games) AS spiele,  COALESCE(SUM(points),0) AS punkte,
        COALESCE(SUM(tore),0) as tore,  COALESCE(SUM(gegentore),0) as gegentore,
        COALESCE((SUM(tore) - SUM(gegentore)),0) as diff, COALESCE(SUM(penalty_points),0) as penalty_points,
        COALESCE(SUM(penaltytore),0) as penaltytore, COALESCE(SUM(penaltygegentore),0) as penaltygegentore,
        COALESCE((SUM(penaltytore) - SUM(penaltygegentore)),0) as penalty_diff
        FROM(
            SELECT team_id_a,
                CASE 
                    WHEN `tore_a`> `tore_b` THEN 3
                    WHEN `tore_a` = `tore_b` THEN 1
                    ELSE 0
                END AS points,
                CASE
		            WHEN `tore_a` IS NULL THEN 0
		            ELSE 1
                END AS games,
                CASE 
                    WHEN `penalty_a`>`penalty_b` THEN 3
                    WHEN `penalty_a`=`penalty_b` THEN 0
                    ELSE 0
		        END AS penalty_points, tore_a as tore, tore_b as gegentore, penalty_a AS penaltytore, penalty_b as penaltygegentore
            FROM spiele
            WHERE".$where."
           
            UNION ALL
        
            SELECT team_id_b,
                CASE 
                    WHEN `tore_a` < `tore_b` THEN 3
                    WHEN `tore_a` = `tore_b` THEN 1
                    ELSE 0
                END AS points,
                CASE
		            WHEN `tore_a` IS NULL THEN 0
		            ELSE 1
                END AS games,
                CASE 
                    WHEN `penalty_a`<`penalty_b` THEN 3
                    WHEN `penalty_a`=`penalty_b` THEN 0
                    ELSE 0
		        END AS penalty_points, tore_b as tore, tore_a as gegentore, penalty_b AS penaltytore, penalty_a as penaltygegentore
            FROM spiele
            WHERE ".$where."
            ) AS t
        GROUP BY team_id_a
        ORDER BY".$order;
        $result = db::readdb($sql);
        ////echo $sql;
        $daten=[];
        while($row=mysqli_fetch_assoc($result)){
            //////echo $row["team_id_a"]." ".$row["punkte"]." ".$row["tore"]." ".$row["gegentore"]." ".$row["diff"]." ".$row["penaltytore"];
            array_push($daten, $row);
        }
        return db::escape($daten);
    }

    function get_spiele(){
        $sql="
        SELECT spiel_id, t1.teamname AS team_a_name, t2.teamname AS team_b_name, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b
        FROM spiele sp, teams_liga t1, teams_liga t2
        WHERE turnier_id='$this->turnier_id'
        AND team_id_a = t1.team_id
        AND team_id_b = t2.team_id
        ORDER BY spiel_id ASC
        ";
        $result=db::readdb($sql);
        $daten=[];
        $startzeit=new DateTime($this->akt_turnier->daten["startzeit"]);
        $zeiten=$this->getSpielzeiten();
        //db::debug($zeiten)
        $min=$zeiten["anzahl_halbzeiten"]*$zeiten["halbzeit_laenge"]+$zeiten["pause"];
        $startzeit->sub(date_interval_create_from_date_string($min.' minutes'));
        
        $var=1;
        while($spiel=mysqli_fetch_assoc($result)){
            //4er Spielplan Pause nach geraden Spiel
            if($this->anzahl_teams==4 && $var>2&&($var%2)==1){
                $extra_pause=$zeiten["halbzeit_laenge"]+$zeiten["pause"];
                $startzeit->add(date_interval_create_from_date_string($extra_pause.' minutes'));
            }
            $spiel["zeit"]=$startzeit->add(date_interval_create_from_date_string($min.' minutes'))->format("H:i");
            array_push($daten, $spiel);
            $var += 1;
        }
        return db::escape($daten);
    }
    

    function getSpielzeiten(){
        $plaetze = $this->anzahl_teams;
        $spielplan=$this->akt_turnier->daten["spielplan"];
        $sql="SELECT * FROM spielplan_details WHERE plaetze='$plaetze' AND spielplan='$spielplan'";
        $result=db::readdb($sql);
        return db::escape(mysqli_fetch_assoc($result));
    }

    function get_anzahl_spiele(){
        return db::escape($this->anzahl_spiele);
    }

    //$daten sollten schon sortiert sein!
    function set_ergebnis($daten){
        //Sind alle Spiele gespielt und kein Penalty offen
        $sql="
        SELECT spiel_id, t1.teamname AS team_a_name, t2.teamname AS team_b_name, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b
        FROM spiele sp, teams_liga t1, teams_liga t2
        WHERE turnier_id='$this->turnier_id'
        AND team_id_a = t1.team_id
        AND team_id_b = t2.team_id
        AND (tore_a IS NULL OR tore_b IS NULL)
        ORDER BY spiel_id ASC
        ";
        $result=db::readdb($sql);
        $result=mysqli_fetch_assoc($result);
        if(empty($result)&&empty($this->penalty_warning)){
            //Testen ob Turnier eingetragen werden darf
            if (!Tabelle::check_ergebnis_eintragbar($this->akt_turnier)){
                Form::error("Turnierergebnis konnte nicht eingetragen werden. Kontaktiere bitte den Ligaausschuss.");
                header("Location: ../liga/kontakt.php");
                die();
            }else{
                $this->akt_turnier->set_phase('ergebnis');
                $this->akt_turnier->delete_ergebnis();
                foreach($daten as $index=>$date){
                    $this->akt_turnier->set_ergebnis($date["team_id_a"], $date["ligapunkte"], $index+1);
                    //echo " Turnierergenis ".$date["team_id_a"]." ".$date["teamname"]." -> ".$date["ligapunkte"]." Platz ".($index+1)."<br>";
                }
                Form::affirm("Turnierergebnisse wurden eingetragen");
            }
        }else{
            Form::error("Es sind noch Spiele oder Penaltyergebnisse offen");
        }
        
    }

    /*function getOrtDatum(){
        $sql = "SELECT ort FROM turniere_details WHERE turnier_id='$this->turnier_id'";
        $result=db::readdb($sql);
        $this->ort=mysqli_fetch_assoc($result)["ort"];
        
        $sql = "SELECT datum FROM turniere_liga WHERE turnier_id='$this->turnier_id'";
        $result=db::readdb($sql);
        $result=mysqli_fetch_assoc($result)["datum"];
        $datum=new DateTime($this->akt_turnier->daten["startzeit"]);
        $this->datum=$datum->format("j.n.o");
    }*/

    //Löscht einen Spielplan, falls ein manueller Spielplan hochgeladen werden muss.
    public static function delete_spielplan($turnier_id){ 
        db::writedb("DELETE FROM spiele WHERE turnier_id = '$turnier_id'");
    }
}

