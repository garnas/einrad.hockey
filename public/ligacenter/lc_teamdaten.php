<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

//speichert die teamid des ausgewählten Teamnamens in der Session ab, wenn ein Teamname ausgewählt wurde
//Formularauswertung Teamauswahl
require_once '../../logic/la_team_waehlen.logic.php';

if(isset($_GET['team_id'])){
    $team_id = $_GET['team_id'];
    $teamname  = Team::teamid_to_teamname($team_id);
    if (!empty($teamname)){
        $akt_team = new Team ($team_id);
        $team_kontakte = new Kontakt($team_id);

        //Werden an teamdaten.tmp.php übergeben
        $emails = $team_kontakte->get_emails_with_details();
        $daten = $akt_team->get_details();
    }else{
        $daten = '';
        $emails = '';
    }
}
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/la_team_waehlen.tmp.php'; //Formular zur Teamauswahl
include '../../templates/teamdaten.tmp.php'; //Anzeige der Teamdaten des ausgewählten Teams
?>

<!-- Navigationsbuttons -->
<div class="w3-panel w3-card-4 w3-white"> 
    <p>
        <a class="w3-button w3-secondary w3-block" href='lc_teamdaten_aendern.php?team_id=<?=$team_id ?? ''?>'><i class="material-icons" style="visibility: hidden">chevron_left</i>Ändern<i class="material-icons">chevron_right</i></a>
    </p>
    <p>
        <a class="w3-button w3-primary w3-block" href="lc_start.php"><i class="material-icons">chevron_left</i>Zurück<i class="material-icons" style="visibility: hidden">chevron_right</i></a>
    </p>
</div>

<?php include '../../templates/footer.tmp.php';?>