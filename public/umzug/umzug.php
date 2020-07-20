<?php

require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/la_session.logic.php'; //Auth

////////////////////////////////Verbindung mit alter Datenbank////////////////////////////////
unset($verbindung_zur_datenbank);
$verbindung = new db("db_test_alt");

////////////////////////////////Teams übertragen//////////////////////////////
$sql = "SELECT  *, teams.teamID AS r_teamID
FROM teams 
LEFT JOIN teams_details
ON teams.teamID = teams_details.teamID
WHERE saison >= '2019'
ORDER BY teams.teamID ASC"; //r_teamID, da teams_details.teamID = NULL für NLs
$result = db::readdb($sql);
$return_team = array();
while ($x =  mysqli_fetch_assoc($result)){
    $return_team[$x['r_teamID']]['teamname']=trim($x['name']);
    $return_team[$x['r_teamID']]['plz']=trim($x['plz']);
    $return_team[$x['r_teamID']]['ort']=trim($x['stadt']);

    $return_team[$x['r_teamID']]['ligavertreter']=trim($x['kontakt_name']);
    $return_team[$x['r_teamID']]['email_string']=trim($x['kontakt_email']);

    $return_team[$x['r_teamID']]['homepage']=trim($x['homepage']);
    $return_team[$x['r_teamID']]['freilose']=trim($x['freilose']);
    $return_team[$x['r_teamID']]['verein']=html_entity_decode(trim($x['verein']), ENT_QUOTES | ENT_XML1, 'UTF-8');

    if ($x['email_oeffentlich'] == 'J'){$x['public']  = 'Ja';}else{$x['public'] = 'Nein';}
    if ($x['info_email'] == 'J'){$x['get_info_mail']  = 'Ja';}else{$x['get_info_mail'] = 'Nein';}
    $return_team[$x['r_teamID']]['public'] = $x['public'];
    $return_team[$x['r_teamID']]['get_info_mail'] = $x['get_info_mail'];

    if ($x['saison'] == 2020){
        $return_team[$x['r_teamID']]['aktiv']='Ja';
    }
    if ($x['ligateam'] == 'J'){
        $return_team[$x['r_teamID']]['ligateam']='Ja';
    }else{
        $return_team[$x['r_teamID']]['ligateam']='Nein';
        $return_team[$x['r_teamID']]['teamname']=trim($x['name']) . '*';
    }
}

//team_kontakt emails in einzele emails umwandeln
foreach ($return_team as $team_id => $team){
    if (!empty($team['email_string'])){
        $emails = array_map('trim', explode(",", $team['email_string']));
        $emails = array_map('strtolower', $emails);
        $return_team[$team_id]['emails'] = $emails;
    }
}
//db::debug($return_team);

//Email Validierung
//db::debug($return_team);
foreach ($return_team as $team){
    if (!empty($team['emails'])){
        foreach ($team['emails'] as $email){
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                //die("FEHLERHAFTE EMAIL");
                Form::error("FEHLER");
                db::debug($email);
            }
        }
    }
}
//homepage Validierung
foreach ($return_team as $team){
    if (!empty($team['homepage'])){
        if (!filter_var($team['homepage'], FILTER_VALIDATE_URL)) {
            db::debug("FEHLER");
            db::debug($team['homepage']);
        }
    }
}
//db::debug($return_team);
//Doppelte Teamnamen finden un Ändern
foreach ($return_team as $team_id_a => $team_a){
    foreach($return_team as $team_id_b => $team_b){
        if ($team_a['teamname'] == $team_b['teamname'] && $team_id_a != $team_id_b){
            if ($team_id_a < $team_id_b){
                $return_team[$team_id_a]['teamname'] = $team_a['teamname'] .' ('. $team_id_a . ')';
                //db::debug($team_a['teamname'] .' ('. $team_id_a . ')');
                //db::debug($team_b['teamname'] .' '. $team_id_b);
            }else{
                $return_team[$team_id_b]['teamname'] = $team_b['teamname'] .' ('. $team_id_b . ')';
                //db::debug($team_b['teamname'] .' ('. $team_id_b. ')');
                //db::debug($team_a['teamname'] .' '. $team_id_a);
            }
            
        }
    }
}
//db::debug($return_team);
////////////////////////////////Kader übertragen//////////////////////////////
//Kader
$sql = "SELECT *
    FROM spieler 
    INNER JOIN kader
    ON spieler.spielerID = kader.spielerID
    WHERE kader.saison = '2020'";
$result = db::readdb($sql);
$return_kader = array();
while ($x =  mysqli_fetch_assoc($result)){
    $return_kader[$x['spielerID']]['team_id']=$x['teamID'];

    //DESWEGEN IST ES WICHTIG AUFS CHARACTERSET ZU ACHTEN
    $x['vorname'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $x['vorname']);
    $x['nachname'] = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $x['nachname']);
    $return_kader[$x['spielerID']]['vorname']=html_entity_decode(trim($x['vorname']), ENT_COMPAT, 'UTF-8');
    $return_kader[$x['spielerID']]['nachname']=html_entity_decode(trim($x['nachname']), ENT_COMPAT, 'UTF-8');

    $return_kader[$x['spielerID']]['jahrgang']=trim($x['jahrgang']);

    if ($x['geschlecht'] == 'MAENNLICH'){ $x['geschlecht']='m';
    }elseif ($x['geschlecht'] == 'WEIBLICH'){ $x['geschlecht']='w';
    }else{ $x['geschlecht']='d'; }
    $return_kader[$x['spielerID']]['geschlecht']=$x['geschlecht'];

    $return_kader[$x['spielerID']]['vorname']=$x['vorname'];
}
//db::debug($return_kader);

//schiris
$sql="SELECT spieler.spielerID, schiris.darf_schiedsen_bis, schiris.pruefungsart
    FROM spieler 
    INNER JOIN kader
    ON spieler.spielerID = kader.spielerID
    INNER JOIN schiris
    ON spieler.spielerID = schiris.spielerID
    WHERE kader.saison = '2020' AND schiris.darf_schiedsen_bis >= '2020'";
$result = db::readdb($sql);
while ($x =  mysqli_fetch_assoc($result)){
    $return_kader[$x['spielerID']]['schiri']=$x['darf_schiedsen_bis'];
    if ($x['pruefungsart'] == 'JUNIOR'){
        $return_kader[$x['spielerID']]['junior']='Ja';
    }else{
        $return_kader[$x['spielerID']]['junior']='Nein';
    }
}
//Ausbilder
$sql="SELECT * FROM `schiriausbildung_ausbilder` WHERE aktiv='J'";
$result = db::readdb($sql);
while ($x =  mysqli_fetch_assoc($result)){
    if (isset($return_kader[$x['spielerID']])){
        $return_kader[$x['spielerID']]['schiri']='Ausbilder/in';
    }
}
//db::debug($return_kader);



////////////////////////////////Turniere übertragen//////////////////////////////
$sql = "SELECT * 
    FROM turniere 
    INNER JOIN turniere_details
    ON turniere.turnierID = turniere_details.turnierID
    WHERE geloescht='N' AND SAISON >= '2019'";
$result = db::readdb($sql);
$return = array();
while ($x =  mysqli_fetch_assoc($result)){

    $return_turnier[$x['turnierID']]['ort'] = $x['stadt'];
    $return_turnier[$x['turnierID']]['plaetze'] = $x['plaetze'];
    $return_turnier[$x['turnierID']]['tblock'] = $x['typ'];
    $return_turnier[$x['turnierID']]['datum'] = $x['datum'];
    $return_turnier[$x['turnierID']]['organisator'] = $x['voo_name'];
    $return_turnier[$x['turnierID']]['handy'] = $x['voo_handy'];
    if (empty($x['voo_handy'])){$return_turnier[$x['turnierID']]['handy'] = $x['voo_telefon'];}
    $return_turnier[$x['turnierID']]['hinweis'] = $x['hinweis'];
    $return_turnier[$x['turnierID']]['startzeit'] = $x['beginn'];

    if ($x['startgebuehr'] == 0){ $x['startgebuehr']='keine'; }else{ $x['startgebuehr'] = $x['startgebuehr'] .' Euro';}
    $return_turnier[$x['turnierID']]['startgebuehr'] = $x['startgebuehr'];
    
    $return_turnier[$x['turnierID']]['saison'] = $x['saison'];
    $return_turnier[$x['turnierID']]['ausrichter'] = $x['ausrichter_teamID'];
    
    if (!empty($x['typw'])){$x['art'] = 'II';}else{$x['art']='I';}
    if ($x['typ'] == 'ABCDEF'){$x['art'] = 'III';}
    if (in_array($x['typ'],array('FINALE','BFINALE','CFINALE','DFINALE','QUALI'))){ $x['art'] = 'final'; $x['tblock_fixed']='Ja'; $return_turnier[$x['turnierID']]['tblock']='final';}
    $return_turnier[$x['turnierID']]['art'] = $x['art'];

    if($x['typ'] == 'FINALE'){$x['tname'] = 'Deutsche Meisterschaft';}
    if($x['typ'] == 'BFINALE'){$x['tname'] = 'B-Meisterschaft';}
    if($x['typ'] == 'CFINALE'){$x['tname'] = 'C-Meisterschaft';}
    if($x['typ'] == 'DFINALE'){$x['tname'] = 'D-Meisterschaft';}
    if($x['typ'] == 'QUALI'){$x['tname'] = 'Quali zur Deutschen Meisterschaft';}
    $return_turnier[$x['turnierID']]['tblock_fixed'] = $x['tname'] ?? '';

    if ($x['art'] == 'III'){$x['tblock_fixed']='Ja';}
    $return_turnier[$x['turnierID']]['tblock_fixed'] = $x['tblock_fixed'] ?? 'Nein';

    if ($x['plaetze']=='8'){$x['spielplan'] = 'gruppe';}else{$x['spielplan']='jgj';}
    $return_turnier[$x['turnierID']]['spielplan'] = $x['spielplan'];
    
    if ($x['status']=='ERGEBNIS'){$x['status']='ergebnis';}
    if ($x['status']=='OFFEN'){$x['status']='offen';}
    if ($x['status']=='SPIELPLAN'){$x['status']='spielplan';}
    if ($x['status']=='SPERRE'){$x['status']='melde';}
    if ($x['status']=='MELDEN'){$x['status']='melde';}
    $return_turnier[$x['turnierID']]['phase'] = $x['status'];


}
//db::debug($return_turnier);

////////////////////////////////Ergebnisse übertragen//////////////////////////////
$sql = "SELECT * FROM ergebnisse WHERE SAISON >= '2019'";
$result = db::readdb($sql);
$return_ergebnisse = array();
while ($x =  mysqli_fetch_assoc($result)){
    $return_ergebnisse[$x['turnierID']][$x['teamID']]['ergebnis'] = $x['punkte'];
    $return_ergebnisse[$x['turnierID']][$x['teamID']]['platz'] = $x['platz'];
    $return_ergebnisse[$x['turnierID']][$x['teamID']]['team_id']  = $x['teamID'];
}
//db::debug($return_ergebnisse);

////////////////////////////////Turnieranmeldungen übertragen//////////////////////////////
$sql = "SELECT turniere.*, meldungen.*
    FROM meldungen
    INNER JOIN turniere
    ON turniere.turnierID = meldungen.turnierID
    WHERE saison = '2020' AND turniere.geloescht = 'N'";
$result = db::readdb($sql);
$return_liste = array();
while ($x =  mysqli_fetch_assoc($result)){
    if ($x['status']!='WEG'){
        if ($x['status']=='SPIELEN' or $x['status'] == 'GESPIELT'){ 
            $x['liste']='spiele'; 
        }elseif ($x['status']=='NICHTLIGA'){
            $x['liste']='warte';
        }else{
            $x['liste']='melde';
        }
            if ($x['aktion']=='FREILOS'){$x['freilos_gesetzt'] = 'Ja';}
            $return_liste[$x['turnierID']][$x['teamID']]['freilos_gesetzt'] = $x['freilos_gesetzt'] ?? 'Nein';
            $return_liste[$x['turnierID']][$x['teamID']]['liste'] = $x['liste'];
            $return_liste[$x['turnierID']][$x['teamID']]['team_id']  = $x['teamID'];
    }
}
//db::debug($return_turnier);
////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////Mit neuer Datenbank verbinden////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////

//Sanitizing:
$return_ergebnisse=db::sanitize($return_ergebnisse);
$return_kader=db::sanitize($return_kader);
$return_liste=db::sanitize($return_liste);
$return_team=db::sanitize($return_team);
$return_turnier=db::sanitize($return_turnier);

unset($verbindung);
$verbindung = new db("db_test_neu");

////////////////////////////////Teams eintragen////////////////////////////////
foreach ($return_team as $team_id => $team){
    if (!isset($team['aktiv'])){
        $team['aktiv'] = 'Nein';
    }
    $sql = "INSERT INTO teams_liga 
        (team_id, teamname, ligateam, freilose, aktiv, passwort)
        VALUES
        ('$team_id','".$team['teamname']."', '".$team['ligateam']."', '".$team['freilose']."', '".$team['aktiv']."','liga');";
        //db::writedb($sql);
        //db::debug($sql);
    if ($team['ligateam'] == 'Ja'){
        $sql = "INSERT INTO teams_details (team_id, plz, ort, homepage, ligavertreter, verein) VALUES ('$team_id', '".$team['plz']."', '".$team['ort']."', '".$team['homepage']."', '".$team['ligavertreter']."', '".$team['verein']."');";
        //db::writedb($sql);
        //db::debug($sql);
    }
    if (isset($team['emails'])){
        foreach ($team['emails'] as $email){
            $sql = "INSERT INTO teams_kontakt (team_id, email, public, get_info_mail) VALUES ('$team_id', '$email', '".$team['public']."', '".$team['get_info_mail']."');";
            //db::writedb($sql);
            //db::debug($sql);
        }
    }
}

////////////////////////////////Spieler eintragen////////////////////////////////
foreach ($return_kader as $spieler_id => $spieler){
    $sql = "INSERT INTO spieler 
        (spieler_id, team_id, vorname, nachname, jahrgang, geschlecht, schiri, junior)
        VALUES
        ('$spieler_id','".$spieler['team_id']."', '".$spieler['vorname']."', '".$spieler['nachname']."', '".$spieler['jahrgang']."', '".$spieler['geschlecht']."', '".($spieler['schiri'] ?? '')."', '".($spieler['junior'] ?? '')."');";
    //db::debug($sql);
    //db::writedb($sql);
    
    if (!isset($spieler['vorname'])){
        db::debug($spieler);
        db::debug($spieler_id);
    }
}
$sql ="UPDATE spieler SET letzte_saison='2019'";
//db::writedb($sql);

//db::debug($return_kader);
////////////////////////////////Turniere eintragen////////////////////////////////
foreach ($return_turnier as $turnier_id => $turnier){
    $sql = "INSERT INTO turniere_liga 
        (turnier_id, ausrichter, art, tblock, tblock_fixed, datum, phase, saison, tname)
        VALUES
        ('$turnier_id','".$turnier['ausrichter']."', '".$turnier['art']."', '".$turnier['tblock']."', '".$turnier['tblock_fixed']."', '".$turnier['datum']."', '".$turnier['phase']."', '".$turnier['saison']."', '".$turnier['tname']."' );";
    //db::writedb($sql);
    //db::debug($sql);
    $sql = "INSERT INTO turniere_details 
        (turnier_id, ort, plaetze, spielplan, startzeit, hinweis, organisator, handy, startgebuehr) 
        VALUES 
        ('$turnier_id', '".$turnier['ort']."', '".$turnier['plaetze']."', '".$turnier['spielplan']."', '".$turnier['startzeit']."', '".$turnier['hinweis']."', '".$turnier['organisator']."', '".$turnier['handy']."', '".$turnier['startgebuehr']."');";
    //db::writedb($sql);
    //db::debug($sql);
    $sql = "UPDATE turniere_details SET besprechung='Nein'";
    //db::writedb($sql);
}

////////////////////////////////Ergebnisse eintragen////////////////////////////////

//ACHTUNG kann DOPPELT EINGETRAGEN werden, keine Kontrolle da keine ergebnis_id!
foreach ($return_ergebnisse as $turnier_id => $ergebnisse){
    foreach ($ergebnisse as $team_id => $ergebnis){
        $sql = "INSERT INTO turniere_ergebnisse 
            (turnier_id, ergebnis, platz, team_id)
            VALUES
            ('$turnier_id','".$ergebnis['ergebnis']."', '".$ergebnis['platz']."', '$team_id');";
        //db::debug($sql);
        //db::writedb($sql);
    }
}

////////////////////////////////Anmeldungen eintragen////////////////////////////////

//ACHTUNG kann DOPPELT EINGETRAGEN werden, keine Kontrolle da keine ergebnis_id in der alten db!
foreach ($return_liste as $turnier_id => $liste){
    foreach ($liste as $team_id => $anmeldung){
        $sql = "INSERT INTO turniere_liste
            (turnier_id, team_id, liste, freilos_gesetzt)
            VALUES
            ('$turnier_id', '$team_id', '".$anmeldung['liste']."', '".$anmeldung['freilos_gesetzt']."');";
        //db::debug($sql);
        //db::writedb($sql);
    }
}

//Ergebnisse der abgebrochenen Saison der Vorsaison zuordnen
//db::writedb("UPDATE turniere_liga SET saison='2019' WHERE saison='2020' AND phase='ergebnis'");

//Saisonzählung umstellen!
/*
db::writedb("UPDATE `spieler` SET `letzte_saison`='25'");
db::writedb("UPDATE `turniere_liga` SET `saison`='24' WHERE saison='2019'");
db::writedb("UPDATE `turniere_liga` SET `saison`='25' WHERE saison='2020'");
db::writedb("UPDATE `turniere_liga` SET `saison`='26' WHERE datum > '2020-08-13'");
db::writedb("UPDATE `spieler` SET `schiri`='26' WHERE schiri = '2020'");
db::writedb("UPDATE `spieler` SET `schiri`='27' WHERE schiri = '2021'");
db::writedb("UPDATE `spieler` SET `schiri`='28' WHERE schiri = '2022'");
//Deutsche Meisterschaft Ergebnsse auf 0
db::writedb("UPDATE `turniere_ergebnisse` SET `ergebnis`='0' WHERE turnier_id = '827'");
*/


//...Querrad:
/*
$team_id = db::get_auto_increment('teams_liga');
Team::create_new_team('Querrad (Erg. n. übern.)','','');
db::writedb("UPDATE teams_liga SET aktiv='Nein' WHERE team_id = '$team_id'");
db::writedb("UPDATE turniere_ergebnisse 
        INNER JOIN turniere_liga 
        ON turniere_liga.turnier_id = turniere_ergebnisse.turnier_id 
        SET turniere_ergebnisse.team_id = '$team_id' WHERE turniere_ergebnisse.team_id = '20' AND turniere_liga.saison = '24'");
//Überflüssige Teams löschen
db::writedb("DELETE FROM teams_liga WHERE teams_liga.aktiv = 'Nein' AND teams_liga.team_id NOT IN (SELECT turniere_ergebnisse.team_id FROM turniere_ergebnisse)");
//Ligabot Spieltage setzen und Datenbank sichern:
LigaBot::liga_bot();
*/

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

include '../../templates/header.tmp.php';
?>

<?php include '../../templates/footer.tmp.php';