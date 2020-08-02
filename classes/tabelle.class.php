<?php
class Tabelle {
    
    public static function get_aktuellen_spieltag($saison = Config::SAISON)
    {
        $sql = "SELECT * FROM turniere_liga WHERE saison = '$saison' AND (art='I' OR art = 'II' OR art='III') ORDER BY spieltag ASC";
        $result = db::readdb($sql);
        while ($turnier =  mysqli_fetch_assoc($result)){
            if ($turnier['phase'] != 'ergebnis'){
                return $turnier['spieltag'];
            }
            $max_spieltag = $turnier['spieltag'];
        }
        return db::escape($max_spieltag ?? 0); //Durch ASC wird der höchste Spieltag übergeben, wenn alle Turniere in der Ergebnisphase sind
    }
    
    //Schaut ob für der aktuelle Spieltag live ist.
    public static function check_spieltag_live($spieltag, $saison = Config::SAISON)
    {
        $sql = "SELECT * FROM turniere_liga WHERE spieltag = '$spieltag' AND (art='I' OR art = 'II' OR art='III') AND saison = '$saison'";
        $result = db::readdb($sql);
        $check_1 = $check_2 = false;
        while ($turnier =  mysqli_fetch_assoc($result)){
            if ($turnier['phase'] != 'ergebnis'){
                $check_1 = true;
            }else{
                $check_2 = true;
            }
        }
        if ($check_1 && $check_2){ //Es gibt also am Spieltag mind ein Turnier mit eingetragenem Ergebnis und mind eines bei dem das Ergebnis noch fehlt
            return true;
        }
        return false;
    }

    //Schaut ob das Turnierergebnis eingetragen werden darf.
    public static function check_ergebnis_eintragbar($akt_turnier)
    {
        $spieltag = $akt_turnier->daten['spieltag'];
        $saison = $akt_turnier->daten['saison'];
        if (!in_array($akt_turnier->daten['art'],['I','II','III', 'final'])){
            Form::error("Für diesen Turniertyp können keine Ergebnisse eingetragen werden.");
            return false;
        }
        //Spieltag = 0, wenn keiner zugeorndet wurde
        $sql = "SELECT * FROM turniere_liga WHERE spieltag < '$spieltag' AND spieltag != 0 AND (art='I' OR art = 'II' OR art='III') AND saison = '$saison' ORDER BY spieltag ASC, datum ASC";
        //db::debug($sql);
        $result = db::readdb($sql);
        while ($test =  mysqli_fetch_assoc($result)){
            if ($test['phase'] != 'ergebnis'){
                Form::error("Es fehlen noch Turnierergebnisse von vorherigen Spieltagen.");
                return false;
            }
        }
        return true;
    }

    //in die klasse turnier verschieben und immer den richtigen wert bekommen!
    public static function get_team_block($team_id, $spieltag='')
    {
        //Teamblock des relevanten Spieltages, also des Spieltages für den alle Ergebnisse eingetragen sind
        if (empty($spieltag)){$spieltag = self::get_aktuellen_spieltag() - 1;} //-1, da immer der Spieltag zählt, für den alle Ergebnisse eingetragen sind
        if (!isset($GLOBALS['rang_tabelle'][$spieltag])){ //Rangtabelle muss nicht jedes mal neu berechnet werden müssen
            $GLOBALS['rang_tabelle'][$spieltag] = Tabelle::get_rang_tabelle($spieltag);
        }
        $key = array_search($team_id, array_column($GLOBALS['rang_tabelle'][$spieltag], 'team_id')); //$key = false, wenn nicht gefunden, ansonsten Position
        // 0 wird aber auch als false interpretiert, deswegen is_numeric und nicht ($key)
        if (is_numeric($key)){ 
            $platz=$GLOBALS['rang_tabelle'][$spieltag][$key]['platz'];
            return self::platz_to_block($platz);
        }else{
            return 'NL';
        }
    }

    public static function get_team_wertigkeit($team_id, $spieltag='')
    {
        if (empty($spieltag)){$spieltag = self::get_aktuellen_spieltag() - 1;}
        if (!isset($GLOBALS['rang_tabelle'][$spieltag])){ //Rangtabelle muss nicht jedes mal neu berechnet werden müssen
            $GLOBALS['rang_tabelle'][$spieltag] = Tabelle::get_rang_tabelle($spieltag);
        }
        $key = array_search($team_id, array_column($GLOBALS['rang_tabelle'][$spieltag], 'team_id'));
        if (is_numeric($key)){  //$key = false, wenn nicht gefunden, ansonsten Position
            // 0 wird aber auch als false interpretiert, deswegen is_numeric und nicht ($key)
            $platz=$GLOBALS['rang_tabelle'][$spieltag][$key]['platz'];
            return self::platz_to_wertigkeit($platz);
        }else{
            return 'NL';
        }
    }

    //Weist dem Platz in der Rangtabelle einen Block zu
    public static function platz_to_block($platz)
    {
        if (empty($platz)){
            return '';
        }
        //Blockzuordnung
        $blocks=array(
            "A"=>range(1,6),
            "AB"=>range(7,14),
            "BC"=>range(14,21),
            "CD"=>range(21,31),
            "DE"=>range(31,43),
            "EF"=>range(43,57),
            //"F"=>range(1,999),
            );
        foreach ($blocks as $block => $platzierung){
            if (in_array($platz, $platzierung)){
                return $block;
            }
        }
        return "F";
    }

    //Weist dem Platz in der Rangtabelle eine Wertigkeit zu
    public static function platz_to_wertigkeit($platz)
    {
        if (empty($platz)){
            return '';
        }
        //Blockzuordnung
        $platzierung = range(1,43);
            //0.97 =>range(44, 999)
        $platz = (float)$platz;
        if (in_array($platz, $platzierung)){
            return round(250*0.955**($platz-1),0);
        }
        return max(array(round(250*0.955**(43)*0.97**($platz-1-43),0),15));
    }

    //Für die Ergebnisseite
    public static function get_all_ergebnisse($saison = Config::SAISON)
    {
        $sql = 
        "SELECT turniere_ergebnisse.*, teams_liga.teamname 
        FROM turniere_ergebnisse
        LEFT JOIN teams_liga
        ON teams_liga.team_id = turniere_ergebnisse.team_id
        LEFT JOIN turniere_liga
        ON turniere_liga.turnier_id = turniere_ergebnisse.turnier_id
        WHERE turniere_liga.saison = '$saison'
        ORDER BY turniere_liga.datum DESC, platz ASC";
        $result = db::readdb($sql);
        $return = array();
        while ($eintrag = mysqli_fetch_assoc($result)){
            if (!isset($return[$eintrag['turnier_id']])){
                $return[$eintrag['turnier_id']] = array();
            }
            array_push($return[$eintrag['turnier_id']],$eintrag);
        }
        return db::escape($return);
    }

    public static function get_meisterschafts_tabelle($spieltag, $saison = Config::SAISON)
    {
        $sql="SELECT turniere_ergebnisse.ergebnis, turniere_ergebnisse.turnier_id, turniere_liga.datum, turniere_liga.saison, teams_liga.aktiv, teams_liga.teamname, teams_liga.team_id 
            FROM turniere_ergebnisse
            INNER JOIN teams_liga
            ON teams_liga.team_id = turniere_ergebnisse.team_id
            INNER JOIN turniere_liga
            ON turniere_liga.turnier_id = turniere_ergebnisse.turnier_id
            WHERE teams_liga.ligateam = 'Ja'
            AND turniere_liga.art != 'final' 
            AND (turniere_liga.saison = '$saison') 
            AND (turniere_liga.spieltag <= '$spieltag')
            ORDER BY ergebnis DESC, RAND()";
        $result = db::readdb($sql);
        $return = array();
        $counter = array();
        while ($eintrag = mysqli_fetch_assoc($result)){
            if (!isset($return[$eintrag['team_id']])){
                $return[$eintrag['team_id']] = array();

                $return[$eintrag['team_id']]['einzel_ergebnisse'] = array();
                array_push($return[$eintrag['team_id']]['einzel_ergebnisse'],$eintrag['ergebnis']);
                
                $return[$eintrag['team_id']]['team_id'] = $eintrag['team_id'];
                $return[$eintrag['team_id']]['teamname'] = $eintrag['teamname'];
                $return[$eintrag['team_id']]['string'] = Form::link("ergebnisse.php#" . $eintrag['turnier_id'], $eintrag['ergebnis']);
                $return[$eintrag['team_id']]['summe'] = $eintrag['ergebnis'];
                $counter[$eintrag['team_id']] = 1;
            }else{
                if ($counter[$eintrag['team_id']] <= 5){
                    array_push($return[$eintrag['team_id']]['einzel_ergebnisse'],$eintrag['ergebnis']);
                    $return[$eintrag['team_id']]['string'] .= "+" . Form::link("ergebnisse.php#" . $eintrag['turnier_id'], $eintrag['ergebnis']); 
                    $return[$eintrag['team_id']]['summe'] += $eintrag['ergebnis'];
                }
            }
            $counter[$eintrag['team_id']]++;
        }
        //Tabelle mit aktiven Teams ohne Ergebnis auffüllen
        //In vergangenen Saisons werden nur Teams mit Ergebnissen gelistet
        if ($saison == Config::SAISON){
            $list_of_teamids = Team::get_all_teamids();
            foreach($list_of_teamids as $team_id){
                if (!array_key_exists($team_id, $return)){
                    $return[$team_id] = array();
                    $return[$team_id]['teamname'] = htmlspecialchars_decode(Team::teamid_to_teamname($team_id)); //Ansonsten doppel db::escape --> fehler in der Darstellung
                    $return[$team_id]['team_id'] = $team_id;
                    $return[$team_id]['string'] = '';
                    $return[$team_id]['summe'] = 0;
                    $return[$team_id]['einzel_ergebnisse'] = array(0);
                }
            }
        }
        //db::debug($return);
        //Hinzufügen der Strafen:
        $strafen = Team::get_all_strafen();
        foreach ($strafen as $strafe){
            //Hinzufügen des Sterns
            if (!isset($return[$strafe['team_id']]['strafe_stern'])){
                $return[$strafe['team_id']]['strafe_stern'] = '*';
            }else{
                $return[$strafe['team_id']]['strafe_stern'] .= '*';
            }
            //Addieren der Prozentstrafen
            if ($strafe['verwarnung'] == 'Nein' && !empty($strafe['prozentsatz'])){
                $return[$strafe['team_id']]['strafe'] = ($return[$strafe['team_id']]['strafe'] ?? 0) + $strafe['prozentsatz'] / 100;
            }
        }
        //db::debug($return);
        //Kumulierte Strafe mit der Summe der Turnierergebnisse des Teams verrechnen
        foreach ($return as $team_id => $team){
            if (isset($team['strafe'])){
                $return[$team_id]['summe'] = round($return[$team_id]['summe'] * (1 - $team['strafe']), 0);
            }
        }

        //Nach Summe der Ergebnisse sortieren mit der Funktion "sortieren_summe" die eine public static function in dieser Klasse Tabelle ist
        usort($return, array("Tabelle", "sortieren_summe"));

        //Zuordnen der Plätze
        //Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $platz = 1;
        $zeile_vorher['platz'] = 1;
        $zeile_vorher['summe'] = 0;
        foreach ($return as $key => $zeile){
            
            $zeile['max_einzel'] = max($zeile['einzel_ergebnisse']);
            if (($zeile_vorher['summe'] ?? false) == $zeile['summe'] && ($zeile_vorher['max_einzel'] ?? false) == $zeile['max_einzel']){
                $return[$key]['platz'] = $zeile_vorher['platz'];
            }else{
                $return[$key]['platz'] = $platz;
            }
            $zeile_vorher['summe'] = $zeile['summe'];
            $zeile_vorher['max_einzel'] = $zeile['max_einzel'];
            $zeile_vorher['platz'] = $return[$key]['platz'];
            $platz++;            
        }
        return db::escape($return);
    }

    public static function get_rang_tabelle($spieltag, $saison = Config::SAISON)
    {
        if ($saison == 26){
            $ausnahme = "OR (turniere_liga.saison = '" . $saison . "' - 2)"; //Ausnahme wegen Corona-Saison
        }else{
            $ausnahme = '';
        }
        $sql="SELECT turniere_ergebnisse.ergebnis, turniere_ergebnisse.turnier_id, turniere_liga.datum, 
            turniere_liga.saison, teams_liga.teamname, teams_liga.team_id, turniere_liga.spieltag 
            FROM turniere_ergebnisse
            INNER JOIN teams_liga
            ON teams_liga.team_id = turniere_ergebnisse.team_id
            INNER JOIN turniere_liga
            ON turniere_liga.turnier_id = turniere_ergebnisse.turnier_id
            WHERE teams_liga.ligateam = 'Ja'
            AND teams_liga.aktiv = 'Ja'
            AND turniere_liga.art != 'final'
            AND ((turniere_liga.spieltag <= '$spieltag' 
            AND turniere_liga.saison = '$saison') 
            OR (turniere_liga.saison = '$saison' - 1)
            $ausnahme)
            ORDER BY turniere_liga.saison DESC, turniere_liga.datum DESC";
        $result = db::readdb($sql);
        $return = array();
        $counter = array();
        
        while ($eintrag = mysqli_fetch_assoc($result)){

            //Farbe des Ergebnisses in der Rangtabelle festlegen.
            if ($eintrag['saison'] != $saison){
                $color = "w3-text-green";
            }else{
                $color = 'w3-text-blue';
            }
            //Verlinkung des Ergebnisses hinzufügen
            $link = "ergebnisse.php?saison=".$eintrag['saison']."#".$eintrag['turnier_id'];

            //Initialisierung
            if (!isset($return[$eintrag['team_id']])){
                //Zähler der Ergebnisse (Max 5)
                $counter[$eintrag['team_id']] = 1;
                $return[$eintrag['team_id']]['summe'] = $eintrag['ergebnis'];
                $return[$eintrag['team_id']]['einzel_ergebnisse'] = array();
                array_push($return[$eintrag['team_id']]['einzel_ergebnisse'],$eintrag['ergebnis']);

                $return[$eintrag['team_id']]['team_id'] = $eintrag['team_id']; //Wichtig, da bei Sortierung die $eintrag['team_id] überschrieben wird
                $return[$eintrag['team_id']]['teamname'] = $eintrag['teamname'];
                $return[$eintrag['team_id']]['string'] = 
                    "<a href='$link' class='no $color w3-hover-text-secondary'>".$eintrag['ergebnis']."</a>";
                
            }else{
                if ($counter[$eintrag['team_id']] <= 5){
                    array_push($return[$eintrag['team_id']]['einzel_ergebnisse'],$eintrag['ergebnis']);
                    $return[$eintrag['team_id']]['string'] .= 
                        "+<a href='$link' class='no $color w3-hover-text-secondary'>".$eintrag['ergebnis']."</a>"; 
                    $return[$eintrag['team_id']]['summe'] += $eintrag['ergebnis'];
                }
            }
            $counter[$eintrag['team_id']]++;
            $return[$eintrag['team_id']]['avg'] = round($return[$eintrag['team_id']]['summe'] / count($return[$eintrag['team_id']]['einzel_ergebnisse']),1);
        }
        
        //Tabelle mit aktiven Teams ohne Ergebnis auffüllen
        //In vergangenen Saisons werden nur Teams mit Ergebnissen gelistet
        if ($saison == Config::SAISON){
            $list_of_teamids = Team::get_all_teamids();
            foreach($list_of_teamids as $team_id){
                if (!array_key_exists($team_id, $return)){
                    $return[$team_id] = array();
                    $return[$team_id]['teamname'] = htmlspecialchars_decode(Team::teamid_to_teamname($team_id)); //Ansonsten doppel db::escape --> fehler in der Darstellung
                    $return[$team_id]['team_id'] = $team_id;
                    $return[$team_id]['string'] = '';
                    $return[$team_id]['summe'] = 0;
                    $return[$team_id]['avg'] = 0;
                    $return[$team_id]['einzel_ergebnisse'] = array(0);
                }
            }
        }

        //Nach Summe der Ergebnisse sortieren mit der Funktion "sortieren_avg"
        usort($return, array("Tabelle", "sortieren_avg")); //Sortieren nach der static function sortieren_avg in der Klasse Tabelle...

        //Zuordnen der Blöcke
        //Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $platz = 1;
        $zeile_vorher['platz'] = 1;
        $zeile_vorher['summe'] = 0;
        foreach ($return as $key => $zeile){
            
            $zeile['max_einzel'] = max($zeile['einzel_ergebnisse']);
            if (($zeile_vorher['summe'] ?? false) == $zeile['summe'] && ($zeile_vorher['max_einzel'] ?? false) == $zeile['max_einzel']){
                $return[$key]['platz'] = $zeile_vorher['platz'];
            }else{
                $return[$key]['platz'] = $platz;
            }
            $zeile_vorher['summe'] = $zeile['summe'];
            $zeile_vorher['max_einzel'] = $zeile['max_einzel'];
            $zeile_vorher['platz'] = $return[$key]['platz'];
            $platz++;            
        }
        return db::escape($return);
    }

    //individuelle Sortierfunktion für die Meisterschaftstabelle
    public static function sortieren_summe($value1, $value2)
    {
        if ($value1['summe'] < $value2['summe']){
            $return = 1;
        }
        if ($value1['summe'] > $value2['summe']){
            $return = -1;
        }
        if ($value1['summe'] == $value2['summe']){
            $max1 = max($value1['einzel_ergebnisse']);
            $max2 = max($value1['einzel_ergebnisse']);
            if ($max1 < $max2){
                $return = -1;
            }
            if ($max1 < $max2){
                $return = 1;
            }
            if ($max1 == $max2){
                $return = 0;
            }
        }
        return $return;
    }

    //individuelle Sortierfunktion für die Meisterschaftstabelle
    public static function sortieren_avg($value1, $value2)
    {
        if ($value1['avg'] < $value2['avg']){
            $return = 1;
        }
        if ($value1['avg'] > $value2['avg']){
            $return = -1;
        }
        if ($value1['avg'] == $value2['avg']){
            $max1 = max($value1['einzel_ergebnisse']);
            $max2 = max($value1['einzel_ergebnisse']);
            if ($max1 < $max2){
                $return = -1;
            }
            if ($max1 < $max2){
                $return = 1;
            }
            if ($max1 == $max2){
                $return = 0;
            }
        }
        return $return;
    }
}