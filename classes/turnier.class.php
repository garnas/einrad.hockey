<?php

class Turnier {

    public $turnier_id;
    function __construct($turnier_id)
    {
        $this->turnier_id = $turnier_id;
        $this->daten = $this->get_turnier_details();
    }

    //Erstellt ein neues Turnier in der Datenbank
    public static function create_turnier($tname, $ausrichter, $startzeit, $besprechung = "Nein", 
        $art, $tblock, $fixed="Nein", $datum ,$plaetze, $spielplan, $hallenname, $strasse, $plz, $ort, $haltestellen = '', $hinweis,
        $startgebuehr, $name, $handy, $phase)
    {
        $saison = Config::SAISON;
        $turnier_id = db::get_auto_increment('turniere_liga');
        $sql= "INSERT INTO turniere_liga (tname, ausrichter, art, tblock, tblock_fixed, datum, phase, saison) 
                    VALUES ('$tname','$ausrichter','$art','$tblock', '$fixed', '$datum', '$phase', '$saison')";
        db::writedb($sql);
        $sql="INSERT INTO turniere_details (turnier_id, hallenname, strasse, plz, ort, haltestellen, plaetze, spielplan, startzeit, besprechung, hinweis, organisator, handy, startgebuehr)
                    VALUES ('$turnier_id','$hallenname','$strasse','$plz','$ort','$haltestellen','$plaetze','$spielplan','$startzeit','$besprechung','$hinweis', '$name', '$handy', '$startgebuehr');";
        db::writedb($sql);
        //Anmeldung des Ausrichters auf die Spielen-Liste
        $sql = "INSERT INTO turniere_liste (turnier_id, team_id, liste, freilos_gesetzt) VALUES ('$turnier_id','$ausrichter','spiele','Nein')";
        db::writedb($sql);

        //Spieltag in Abhängigkeit aller anderen Turniere bestimmen zu bestimmen
        Ligabot::set_spieltage();
        return true;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////TURNIERDATEN BEKOMMEN///////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    //Alle Turnierdaten auf einmal um die Datenbank zu schonen:
    //Format: $return[turnier_id][daten]
    public static function get_all_turniere($where = '')
    {
        $sql = "SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter "
                . $where .
                 "ORDER BY turniere_liga.datum asc ";
        //db::debug($sql);
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            $return[$x['turnier_id']] = $x;
        }
        return db::escape($return); //array
    }

    //Turnierdetails von nur dem Objekt Turnier erstellen
    function get_turnier_details()
    {
        $turnier_id = $this->turnier_id;
        $sql = "SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname
        FROM turniere_liga 
        INNER JOIN turniere_details 
        ON turniere_liga.turnier_id = turniere_details.turnier_id
        INNER JOIN teams_liga
        ON turniere_liga.ausrichter = teams_liga.team_id
        WHERE turniere_liga.turnier_id = '$turnier_id'";
        $result = db::readdb($sql);
        $return = mysqli_fetch_assoc($result);
        return db::escape($return); //array
    }

    //Alle Turnieranmeldelisten auf einmal um die Datenbank zu schonen: 
    //Format: $turnierlisten[turnier_id][liste] => Array der Anmeldedaten
    public static function get_all_anmeldungen($saison = Config::SAISON)
    {
        $sql=
        "SELECT turniere_liste.*, teams_liga.teamname, teams_liga.ligateam
        FROM turniere_liste
        LEFT JOIN teams_liga
        ON turniere_liste.team_id = teams_liga.team_id
        INNER JOIN turniere_liga
        ON turniere_liga.turnier_id = turniere_liste.turnier_id
        WHERE turniere_liga.saison = '$saison'
        AND turniere_liga.phase != 'ergebnis'
        ORDER BY turniere_liste.position_warteliste ASC";
        //db::debug($sql);
        $result = db::readdb($sql);
        $turnier_listen = array();
        $return = array();
        while ($anmeldung = mysqli_fetch_assoc($result)){
            if (empty($turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']])){
                $turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']] = array(); //Damit diverse Arrayfunktionen ordentlich funktionieren, auch bei leeren Listen.
            }                
                $turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']][$anmeldung['team_id']] = $anmeldung;
                $turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']][$anmeldung['team_id']]['tblock'] = Tabelle::get_team_block($anmeldung['team_id']);
                $turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']][$anmeldung['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($anmeldung['team_id']);
        }
        return db::escape($turnier_listen); //array
    }

    //Nur die Anmeldungen der Warte-, Melde-, Spielen-Liste des aktuellen Objektes
    //Format: zb. $liste['warte'] => Array der Anmeldedaten der Angemeldeten Teams auf der Warteliste
    function get_anmeldungen()
    {
        $turnier_id = $this->turnier_id;
        $sql=
        "SELECT turniere_liste.*, teams_liga.teamname, teams_liga.ligateam
        FROM turniere_liste
        LEFT JOIN teams_liga
        ON turniere_liste.team_id = teams_liga.team_id
        WHERE turniere_liste.turnier_id='$turnier_id'
        ORDER BY turniere_liste.position_warteliste ASC";
        $result = db::readdb($sql);
        $liste = array();
        while ($anmeldung = mysqli_fetch_assoc($result)){
            $liste[$anmeldung['liste']][$anmeldung['team_id']] = $anmeldung;
            $liste[$anmeldung['liste']][$anmeldung['team_id']]['tblock'] = Tabelle::get_team_block($anmeldung['team_id']);
            $liste[$anmeldung['liste']][$anmeldung['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($anmeldung['team_id']);
        }
        if (empty($liste['spiele'])){ $liste['spiele'] = array();} //Damit Array-Funktionen auch bei leeren Listen funktionieren
        if (empty($liste['melde'])){ $liste['melde'] = array();}
        if (empty($liste['warte'])){ $liste['warte'] = array();}
        return db::escape($liste); //array
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////ERGEBNISSE//////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    //Bekommt alle Turnieranmeldungen für den Spielplan nach wertigkeit sortiert.
    function get_liste_spielplan()
    {
        $turnier_id = $this->turnier_id;
        $sql=
        "SELECT turniere_liste.team_id, teams_liga.teamname, teams_liga.ligateam
        FROM turniere_liste
        LEFT JOIN teams_liga
        ON turniere_liste.team_id = teams_liga.team_id
        WHERE turniere_liste.turnier_id='$turnier_id' AND turniere_liste.liste='spiele'";
        $result = db::readdb($sql);
        $index = 1;
        $liste = array();
        while ($anmeldung = mysqli_fetch_assoc($result)){
                $liste[$index] = $anmeldung;
                $liste[$index]['tblock'] = Tabelle::get_team_block($anmeldung['team_id']);
                $liste[$index]['wertigkeit'] = Tabelle::get_team_wertigkeit($anmeldung['team_id']);
                $index++;
        }
        if (!empty($liste)){
            //Array nach Wertigkeit sortieren
            $sortkey = array_column($liste, 'wertigkeit');
            array_multisort($sortkey, SORT_DESC, $liste);

            //Indexierung mit 1 beginnen lassen für Spielplanerstellung
            $liste = array_combine(range(1, count($liste)), array_values($liste));
        }
        return db::escape($liste); //array
    }

    function delete_ergebnis()
    {
        $turnier_id = $this->turnier_id;
        $sql = "DELETE FROM turniere_ergebnisse WHERE turnier_id = $turnier_id";
        db::writedb($sql);
    }

    function get_ergebnis()
    {
        $turnier_id = $this->turnier_id;
        $sql = "SELECT * FROM turniere_ergebnisse WHERE turnier_id = $turnier_id ORDER BY platz ASC";
        $result = db::readdb($sql);
        $index = 1;
        while ($eintrag = mysqli_fetch_assoc($result)){
            $return[$index] = $eintrag;
            $index++;
        }
        return db::escape($return);
    }

    function set_ergebnis($team_id, $ergebnis, $platz)
    {
        $turnier_id = $this->turnier_id;
        $sql = "INSERT INTO turniere_ergebnisse (turnier_id, team_id, ergebnis, platz) VALUES ('$turnier_id', '$team_id', '$ergebnis', '$platz');";
        db::writedb($sql);
    }
        
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////TEAM AN- UND ABMELDUNG////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    //Ein Team zum Turnier anmelden
    //Bei Anmeldung auf die Warteliste sollte $pos als die jeweilige Wartelistenposition übergeben werden
    //Könnnte man das auch mit nl_anmelden für nichtligateams zusammenlegen?
    //TODO #10
    function team_anmelden($team_id, $liste, $pos = 0)
    {
        $turnier_id = $this->turnier_id;
        $freilos_gesetzt = 'Nein';
        //Handhabung der Warteliste, bei einer Anmeldung durch den LA nicht ans Ende der Warteliste... todo? Gehört das hier rein? Wird das überhaupt jemals benutzt werden?
        if ($liste == 'warte'){
            //Schreiben der Logs
            $teamname = Team::teamid_to_teamname($team_id);
            $sql = "SELECT team_id, position_warteliste FROM turniere_liste WHERE turnier_id = $turnier_id AND position_warteliste >= $pos";
            $result = db::readdb($sql);
            while ($team = mysqli_fetch_assoc($result)){
                $this->schreibe_log("Warteliste: \r\n". Team::teamid_to_teamname($team['team_id']) ." ". $team['position_warteliste'] ." -> ". ($team['position_warteliste']+1), "automatisch");
            }
            //Update der Wartelistepositionen
            $sql = "UPDATE turniere_liste SET position_warteliste = position_warteliste + 1 WHERE turnier_id = '$turnier_id' AND liste = 'warte' AND position_warteliste >= '$pos'";
            db::writedb($sql);
        }
        $sql = "INSERT INTO turniere_liste (turnier_id, team_id, liste, position_warteliste) VALUES ('$turnier_id', '$team_id','$liste', '$pos')";
        db::writedb($sql);
    }

    //Meldet ein Nichtligateam an
    //Existiert bereits ein Nichtligateam mit gleichem Namen in der Datenbank, so wird dieses angemeldet
    //es wird also kein neues Nichtligateam erstellt
    //Nichtligateams bekommen immer einen Stern hinter ihrem Namen
    function nl_anmelden($teamname, $liste, $pos = 0)
    {
        $turnier_id = $this->turnier_id;     
        $freilos_gesetzt = 'Nein';
        $teamname .= "*"; //Nichtligateams haben einen Stern hinter dem Namen
        if (empty(Team::teamname_to_teamid($teamname))){
            $nl_team_id = db::get_auto_increment('teams_liga');
            $sql = "INSERT INTO teams_liga (teamname, ligateam) VALUES ('$teamname','Nein')";
            db::writedb($sql);
        }else{
            $nl_team_id = Team::teamname_to_teamid($teamname);
        }
        if ($liste == 'warte'){
            //Schreiben der Logs für die Warteliste //Siehe auch team_anmelden()
            $teamname = 
            $sql = "SELECT team_id, position_warteliste FROM turniere_liste WHERE turnier_id = $turnier_id AND position_warteliste >= $pos";
            $result = db::readdb($sql);
            while ($team = mysqli_fetch_assoc($result)){
                $this->schreibe_log("Warteliste: \r\n". Team::teamid_to_teamname($team['team_id']) ." ". $team['position_warteliste'] ." -> ". ($team['position_warteliste']+1), "automatisch");
            }
            $sql = "UPDATE turniere_liste SET position_warteliste = position_warteliste + 1 WHERE turnier_id = '$turnier_id' AND liste = 'warte' AND position_warteliste >= '$pos'";
            db::writedb($sql);
        }
        $sql = "INSERT INTO turniere_liste (turnier_id, team_id, liste, position_warteliste) VALUES ('$turnier_id', '$nl_team_id','$liste', '$pos')";
        db::writedb($sql);
    }

    //Team setzt freilos
    function freilos($team_id)
    {
        $turnier_id = $this->turnier_id;
        $team = new Team($team_id);
        $freilose = $team->get_freilose();
        $sql = "INSERT INTO turniere_liste (turnier_id, team_id, liste, freilos_gesetzt) VALUES ('$turnier_id','$team_id','spiele','Ja')";
        db::writedb($sql);
        $team->set_freilose($freilose-1);
    }

    //Team wird abgemeldet, bei Mehrfachanmeldungen werden alle Anmeldungen entfernt
    function abmelden($team_id)
    {
        $turnier_id = $this->turnier_id;
        $sql = "DELETE FROM turniere_liste WHERE turnier_id = '$turnier_id' AND team_id = '$team_id'";
        db::writedb($sql);
    }

    //Sucht alle Wartelisteneinträge und sortiert diese der größe ihrer Position auf der Warteliste. Anschließend
    //weren die Wartelistenpostionen von eins auf wieder vergeben
    //Bsp: position auf der warteliste: 2 4 5 wird zu 1 2 3
    function warteliste_aktualisieren($autor = 'automatisch')
    {
        //Für den Turnierlog
        $listen_vorher = $this->get_anmeldungen();
        $turnier_id = $this->turnier_id;
        //Warteliste korrigieren, wenn sich das Team von der Warteliste abmeldet
        $sql = "SELECT * FROM turniere_liste WHERE turnier_id = '$turnier_id' AND liste = 'warte' ORDER BY position_warteliste";
        $result = db::readdb ($sql);
        $pos = 0;
        while ($team = mysqli_fetch_assoc($result)){
            $pos += 1;
            $team_id = $team['team_id'];
            $sql = 
            "UPDATE turniere_liste 
            SET position_warteliste = '$pos' 
            WHERE turnier_id = '$turnier_id'
            AND liste = 'warte'
            AND team_id = '$team_id'; ";
            db::writedb($sql);
        }
        //Turnierlog schreiben
        $listen_nachher = $this->get_anmeldungen();
        foreach (($listen_vorher['warte'] ?? array()) as $key => $team_vorher){
            $team_nachher = $listen_nachher['warte'][$key];
            //Die Reihenfolge sollte sich nicht ändern dürfen, da get_anmeldungen nach der position auf der Warteliste sortiert
            if ($team_vorher['position_warteliste'] !=  $team_nachher['position_warteliste']){
                $this->schreibe_log("Warteliste aktualisieren: \r\n". $team_vorher['teamname'] ." ". $team_vorher['position_warteliste'] ." -> ". $team_nachher['position_warteliste'], $autor);
            }
        }
    }

    //Füllt freie Plätze auf der Spielen-Liste von der Warteliste aus wieder auf,
    //wenn der Teamblock des Wartelisteneintrags zum Turnier passt
    //wenn das Turnier nicht in der offenen Phase ist
    //wenn das Turnier noch freie Plätze hat
    function spieleliste_auffuellen ($autor = 'automatisch')
    {
        $turnier_id = $this->turnier_id;
        $daten = $this->daten;
        $freie_plaetze = $this->anzahl_freie_plaetze();
        if ($daten['phase'] != 'offen' && $freie_plaetze > 0){
            $liste = $this->get_anmeldungen(); //Order by Warteliste weshalb die Teams in der foreach schleife in der Richtigen reihenfolge behandelt werden
            foreach (($liste['warte'] ?? array()) as $team){
                if ($this->check_team_block($team['team_id']) && $freie_plaetze  > 0){ //Das Team wird abgemeldet, wenn es schon am Turnierdatum auf einer Spielenliste steht
                    if (!$this->check_doppel_anmeldung($team['team_id'])){
                        $this->liste_wechsel($team['team_id'], 'spiele'); //von Warteliste abmelden
                        $this->schreibe_log("Spielenliste auffüllen: \r\n" . $team['teamname'] . " warte -> spiele", $autor);
                        $freie_plaetze -= 1;
                    }else{
                        $this->abmelden($team['team_id']);
                        $this->schreibe_log("Abgemeldet: \r\n" . $team['teamname'] . "Doppelanmeldung", $autor);
                    }
                    
                }
            }
            $this->warteliste_aktualisieren();
        }
    }

    //Findet die Anzahl der freien Plätze auf dem Turnier
    function anzahl_freie_plaetze()
    {
        $turnier_id = $this->turnier_id;
        $sql=
        "SELECT 
        (SELECT plaetze FROM turniere_details WHERE turnier_id='$turnier_id')
         - 
        (SELECT COUNT(liste_id) FROM turniere_liste WHERE turnier_id='$turnier_id' AND liste='spiele')
        AS freie_plaetze";
        $result = db::readdb($sql);
        $return = mysqli_fetch_assoc($result);
        return db::escape($return['freie_plaetze']);
    }

    //Ändert die Liste auf der sich ein Team befindet (Warte-, Melde- oder Spielen-Liste)
    function liste_wechsel($team_id, $liste, $pos = 0)
    {
        $turnier_id = $this->turnier_id;
        $sql = "UPDATE turniere_liste SET liste='$liste', position_warteliste='$pos'  WHERE turnier_id='$turnier_id' AND team_id = '$team_id'";
        db::writedb($sql);
    }

    //True, wenn das Team bereits zum Turnier angemeldet ist, sonst false
    function check_team_angemeldet($team_id)
    {
        $turnier_id = $this->turnier_id;
        $sql = "SELECT liste FROM turniere_liste WHERE team_id='$team_id' AND turnier_id='$turnier_id'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        if (!empty($result['liste'])){
            return true;
        }
        return false;
    }
    //True, wenn das Team am Kalendertag des Turnieres bereits bei einem Turnier auf der Spielenliste steht
    function check_doppel_anmeldung($team_id)
    {
        $datum = $this->daten['datum'];
        $sql = 
        "SELECT liste_id
        FROM turniere_liste
        INNER JOIN turniere_liga
        ON turniere_liste.turnier_id = turniere_liga.turnier_id
        WHERE team_id='$team_id' AND datum='$datum' AND liste='spiele'
        AND (turniere_liga.art='I' OR turniere_liga.art='II' OR turniere_liga.art='III')";
        $result = db::readdb($sql);
        if (mysqli_num_rows($result) > 0){
            return true;
        }
        return false;
    }

    function get_team_liste($team_id)
    {
        $turnier_id = $this->turnier_id;
        $sql = "SELECT liste FROM turniere_liste WHERE team_id='$team_id' AND turnier_id='$turnier_id'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result['liste'] ?? '');
    
    }

    //True, wenn der Teamblock in das Turnier passt.
    //Um die DB zu schonen, können teamblock und turnierblock auch manuell übergeben werden
    function check_team_block ($team_id)
    {
        $team_block = Tabelle::get_team_block($team_id);
        $turnier_block = $this->daten['tblock'];
        return self::check_team_block_static($team_block, $turnier_block);
    }

    //statische check team block ohne auf die db zugreifen
    public static function check_team_block_static ($team_block, $turnier_block)
    {
        if ($team_block == 'NL'){ //NL Teams können auch zu final, spass, fixed Turnieren angemeldet werden
            return true;
        }else{
            //Check ob es sich um ein Block-Turnier handelt (nicht spass oder finale)
            if (in_array($turnier_block, Config::BLOCK_ALL)){
                //Block-String in Array auflösen
                $turnier_block = str_split($turnier_block);
                $team_block = str_split($team_block);
                //Check ob ein Buchstabe des Team-Blocks im Turnier-Block vorkommt
                foreach ($team_block as $buchstabe){
                    if (in_array($buchstabe,$turnier_block)){
                        return true;
                    }     
                }
            }
        }
        return false;
    }

    //True wenn das Team für das Turnier ein freilos setzten darf
    function check_team_block_freilos ($team_id)
    {
        $team_block = Tabelle::get_team_block($team_id);
        $turnier_block = $this->daten['tblock'];
        return self::check_team_block_freilos_static ($team_block, $turnier_block);
    }

    //statische check team block freilos ohne auf die db zugreifen
    public static function check_team_block_freilos_static ($team_block, $turnier_block)
    {
        //Check ob es sich um einen Block-Turnier handelt (nicht spass, finale, oder fix)
        if (in_array($turnier_block, Config::BLOCK_ALL)){
            $pos_turnier = array_search($turnier_block, Config::BLOCK_ALL, true);
            $team_block = str_split($team_block);
            for ($i = $pos_turnier; $i <= (count(Config::BLOCK_ALL)-1); $i++){
                foreach ($team_block as $buchstabe){
                    $turnier_block = str_split(Config::BLOCK_ALL[$i]);
                    if (in_array($buchstabe,$turnier_block)){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////TURNIERDATEN ÄNDERN///////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    function set_link_spielplan($link)
    {
        $turnier_id = $this->turnier_id;
        $sql = "UPDATE turniere_details SET link_spielplan='$link' WHERE turnier_id='$turnier_id'";
        db::writedb($sql);
        $this->daten['link_spielplan'] = $link;
    }
    
    //Ändert die Phase in der sich das Turnier befindet
    function set_phase ($phase)
    {
        $turnier_id = $this->turnier_id;
        $sql = "UPDATE turniere_liga SET phase='$phase' WHERE turnier_id='$turnier_id'";
        db::writedb($sql);
        $this->daten['phase'] = $phase;
    }
    function set_turnier_block ($block)
    {
        $turnier_id = $this->turnier_id;
        $sql = "UPDATE turniere_liga SET tblock='$block' WHERE turnier_id='$turnier_id'";
        db::writedb($sql);
        $this->daten['tblock'] = $block;
    }

    function set_spieltag($spieltag)
    {
        $turnier_id = $this->turnier_id;
        $sql = "UPDATE turniere_liga SET spieltag='$spieltag' WHERE turnier_id='$turnier_id'";
        db::writedb($sql);
        $this->daten['spieltag'] = $spieltag;
    }

    function change_turnier_details($startzeit, $besprechung, $plaetze, $spielplan, $hallenname, $strasse, $plz, $ort, $haltestellen = '', $hinweis, $startgebuehr, $name, $handy)
    {
        $turnier_id = $this->turnier_id;
        $sql=
        "UPDATE turniere_details 
        SET hallenname='$hallenname', strasse='$strasse', plz='$plz', ort='$ort', haltestellen='$haltestellen', plaetze='$plaetze', spielplan='$spielplan', startzeit='$startzeit', besprechung='$besprechung', hinweis='$hinweis', organisator='$name', handy='$handy', startgebuehr='$startgebuehr'
        WHERE turnier_id = '$turnier_id'";
        db::writedb($sql);
        return true;
    }

    function change_turnier_liga($tname, $ausrichter, $art, $tblock, $fixed, $datum, $phase)
    {
        $turnier_id = $this->turnier_id;
        $sql= 
        "UPDATE turniere_liga 
        SET tname='$tname', phase='$phase', ausrichter='$ausrichter', art='$art', tblock='$tblock', tblock_fixed='$fixed', datum='$datum'
        WHERE turnier_id = '$turnier_id'";
        db::writedb($sql);
        return true;
    }

    function change_turnier_block($tblock, $fixed, $art)
    {
        $turnier_id = $this->turnier_id;
        $sql=
        "UPDATE turniere_liga 
        SET tblock = '$tblock', tblock_fixed = '$fixed', art = '$art'
        WHERE turnier_id = '$turnier_id'";
        db::writedb($sql);
        return true;
    }

    //Schreibt in den Turnierlog
    function schreibe_log($log_text,$autor)
    {
        $turnier_id = $this->turnier_id;
        $sql= "INSERT INTO turniere_log (turnier_id, log_text, autor) VALUES ('$turnier_id','$log_text', '$autor');";
        db::writedb($sql);
    }

    //Schreibt in den Turnierlog
    function get_logs()
    {
        $turnier_id = $this->turnier_id;
        $sql= "SELECT * FROM turniere_log WHERE turnier_id = '$turnier_id'";
        $result = db::readdb($sql);
        $logs = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($logs, $x);
        }
        return db::escape($logs);
    }

    //Löscht das Turnier
    function delete()
    {
    $turnier_id = $this->turnier_id;
    $sql = "DELETE FROM turniere_liga WHERE turnier_id = $turnier_id";
    db::writedb($sql);
    Ligabot::set_spieltage();
    return true;
    }
}