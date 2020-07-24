<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/la_team_waehlen.logic.php'; //Auswahlfeld für ein Team

if (isset($_GET['team_id'])){
    $team_id = $_GET['team_id'];
    if (empty(Team::teamid_to_teamname($team_id))){
        Form::error("Team wurde nicht gefunden");
    }else{
        $kader = Spieler::get_teamkader($team_id); //wird an Kader-Template übergeben
        $kader_vorsaison = Spieler::get_teamkader_vorsaison($team_id); //wird an kader.logic und an template übergeben
    }
}

//Formularauswertung neuer Spieler
require_once '../../logic/kader.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/la_team_waehlen.tmp.php';

if (Team::is_ligateam($team_id ?? '')){
    include '../../templates/kader.tmp.php';
}

include '../../templates/footer.tmp.php';

