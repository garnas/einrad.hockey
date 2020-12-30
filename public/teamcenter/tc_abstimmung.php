<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/abstimmung.logic.php';


/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<!-- Panel mit dem Hinweis für den Abschluss der Befragung -->
<div class="w3-panel w3-pale-yellow w3-leftbar w3-border-yellow">
    <p>Die Abstimmung endet am <?=date("d.m.Y", $abschluss)?> um <?=date("H:i", $abschluss)?> Uhr.</p>
</div>

<h1 class="w3-text-primary">Abstimmung</h1>

<!-- Informationstext für die Abstimmung -->
<p>Hier kann dann noch ein Text eingefügt werden</p>

<!-- Bereich der sich je nach Zeitpunkt in der Abstimmung änder -->
<div class="w3-panel w3-light-grey w3-padding-32">
    <?php if ($beginn >= $abschluss) { ?>
        <p style="text-transform: uppercase;" class="w3-large w3-text-red">Fehler bei der Einrichtung der Abstimmung</p>
        <?=Form::error("Fehler bei der Einrichtung der Abstimmung!");?>
    <!-- VOR DER ABSTIMMUNG -->
    <?php } elseif ($uhrzeit < $beginn) { ?>
        <p class="w3-large">Die Abstimmung startet am <?=date("d.m.Y", $beginn)?> um <?=date("H:i", $beginn)?> Uhr.</p>
    <!-- WÄHREND DER ABSTIMMUNG -->
    <?php } elseif ($uhrzeit < $abschluss) { ?>
        <!-- Formular zur Stimmabgabe -->
        <form method="post">
            <p class="w3-large">
                Hier kann ein Fragetext rein.
            </p>
            <p">
                <!-- Erste Antwortmöglichkeit -->
                <input type="radio" name="abstimmung" id="sommerpause" value="sommerpause" class="w3-radio">
                <label for="sommerpause">Wir sprechen uns für eine Änderung hin zu einer <b>Saisonpause im Sommer</b> aus.</label>
            </p>
            <p>
                <!-- Zweite Antwortmöglichkeit -->
                <input type="radio" name="abstimmung" id="winterpause" value="winterpause" class="w3-radio">
                <label for="winterpause">Wir sprechen uns für einen Erhalt des bisherigen Saisonverlaufs mit einer <b>Saisonpause im Winter</b> aus.</label>
            </p>
            <p>
                <!-- Dritte Antwortmöglichkeit -->
                <input type="radio" name="abstimmung" id="enthaltung" value="enthaltung" class="w3-radio">
                <label for="enthaltung">Wir <b>enthalten</b> uns.</label>
            </p>
            <p>
                <button style='cursor: pointer; border: 0px;' class="w3-block w3-primary w3-padding"><i class="material-icons">how_to_vote</i> Stimme abgeben!</button>
            </p>
        </form>
    <!-- NACH DER ABSTIMMUNG -->
    <?php } elseif ($uhrzeit > $abschluss) { ?>
        <p class="w3-large">Die Abstimmung ist beendet.</p>
    <?php } ?>
</div>






<?php
include '../../templates/footer.tmp.php';