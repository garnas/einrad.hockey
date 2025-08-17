<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/abstimmung.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Saisonrhythmus Abstimmung | Deutsche Einradhockeyliga";
Html::$content = "Das aktuelle Abstimmungsergebnis der Teams über einen Saisonrhythmus-Wechsel.";
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Abstimmung Saisonwechsel</h1>

<p>Schon häufig gab es in der Liga Stimmen, welche sich für eine Änderung des Saisonrhythmus einsetzten. Darüber lässt der Ligaausschuss nun unter den Ligavertretern abstimmen.</p>
    <p>
        Mehr Infos: <?=Html::link("../dokumente/rundmails/rundmail_saisonwechsel.pdf",
            "Rundmail bezüglich der Abstimmung",
            true,
            "insert_drive_file")?>
    </p>

<?php
if (time() > strtotime(Abstimmung::ENDE)){
    include '../../templates/abstimmung_ergebnis.tmp.php';
}else{
    Html::message('notice',
            "Das Abstimmungsergebnis wird hier am " . Abstimmung::ENDE . " Uhr veröffentlicht.", "");
?>

    <a href="../teamcenter/tc_abstimmung.php" class="w3-button w3-section w3-block w3-primary">
        <?= Html::icon("how_to_vote") ?> Jetzt abstimmen!
    </a>

<?php } //end if
include '../../templates/footer.tmp.php';