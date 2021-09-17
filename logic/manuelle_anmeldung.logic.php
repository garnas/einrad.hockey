<?php
//Turnierobjekt erstellen
$turnier_id = (int) @$_GET['turnier_id'];
$turnier = nTurnier::get($turnier_id);

//Existiert das Turnier?
if (empty($turnier->get_turnier_id())){
    Html::error("Turnier wurde nicht gefunden");
    header('Location: ../liga/turniere.php');
    die();
}

//im Teamcenter testen, ob es sich um den Ausrichter handelt
if (Helper::$teamcenter && ($turnier->get_ausrichter() != $_SESSION['logins']['team']['id'] || $turnier->get_art() != 'spass')){
    Html::error("Fehlende Berechtigung Teams zu diesem Turnier anzumelden");
    header('Location: ../liga/turniere.php');
    die();
}

//Autor für Logs
if (Helper::$teamcenter) {
    $autor = $_SESSION['logins']['team']['name'];
} elseif (Helper::$ligacenter) {
    $autor = "Ligaausschuss";
} else {
    Html::error("Weder im Teamcenter noch im Ligacenter angemeldet");
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
                $turnier->set_abmeldung($team['team_id']);
                if ($team['liste'] == 'warte'){
                    $turnier->warteliste_aktualisieren();
                }
                Html::info ($team['teamname'] . " wurde abgemeldet");
                header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->get_turnier_id()));
                die();
            }
        }
    }
    Html::error("Es wurde kein Team abgemeldet. Es ist ein Fehler aufgetreten.");
}

/////////////Ligateam als Ligaausschuss anmelden/////////////
if (isset($_POST['team_anmelden'])){
    $liste = $_POST['liste'];
    $teamname = $_POST['teamname'];
    $team_id = Team::name_to_id($teamname);
    $error = false;

    //Postion auf der Warteliste
    if ($liste == 'warte') {
        $pos = $_POST['pos'];
    } else {
        $pos = 0;
    }

    //Existiert der ausgewählte Teamname?
    if (empty($team_id)){
        $error = true;
        Html::error("Team wurde nicht gefunden");
    }

    //Ist das Team bereits angemeldet?
    if ($turnier->is_angemeldet($team_id)){
        $error = true;
        Html::error("Team ist bereits angemeldet");
    }

    if (!$error){
        $turnier->set_team($team_id, $liste, $pos);
        Html::info ("$teamname wurde angemeldet");
        header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->get_turnier_id()));
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
    $team_id = Team::name_to_id($teamname . '*');
    if (!$turnier->is_angemeldet($team_id ?? 0)){
        $turnier->set_nl_team($teamname, $liste, $pos);
        Html::info("$teamname wurde angemeldet auf Liste: $liste");
        header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->get_turnier_id()));
        die();
    }else{
        Html::error("Ein Nichtligateam mit diesem Namen ist bereits angemeldet");
    }
}

/////////////Warteliste neu Durchnummerieren/////////////
if (isset($_POST['warteliste_aktualisieren'])){

    $turnier->warteliste_aktualisieren();
    //Log wird automatisch in der Funktion geschrieben, Argument: Autor

    Html::info("Warteliste wurde aktualisiert");
    header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->get_turnier_id()));
    die();
}

/////////////Spielen-Liste von der Warteliste neu auffuellen/////////////
if (isset($_POST['spieleliste_auffuellen'])){
    $error = false;

    //Hat das Turnier noch freie Plätze?
    if ($turnier->get_freie_plaetze() <= 0){
        $error = true;
        Html::error("Spielen-Liste ist bereits voll");
    }

    //Ist das Turnier in der Meldephase?
    if ($turnier->details['phase'] != 'melde'){
        $error = true;
        Html::error("Turnier befindet sich nicht in der Meldephase");
    }
    
    if (!$error){
        $turnier->spieleliste_auffuellen("Ligaausschuss");
        Html::info("Spielen-Liste wurde aufgefüllt");
        header('Location: ' . db::escape($_SERVER['PHP_SELF'] . '?turnier_id=' . $turnier->get_turnier_id()));
        die();
    }else{
        Html::error('Spielen-Liste wurde nicht aufgefüllt');
    }
}
