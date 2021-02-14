<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/abstimmung.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$titel = "Saisonrhythmus Abstimmung | Deutsche Einradhockeyliga";
Config::$content = "Das aktuelle Abstimmungsergebnis der Teams über einen Saisonrhythmus-Wechsel.";
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Abstimmung Saisonwechsel</h1>

<p>Schon häufig gab es in der Liga Stimmen, welche sich für eine Änderung des Saisonrhythmus einsetzten. Darüber lässt der Ligaausschuss nun unter den Ligavertretern abstimmen.</p>
    <p>Mehr Infos: <?=Form::link("../dokumente/rundmails/rundmail_saisonwechsel.pdf", "<i class='material-icons'>insert_drive_file</i> Rundmail bezüglich der Abstimmung", true)?></p>

<?php
if (time() > strtotime(Abstimmung::ENDE)){
    include '../../templates/abstimmung_ergebnis.tmp.php';
}else{
    Form::message('notice',
            "Das Abstimmungsergebnis wird hier am " . Abstimmung::ENDE . " Uhr veröffentlicht.", "");
?>

    <a href="../teamcenter/tc_abstimmung.php" class="w3-button w3-section w3-block w3-primary">
        <i class="material-icons">how_to_vote</i> Jetzt abstimmen!
    </a>

<?php } //end if
include '../../templates/footer.tmp.php';