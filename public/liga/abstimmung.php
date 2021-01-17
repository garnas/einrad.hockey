<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
$teamcenter = $ligacenter = false;
require_once '../../logic/abstimmung.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Saisonrhythmus Abstimmung | Deutsche Einradhockeyliga";
$content = "Das aktuelle Abstimmungsergebnis der Teams über einen Saisonrhythmus-Wechsel.";
include '../../templates/header.tmp.php';

if (time() > strtotime(Abstimmung::ENDE)){
    include '../../templates/abstimmung_ergebnis.tmp.php';
}else{
    Form::schreibe_attention(
            "Das Abstimmungsergebnis wird hier am " . Abstimmung::ENDE . " Uhr veröffentlicht.");
?>

    <a href="../teamcenter/tc_abstimmung.php" class="w3-button w3-section w3-block w3-primary">
        <i class="material-icons">how_to_vote</i> Jetzt abstimmen!
    </a>

<?php
} //end if
include '../../templates/footer.tmp.php';