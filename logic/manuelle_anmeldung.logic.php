<?php
//Turnierobjekt erstellen
$turnier_id = (int) $_GET['turnier_id'];
$turnier = new Turnier ($turnier_id);

//Existiert das Turnier?
if (empty($turnier->details)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location: ../liga/turniere.php');
    die();
}
//im Teamcenter testen, ob es sich um den Ausrichter handelt
if ($teamcenter && ($turnier->details['ausrichter'] != $_SESSION['team_id'] or $turnier->details['art'] != 'spass')){
    Form::error("Fehlende Berechtigung Teams zu diesem Turnier anzumelden");
    header('Location: ../liga/turniere.php');
    die();
} 

//Autor für Logs
if($teamcenter){
    $autor = $_SESSION['teamname'];
}elseif ($ligacenter){
    $autor = "Ligaausschuss";
}else{
    Form::error("Weder im Teamcenter noch im Ligacenter angemeldet");
    header('Location: ../liga/turniere.php');
    die();
}

//Turnieranmeldungen bekommen
$anmeldungen = $turnier->get_anmeldungen();

//Formularauswertung

/////////////Team als Ligaausschuss abmelden/////////////
if (isset($_POST['abmelden'])){
    foreach ($anmeldungen as $liste) {
        foreach ($liste as $team) {
            if (isset($_POST['abmelden' . $team['team_id']])){
                $turnier->abmelden($team['team_id']);
                $turnier->log("Abmeldung: " . $team['teamname'] . "\r\nvon Liste: " . $team['liste'], $autor);
                if ($team['liste'] == 'warte'){
                    $turnier->warteliste_aktualisieren();
                }
                Form::affirm ($team['teamname'] . " wurde abgemeldet");
                header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->details['turnier_id']));
                die();
            }
        }
    }
    Form::error("Es wurde kein Team abgemeldet. Es ist ein Fehler aufgetreten.");  
}

/////////////Ligateam als Ligaausschuss anmelden/////////////
if (isset($_POST['team_anmelden'])){
    $liste = $_POST['liste'];
    $teamname = $_POST['teamname'];
    $team_id = Team::teamname_to_teamid($teamname);
    $error = false;

    //Postion auf der Warteliste
    if ($liste == 'warte'){
        $pos = $_POST['pos'];
    }else{
        $pos = 0;
    }

    //Existiert der ausgewählte Teamname?
    if (empty($team_id)){
        $error = true;
        Form::error("Team wurde nicht gefunden");
    }

    //Ist das Team bereits angemeldet?
    if ($turnier->check_team_angemeldet($team_id)){
        $error = true;
        Form::error("Team ist bereits angemeldet");
    }

    if (!$error){
        $turnier->team_anmelden($team_id, $liste, $pos);
        $turnier->log("Anmeldung: $teamname\r\nTeamblock: " . (Tabelle::get_team_block($team_id) ?: 'NL') . " Turnierblock: " . $turnier->details['tblock'] ."\r\nListe: $liste (WartePos: $pos)", $autor);
        Form::affirm ("$teamname wurde angemeldet");
        header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->details['turnier_id']));
        die();
    }
}

/////////////Nichtligateam anmelden/////////////
if (isset($_POST['nl_anmelden'])){
    $liste = $_POST['nl_liste'];
    $teamname = $_POST['nl_teamname'];

    if ($liste == 'warte'){
        $pos = $_POST['nl_pos'];
    }else{
        $pos = 0;
    }

    //Check ob schon ein Nichtligateam mit diesem Namen in der Datenbank existiert
    //Nichtligateams bekommen immer einen Stern hinter ihrem Namen
    $team_id = Team::teamname_to_teamid($teamname . '*');
    if (!$turnier->check_team_angemeldet($team_id)){
        $turnier->nl_anmelden($teamname, $liste, $pos);
        $turnier->log("Anmeldung: $teamname*\r\nTeamblock: " . (Tabelle::get_team_block($team_id) ?: 'NL') . "\r\nListe:  $liste (WartePos: $pos)", $autor);
        Form::affirm("$teamname wurde angemeldet auf Liste: $liste");
        header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->details['turnier_id']));
        die();
    }else{
        Form::error("Ein Nichtligateam mit diesem Namen ist bereits angemeldet");
    }
}

/////////////Warteliste neu Durchnummerieren/////////////
if (isset($_POST['warteliste_aktualisieren'])){

    $turnier->warteliste_aktualisieren("Ligaausschuss");
    //Log wird automatisch in der Funktion geschrieben, Argument: Autor

    Form::affirm("Warteliste wurde aktualisiert");
    header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->details['turnier_id']));
    die();
}

/////////////Spielen-Liste von der Warteliste neu auffuellen/////////////
if (isset($_POST['spieleliste_auffuellen'])){
    $error = false;

    //Hat das Turnier noch freie Plätze?
    if ($turnier->get_anzahl_freie_plaetze() <= 0){
        $error = true;
        Form::error("Spielen-Liste ist bereits voll");
    }

    //Ist das Turnier in der Meldephase?
    if ($turnier->details['phase'] != 'melde'){
        $error = true;
        Form::error("Turnier befindet sich nicht in der Meldephase");
    }
    
    if (!$error){
        $turnier->spieleliste_auffuellen("Ligaausschuss");
        Form::affirm("Spielen-Liste wurde aufgefüllt");
        header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->details['turnier_id']));
        die();
    }else{
        Form::error('Spielen-Liste wurde nicht aufgefüllt');
    }
}
