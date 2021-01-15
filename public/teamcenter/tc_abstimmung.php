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
    <h1 class="w3-text-primary">Abstimmung</h1>

    <!-- Panel mit dem Hinweis für den Abschluss der Befragung -->
    <?php Form::schreibe_attention("Die Abstimmung endet am " . date("d.m.Y H:i", $abschluss) . " Uhr", ''); ?>.

    <!-- Informationstext für die Abstimmung -->
    <div class="w3-section">
        <p>
            Die Abstimmung erfolgt technisch anonym - die Stimmzuordnung wird mit eurem Teamcenter-Passwort verschlüsselt.
        </p>
        <p class="w3-text-grey">
            <i>
                Ohne euer Passwort ist eine Einsicht oder Änderung eurer Stimme nicht mehr möglich. Es muss immer das
                Teamcenter-Passwort eingegeben werden, mit welchem ihr das erste Mal abgestimmt habt (unabhängig davon,
                ob es danach geändert wurde).
            </i>
        </p>
    </div>

    <!-- Bereich der sich je nach Zeitpunkt in der Abstimmung ändert -->
    <div class="w3-panel w3-light-grey">
        <p class="w3-large">
            Status
        </p>
            <!-- VOR DER ABSTIMMUNG -->
        <?php if ($uhrzeit < $beginn) { ?>
            <p class="w3-large">Die Abstimmung startet am <?= date("d.m.Y", $beginn) ?> um <?= date("H:i", $beginn) ?>
                Uhr.</p>
            <!-- WÄHREND DER ABSTIMMUNG -->
        <?php } elseif ($uhrzeit < $abschluss) { ?>
                <?= Form::link("../liga/abstimmung.php", "<i class='material-icons'>info </i> Ergebnis einsehen")?>
                <?php if (empty($abstimmung->team)){ ?>
                    <!-- Noch nicht abgestimmt hinterlegt -->
                    <p class="w3-text-secondary">Dein Team "<?=$_SESSION['teamname']?>" hat noch nicht abgestimmt.</p>
                <?php }else{?>
                    <p class="w3-text-green"><b>Es wurde eine Stimme für dein Team hinterlegt.</b></p>
                    <p>Dein Team hat seine Stimme <?=$abstimmung->team['aenderungen']?> mal im Nachhinein geändert.</p>
                    <?php if (empty($stimme)){ ?>
                        <!-- Formular zur Stimmeinsicht -->
                        <form method="post">
                            <p>
                                <!-- Passwort -->
                                <label for="passwort_einsicht" style="cursor: pointer;">
                                    Bitte Teamcenter-Passwort eingeben, um deine Stimme einzusehen:
                                </label>
                                <input required type="password" name="passwort" id="passwort_einsicht" placeholder="Passwort eingeben"
                                       class="w3-input">
                            </p>
                            <p>
                                <button type="submit" name="stimme_einsehen" class="w3-block w3-primary w3-button">
                                    <i class="material-icons">info</i>
                                    Stimme einsehen
                                </button>
                            </p>
                        </form>
                    <?php }else{?>
                        <p id="stimme">Dein Team hat wie folgt abgestimmt: <b><?=ucfirst($stimme)?></b></p>
                    <?php } //endif?>
                <?php } //endif?>
    </div>
    <div class="w3-panel w3-light-grey">
            <!-- Formular zur Stimmabgabe -->
            <form method="post">
                <p class="w3-large">
                    Hier kann ein Fragetext rein.
                </p>
                <p>
                    <!-- Passwort -->
                    <label for="passwort" style="cursor: pointer;">
                        Bitte Teamcenter-Passwort eingeben, um abzustimmen:
                    </label>
                    <input required type="password" name="passwort" id="passwort" placeholder="Passwort eingeben"
                           class="w3-input">
                </p>
                <p class="w3-hover-text-primary">
                    <!-- Erste Antwortmöglichkeit -->
                    <input required type="radio" name="abstimmung" id="sommerpause" value="sommerpause" class="w3-radio">
                    <label style="cursor: pointer;" for="sommerpause">
                        Wir sprechen uns für eine Änderung hin zu einer <b>Saisonpause im Sommer</b> aus.
                    </label>
                </p>
                <p class="w3-hover-text-primary">
                    <!-- Zweite Antwortmöglichkeit -->
                    <input required type="radio" name="abstimmung" id="winterpause" value="winterpause" class="w3-radio">
                    <label style="cursor: pointer;" for="winterpause">
                        Wir sprechen uns für einen Erhalt des bisherigen Saisonverlaufs mit einer
                        <b>Saisonpause im Winter</b> aus.
                    </label>
                </p>
                <p class="w3-hover-text-primary">
                    <!-- Dritte Antwortmöglichkeit -->
                    <input required type="radio" name="abstimmung" id="enthaltung" value="enthaltung" class="w3-radio">
                    <label style="cursor: pointer;" for="enthaltung">
                        Wir <b>enthalten</b> uns.
                    </label>
                </p>
                <p>
                    <button type="submit" class="w3-block w3-primary w3-button">
                        <i class="material-icons">how_to_vote</i>
                        <?php if (empty($abstimmung->team)){ ?>
                            Stimme abgeben
                        <?php }else{ ?>
                            Stimme ändern
                        <?php } //end if?>
                    </button>
                </p>
            </form>
            <!-- NACH DER ABSTIMMUNG -->
        <?php } elseif ($uhrzeit > $abschluss) { ?>
            <p class="w3-large">Die Abstimmung ist beendet.</p>
        <?php } ?>
    </div>


<?php
include '../../templates/footer.tmp.php';