<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/la_team_waehlen.logic.php'; //Auswahlfeld für ein Team

if (isset($_GET['team_id'])) {
    $team_id = (int)$_GET['team_id'];
    if (Team::is_ligateam($team_id)) {
        $kader = Spieler::get_teamkader($team_id); //wird an Kader-Template übergeben
        $kader_vorsaison = Spieler::get_teamkader_vorsaison($team_id); //wird an kader.logic und an template übergeben
    } else {
        Html::error("Team wurde nicht gefunden");
    }
}

//Formularauswertung neuer Spieler
require_once '../../logic/kader.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/la_team_waehlen.tmp.php';

if (isset($kader)) {
    include '../../templates/kader.tmp.php';
}

include '../../templates/footer.tmp.php';

