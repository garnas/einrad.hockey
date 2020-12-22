<?php
//TODO
//+2 Vergleich alles gleich nach Penalty sortieren !!Fertig
//plaetze != anzahl teams !!FERTIG
//layout formular zum eintragen !!Ferig
//8 er Gruppe
//4 er Spielplan Pause
//keine Spiele eingetragen Fehler in Zeile 65, keine rangtabelle möglich da alles NULL !! FERTIG
//testen, testen, testen
//ergebnisse in datenbank speichern, datum dabei testen !! Fertig
//cronjob dienstags spielplan erstellen -> ansgar
class SpielplanAlt{
    public $turnier_id;
    public $akt_turnier;
    public $teamliste;
    public $penalty_warning = '';
    public $anzahl_teams;
    public $anzahl_spiele;

    function __construct($turnier_id)
    {

        $this->turnier_id = $turnier_id;
        $this->akt_turnier = new Turnier($turnier_id);
        $this->teamliste = array_combine(range(1, count($this->akt_turnier->get_liste_spielplan())), array_values($this->akt_turnier->get_liste_spielplan()));
        $this->anzahl_teams=sizeof($this->teamliste);
    }

    //Funktion gibt false zurück, wenn beim direkten Vergleich manche Teams noch nicht alle Spiele gespielt haben, sonst true.
    function check_penalty_warning($subdaten){
        $turnier_id = $this->turnier_id;
        foreach ($subdaten as $key => $team_ergebnis){
            $team_id = $team_ergebnis['team_id_a'];
            $sql = "SELECT * FROM spiele WHERE turnier_id = '$turnier_id' AND (team_id_a = '$team_id' or team_id_b = '$team_id')";
            $result = db::read($sql);
            while ($x = mysqli_fetch_assoc($result)){
                if ($x['tore_a'] === NULL or $x['tore_b'] === NULL){
                    return false;
                }
            }
        }
        return true;
    }

    //Check, ob alle regulären Spiele beendet worden sind
    function check_alles_gespielt($daten){
        foreach ($daten as $team){
            if ($team['spiele'] < $this->anzahl_teams - 1){
                return false;
            }
        }
        return true;
    }

    //Check, ob jedes Team ein Team gespielt hat. Wenn ja wird die Platzierung und das Turnierergebnis im Template angezeigt
    function check_tabelle_einblenden($daten){
        foreach ($daten as $team){
            if ($team['spiele'] < 1){
                return false;
            }
        }
        return true;
    }

    function create_spielplan_jgj()
    {
        //TESTEN OB SPIELE SCHON EXISTIEREN
        $sql="SELECT * FROM spiele WHERE turnier_id = '$this->turnier_id'";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        $this->anzahl_spiele = 0;

        if (empty($result)){
            $spielplan=$this->akt_turnier->details["spielplan"];
            $sql = "SELECT * FROM spielplan_paarungen WHERE plaetze = '$this->anzahl_teams' AND spielplan = '$spielplan'";
            $result = db::read($sql);
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
            db::write($sqlinsert);
            $this->akt_turnier->log("Spielplan wurde in die Datenbank eingetragen.","automatisch");
        }else{
            //spiele zaehlen
            $sql="SELECT * FROM spiele WHERE turnier_id=$this->turnier_id";
            $result = db::read($sql);
            while($spiel=mysqli_fetch_assoc($result)){
                $this->anzahl_spiele += 1;
            }
        }
    }

    function update_spiel($spiel_id, $tore_a,$tore_b,$penalty_a,$penalty_b){
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
            db::write($sql);
        }
    }

    function get_turnier_tabelle(){
        $teams=[];
        for($i=1 ;$i<=sizeof($this->teamliste);$i++){
            array_push($teams,$this->teamliste[$i]["team_id"]);
        }
        $daten=$this->sqlQuery(FALSE,$teams);
        //Teamname hinzufügen
        for($i=0;$i<sizeof($daten);$i++){
            $daten[$i]["teamname"]=$this->getTeamnameByTeamID($daten[$i]["team_id_a"]);
        }
        //auf punktgleichheit testen und richtig sortieren
        //index und i werden gleichmäßig erhöht außer es wird punktgleichheit zwischen
        //benachbareten Teams festgestellt, dann haben die Teams von index bis i (inklusiv)
        //gleich viele Punkte und müssen in den Direktvergleich (sort_teams)
        $index=0;
        ////db::debug($get_teamdaten);
        for ($i=0;$i<$this->anzahl_teams-1;$i++){
            if($daten[$i]["punkte"]==$daten[$i+1]["punkte"]){
                $index=$index;
            }elseif($i!=$index){
                //sortiere teams index bis i
                //echo "sort Teams von ".$index." bis ".$i." <br>";
                $daten=$this->sort_teams($daten,$index,$i);

                $index=$i+1;
            }else{
                $index=$i+1;
            }
        }
        //letzte Teams unterscheiden
        if($i!=$index){
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
        //Ligapunkte ausrechnen Turnierergebnis
        $punkte=0;
        $faktor=$this->getFaktor();
        for($i=sizeof($daten)-1;$i>=0;$i--){
            $punkte += round($daten[$i]["wertigkeit"]); //round() von Ansgar hinzugefügt
            $daten[$i]["ligapunkte"]=round($punkte*(6/$faktor));
        }
        return db::escape($daten);
    }

    function setWertigkeitenNL($daten){
        //letztes Team NL
        if(!is_numeric($daten[sizeof($daten)-1]["wertigkeit"])){
            $j=sizeof($daten)-2;
            while(!is_numeric($daten[$j]["wertigkeit"])&&$j>=0){
                $j--;
            }
            $lastNL = round($daten[$j]["wertigkeit"] / 2); //Hinzugefügt von Ansgar
            $daten[sizeof($daten)-1]["wertigkeit"] = max (15, $lastNL);
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
        //in get_teamdaten reihen swapen
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
        if( $subdaten[0]["punkte"]==$subdaten[$last]["punkte"]&&
            $subdaten[0]["diff"]==$subdaten[$last]["diff"]&&
            $subdaten[0]["tore"]==$subdaten[$last]["tore"]&&
            $subdaten[0]["penalty_points"]==$subdaten[$last]["penalty_points"]&&
            $subdaten[0]["penalty_diff"]==$subdaten[$last]["penalty_diff"]&&
            $subdaten[0]["penaltytore"]==$subdaten[$last]["penaltytore"]){
            //Penalty-Hinweis nur anzeigen, wenn relevant für die Teams
            if ($this->check_penalty_warning($subdaten)){
                if (!$this->check_alles_gespielt($daten)){
                    $this->penalty_warning .= "Es könnte ein Penalty-Schießen geben - dies wird zum Ende des Turniers sicher angegeben.<br><br>Mögliches Penalty-Schießen zwischen ";
                }else{
                    $this->penalty_warning .= "<b>Penalty-Schießen</b> zwischen";
                }
                for($i=0;$i<$end-$begin;$i++){
                    $this->penalty_warning .=" <b>".$subdaten[$i]["teamname"]."</b> und";
                }
                $this->penalty_warning .=" <b>".$subdaten[$end-$begin]["teamname"]."</b>!<br><br>";
            }
        }else{
            $index=0;
            for($i=0;$i<$end-$begin;$i++){
                //testen ob aktuelle Zeile gleich zur nächsten
                //bei ungleichheit get_teamdaten swapen
                //bei gleichheit erneuter direkter vergleich
                if($subdaten[$i]["punkte"]==$subdaten[$i+1]["punkte"]&&
                    $subdaten[$i]["diff"]==$subdaten[$i+1]["diff"]&&
                    $subdaten[$i]["tore"]==$subdaten[$i+1]["tore"]&&
                    $subdaten[$i]["penalty_points"]==$subdaten[$i+1]["penalty_points"]&&
                    $subdaten[$i]["penalty_diff"]==$subdaten[$i+1]["penalty_diff"]&&
                    $subdaten[$i]["penaltytore"]==$subdaten[$i+1]["penaltytore"]){
                    $index=$index;
                    //echo "sort teams in for schleife case: Gleichwertige Teams <br>";
                }elseif($i!=$index){
                    //echo "sort teams in for schleife case: Gleichwertige Teams ueber aktuellem Team(inklusive) <br>";
                    //im direktvergleich ist wieder Gleichheit aufgetreten -> erneuter direkter Vergleich
                    //so drehen dass die gleichen Teams an der richtigen Stelle im sub array stehen
                    //echo "sort teams <br> im direktvergleich ist wieder Gleichheit aufgetreten <br>";
                    for($j=0;$j<$i-$index+1;$j++){
                        //echo "<br> tauschen <br>";
                        $in=$this->getDatenIndexByTeamID($daten,$subdaten[$index+$j]["team_id_a"]);
                        $ex=$index+$j+$begin;
                        $temp=$daten[$ex];
                        $daten[$ex]=$daten[$in];
                        $daten[$in]=$temp;
                    }
                    //echo "sort teams ".$get_teamdaten[$index]["teamname"]." und ".$get_teamdaten[$i]["teamname"];
                    $daten=$this->sort_teams($daten,$index+$begin,$i+$begin);
                    $index=$i+1;
                }else{
                    //echo "sort teams in for schleife case: nicht Gleichwertige Teams <br>";
                    //aktueller Wert(Punkte, Diff, geschossene Tore) ist anders als Wert davor und danach
                    $index=$index+1;
                    //swapen
                    //nur tauschen wenn Team noch nicht am richtigen Platz
                    $in=$this->getDatenIndexByTeamID($daten,$subdaten[$i]["team_id_a"]);
                    if($in!=$begin+$i){
                        $ex=$begin+$i;
                        $temp=$daten[$ex];
                        $daten[$ex]=$daten[$in];
                        $daten[$in]=$temp;
                    }
                }
            }
            //evtl letzten Gleich
            //echo "sort teams - Testen ob letzte Teams gleiche Punktzahl haben<br>";
            //echo "i: ".$i."  index: ".$index."<br>";
            if($i!=$index){ //TODO zaehlt php for nach ende noch eins weiter -> Ja
                //echo "sort teams <br> im direktvergleich ist wieder Gleichheit aufgetreten <br>";
                //echo "sort teams ".$get_teamdaten[$index+$begin]["teamname"]." und ".$get_teamdaten[$i+$begin]["teamname"];
                $daten=$this->sort_teams($daten,$index+$begin,$i+$begin);

            }
        }
        return db::escape($daten);
    }

    function getDatenIndexByTeamID($daten, $team_id)
    {
        for($i=0;$i<sizeof($daten);$i++){
            if($daten[$i]["team_id_a"]==$team_id){
                return $i;
            }
        }
    }

    function sqlQuery($isDirektVergleich, $active_teams)
    {
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
        $result = db::read($sql);
        ////echo $sql;
        $daten=[];
        while($row=mysqli_fetch_assoc($result)){
            //////echo $row["team_id_a"]." ".$row["punkte"]." ".$row["tore"]." ".$row["gegentore"]." ".$row["diff"]." ".$row["penaltytore"];
            array_push($daten, $row);
        }
        return db::escape($daten);
    }

    function get_spiele()
    {
        $sql=
            "SELECT spiel_id, t1.teamname AS teamname_a, t2.teamname AS teamname_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b
            FROM spiele sp, teams_liga t1, teams_liga t2
            WHERE turnier_id='$this->turnier_id'
            AND team_id_a = t1.team_id
            AND team_id_b = t2.team_id
            ORDER BY spiel_id ASC";
        $result=db::read($sql);
        $daten=[];
        $startzeit=new DateTime($this->akt_turnier->details["startzeit"]);
        $zeiten=$this->getSpielzeiten();
        //////db::debug($zeiten)
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

    function getSpielzeiten()
    {
        $plaetze = $this->anzahl_teams;
        $spielplan = $this->akt_turnier->details["spielplan"];
        $sql = "SELECT * FROM spielplan_details WHERE plaetze = '$plaetze' AND spielplan = '$spielplan'";
        $result = db::read($sql);
        return db::escape(mysqli_fetch_assoc($result));
    }

    function get_anzahl_spiele()
    {
        return db::escape($this->anzahl_spiele);
    }

    //$get_teamdaten sollten schon sortiert sein!
    function set_ergebnis($daten)
    {
        //Sind alle Spiele gespielt und kein Penalty offen
        $sql =
            "SELECT spiel_id, t1.teamname AS teamname_a, t2.teamname AS teamname_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b
            FROM spiele sp, teams_liga t1, teams_liga t2
            WHERE turnier_id='$this->turnier_id'
            AND team_id_a = t1.team_id
            AND team_id_b = t2.team_id
            AND (tore_a IS NULL OR tore_b IS NULL)
            ORDER BY spiel_id ASC";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        if(empty($result) && empty($this->penalty_warning) && $this->check_alles_gespielt($daten)){
            //Testen ob Turnier eingetragen werden darf
            if (!Tabelle::check_ergebnis_eintragbar($this->akt_turnier)){
                Form::error("Turnierergebnis konnte nicht eingetragen werden. Kontaktiere bitte den Ligaausschuss.");
                header("Location: " . db::escape($_SERVER['REQUEST_URI']));
                die();
            }else{
                $this->akt_turnier->set_phase('ergebnis');
                $this->akt_turnier->delete_ergebnis();
                foreach($daten as $index=>$date){
                    $this->akt_turnier->set_ergebnis($date["team_id_a"], $date["ligapunkte"], $index + 1);
                }
                Form::affirm("Das Turnierergebnis wurde dem Ligaausschuss übermittelt und wird jetzt in den Ligatabellen angezeigt.");
            }
        }else{
            Form::error("Es sind noch Spiel- oder Penaltyergebnisse offen. Turnierergebnisse wurden nicht übermittelt.");
        }
    }

    //Löscht einen Spielplan, falls ein manueller Spielplan hochgeladen werden muss.
    public static function delete_spielplan($turnier_id)
    {
        db::write("DELETE FROM spiele WHERE turnier_id = '$turnier_id'");
    }
}

