<?php
//Turnierobjekt erstellen
$turnier_id = $_GET['turnier_id'];
$akt_turnier = new Turnier ($turnier_id);

if ($akt_turnier->daten['ausrichter'] == ($_SESSION['team_id'] ?? '') or $ligacenter){
    $change_tbericht = true; //Berechtigung zum Verändern des Reports
}else{
    $change_tbericht = false;
}

if(strtotime($akt_turnier->daten['datum']) - Config::time_offset() < -3*24*60*60 && !$ligacenter){
    $change_tbericht = false; //Berechtigung zum Verändern des Reports widerrufen für Ausrichter, wenn das Turnier mehr als zwei Tage zurückliegt.
    if ($akt_turnier->daten['ausrichter'] == ($_SESSION['team_id'] ?? '')){
        Form::attention("Das Turnier liegt bereits in der Vergangenheit. Bearbeiten des Turnierreports nur noch via den Ligaausschuss möglich.");
    }
}

$tbericht = new TurnierReport ($turnier_id);
//db::debug($akt_turnier->get_liste_spielplan());

//Existiert das Turnier?
if (empty($akt_turnier->daten)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location: ../liga/turniere.php');
    die();
}

if ($akt_turnier->daten['art'] == 'spass'){
    Form::attention("Spaßturniere erfordern keinen Turnierreport.");
    header('Location: ../liga/turnier_details.php?turnier_id=' . $akt_turnier->daten['turnier_id']);
    die();
}
/*
if ($akt_turnier->get_team_liste($_SESSION['team_id']) != 'spiele'){
    Form::error("Fehlende Berechtigung");
    header('Location: ../teamcenter/tc_turnierliste_anmelden.php');
    die();
}
*/

//Liste der Teams
$teams = $akt_turnier->get_liste_spielplan();
//Kader und Ausbilder
$kader_array = $akt_turnier->get_kader_kontrolle();
$ausbilder_liste = array();
$spieler_liste = array();
foreach ($kader_array as $team_id => $kader){
    foreach ($kader as $spieler_id => $spieler){
        if($spieler['schiri'] == 'Ausbilder/in'){
            $ausbilder_liste[$spieler_id] = $spieler;
        }
        $spieler_liste[$spieler_id] = $spieler;
    }
}

//Spielerausleihe
$spieler_ausleihen = $tbericht->get_spieler_ausleihen();
    //Spielerausleihe löschen
foreach ($spieler_ausleihen as $ausleihe_id => $ausleihe){
    if (isset($_POST['del_ausleihe_' . $ausleihe_id]) && $change_tbericht){
        $tbericht->delete_spieler_ausleihe($ausleihe_id);
        Form::affirm("Spielerausleihe wurde entfernt.");
        header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
        die();
    }
}
    //Spielerausleihe hinzufügen
if (isset($_POST['new_ausleihe']) && $change_tbericht){
    $name = $_POST['ausleihe_name'];
    $team_ab = $_POST['ausleihe_team_ab'];
    $team_auf = $_POST['ausleihe_team_auf'];
    $team_id_ab = Team::teamname_to_teamid($team_ab);
    $team_id_auf = Team::teamname_to_teamid($team_auf);
    if (!(Team::is_ligateam($team_id_ab) && Team::is_ligateam($team_id_auf))){
        Form::error("Ligateams der Spielerausleihe wurden nicht gefunden");
        header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
        die();
    }
    /*
    $platz_ab = Tabelle::get_team_rang($team_id_ab, $akt_turnier->daten['spieltag']);
    $platz_auf = Tabelle::get_team_rang($team_id_auf, $akt_turnier->daten['spieltag']);
    if ($platz_ab - $platz_auf < 10 && !$ligacenter){
        Form::error("Es müssen mindestens 10 Plätze zwischen den beiden Teams in der Rangtabelle liegen.");
        header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
        die();
    }
    */
    $tbericht->new_spieler_ausleihe($name,$team_auf,$team_ab);
    Form::affirm("Spielerausleihe wurde hinzugefügt.");
    header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
    die();
}

//Zeitstrafen
$zeitstrafen = $tbericht->get_zeitstrafen();
    //Zeitstrafe löschen
foreach ($zeitstrafen as $zeitstrafe_id => $zeitstrafe){
    if (isset($_POST['del_zeitstrafe_' . $zeitstrafe_id]) && $change_tbericht){
        $tbericht->delete_zeitstrafe($zeitstrafe_id);
        Form::affirm("Zeitstrafe wurde entfernt.");
        header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
        die();
    }
}
    //Zeitstrafe hinzufügen
if (isset($_POST['new_zeitstrafe']) && $change_tbericht){
    $dauer = $_POST['zeitstrafe_dauer'];
    $name = $_POST['zeitstrafe_spieler'];
    $team_a = $_POST['zeitstrafe_team_a'];
    $team_b = $_POST['zeitstrafe_team_b'];
    $bericht = $_POST['zeitstrafe_bericht'];
    $tbericht->new_zeitstrafe($name,$dauer,$team_a,$team_b,$bericht);
    Form::affirm("Zeitstrafe wurde hinzugefügt.");
    header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
    die();
}

//Turnierbericht
if (isset($_POST['set_turnierbericht']) && $change_tbericht){
    $bericht = $_POST['turnierbericht'];
    $kader_check = $_POST['kader_check'];
    if ($kader_check == "kader_checked"){
        $kader_check = 'Ja';
    }else{
        $kader_check = 'Nein';
    }
    $tbericht->set_turnier_bericht($bericht, $kader_check);
    Form::affirm("Turnierbericht wurde gespeichert");
    header('Location:' . db::escape($_SERVER['PHP_SELF']) . '?turnier_id=' . $turnier_id);
    die();
}