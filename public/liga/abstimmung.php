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

    <h1 class="w3-text-primary">Abstimmung Finalturniermodus</h1>

        <!-- Informationstext für die Abstimmung -->
        Der Ligaausschuss hat sich in den letzten Monaten intensiv mit dem Modus der Deutschen Meisterschaft
        auseinandergesetzt.  Schlussendlich haben sich zwei Varianten herauskristallisiert, die der
        Ligaausschuss zusammen mit dem alten Modus zur Abstimmung stellt.

        <p><strong>Informationen</strong></p>

        <p>
            <?= Html::link(Env::BASE_URL, "Rundmail", true, "insert_drive_file") ?>
        </p>

        <p>
            <?= Html::link(Env::BASE_URL, "Anhang Beispiel-Spielpläne", true, "insert_drive_file") ?>
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