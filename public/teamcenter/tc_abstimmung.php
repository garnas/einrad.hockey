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
    <h1 class="w3-text-primary">Abstimmung Saisonrhythmus</h1>
    <!-- Bereich der sich je nach Zeitpunkt in der Abstimmung ändert -->

    <!-- Vor der Abstimmung -->
    <?php if (time() < $beginn) { ?>
        <div class="w3-panel w3-light-grey">
            <p class="w3-large">Die Abstimmung startet am <?= date("d.m.Y \u\m H:i", $beginn) ?> Uhr.</p>
        </div>
    <?php } //endif ?>

    <!-- Nach Beginn der Abstimmung -->
<?php if (time() > $beginn) { ?>
    <!-- Informationstext für die Abstimmung -->
    <?php Form::schreibe_attention(
        "Die Abstimmung endet am " . date("d.m.Y \u\m H:i", $abschluss) . " Uhr. "
        . "Es haben bisher $abgegebene_stimmen von $anzahl_teams Teams abgestimmt.", ''); ?>
    <!-- Informationstext für die Stimmeinsicht -->
    <div class="w3-card-4 w3-panel w3-light-grey">
        <h2 class="w3-text-primary">Status</h2>
        <?php if (empty($abstimmung->team)) { ?>
            <!-- Noch nicht abgestimmt hinterlegt -->
            <p class="w3-text-secondary">Es ist keine Stimme für dein Team hinterlegt.</p>
        <?php } else { ?>
        <p class="w3-text-green">Es wurde eine Stimme für dein Team hinterlegt. (<?= $abstimmung->team['aenderungen'] ?> mal geändert)</p>
        <?php if (empty($stimme)) { ?>
            <!-- Formular zur Stimmeinsicht -->
            <form method="post">
                <p>
                    <!-- Passwort -->
                    <label for="passwort_einsicht" style="cursor: pointer;">
                        Bitte Teamcenter-Passwort eingeben, um deine Stimme einzusehen:
                    </label>
                    <input required
                           type="password"
                           name="passwort"
                           id="passwort_einsicht"
                           placeholder="Passwort eingeben"
                           class="w3-input"
                    >
                </p>
                <p>
                    <button type="submit"
                            name="stimme_einsehen"
                            class="w3-primary w3-button"
                    >
                        <i class="material-icons">info</i>
                        Stimme einsehen
                    </button>
                </p>
            </form>
        <?php } else { ?>
            <p id="stimme">Dein Team hat wie folgt abgestimmt:</p>
            <p><?= $display_ergebnisse[$stimme]['formulierung'] ?></p>
        <?php } //endif Team will Stimme einsehen?>
    <?php } //endif Team hat abgestimmt?>
    </div>
<?php } //endif Zeit?>

    <!-- Während der Abstimmung -->
<?php if (time() > $beginn && time() < $abschluss) { ?>
    <!-- Formular zur Stimmabgabe -->
    <div class="w3-card-4 w3-panel w3-light-grey">
        <h2 class="w3-text-primary"><?= (empty($abstimmung->team)) ? 'Jetzt abstimmen' :  'Stimme ändern' ?></h2>
        <form method="post">
            <p class="w3-large">
                Im Zuge der aktuellen Situation hat sich der Ligaausschuss mit dem Gedanken auseinandergesetzt, ob wir
                die Gelegenheit nutzen sollten, unseren bisher bewährten Saisonrhythmus zu ändern.
            </p>
            <p class="w3-large">
                <b>Es geht dabei nicht um die aktuelle Saison 2020/21, sondern um alle zukünftigen Saisons.</b>
            </p>
            <p class="w3-large">
                Da es sich hierbei um eine Entscheidung von grundlegender Bedeutung handelt, gibt es eine Abstimmung
                unter den Ligavertretern.
            </p>
            <p class="w3-hover-text-primary">
                <!-- Erste Antwortmöglichkeit -->
                <input required
                       type="radio"
                       name="abstimmung"
                       id="sommerpause"
                       value="sommerpause"
                       class="w3-radio"
                >
                <label style="cursor: pointer;" for="sommerpause">
                    Der Ligaausschuss arbeitet auf einen <b>Saisonrhythmus Sommer-Sommer</b> hin. Der Saisonwechsel
                    würde so in die Sommerferien fallen, mit Abschlussturnieren etwa im Mai/Juni.
                </label>
            </p>
            <p class="w3-hover-text-primary">
                <!-- Zweite Antwortmöglichkeit -->
                <input required
                       type="radio"
                       name="abstimmung"
                       id="winterpause"
                       value="winterpause"
                       class="w3-radio"
                >
                <label style="cursor: pointer;" for="winterpause">
                    Der bisherige Rhythmus mit einer Saison, die sich am <b>Kalenderjahr</b> orientiert, wird
                    beibehalten.
                </label>
            </p>
            <p class="w3-hover-text-primary">
                <!-- Dritte Antwortmöglichkeit -->
                <input required
                       type="radio"
                       name="abstimmung"
                       id="enthaltung"
                       value="enthaltung"
                       class="w3-radio"
                >
                <label style="cursor: pointer;" for="enthaltung">
                    Wir <b>enthalten</b> uns.
                </label>
            </p>
            <p>
                <!-- Passwort -->
                <label for="passwort" style="cursor: pointer;">
                    Bitte Teamcenter-Passwort eingeben, um abzustimmen:
                </label>
                <input required
                       type="password"
                       name="passwort"
                       id="passwort"
                       placeholder="Passwort eingeben"
                       class="w3-input"
                >
            </p>
            <p>
                <button type="submit" class="w3-block w3-primary w3-button">
                    <i class="material-icons">how_to_vote</i>
                    <?php if (empty($abstimmung->team)) { ?>
                        Stimme abgeben
                    <?php } else { ?>
                        Stimme ändern
                    <?php } //end if?>
                </button>
            </p>
        </form>
    </div>
    <!-- Hinweise zur Abstimmung -->
    <h3 class="w3-text-primary">Hinweise zur Abstimmung</h3>

        <p>
            <i>
            Die Abstimmung ist technisch anonym - die Stimmzuordnung wird mit eurem Teamcenter-Passwort verschlüsselt. Eine
            Einsicht und Änderung eurer abgegebenen Stimme ist im Nachhinein möglich.
            </i>
        </p>
        <p>
            <i>
            Dafür muss immer das Teamcenter-Passwort eingegeben werden, mit welchem ihr das erste Mal abgestimmt habt
            (unabhängig davon, ob es danach geändert wurde). Ohne euer Passwort ist eine Einsicht oder Änderung eurer Stimme
            nicht mehr möglich.
            </i>
        </p>

<?php } //endif?>

    <!-- NACH DER ABSTIMMUNG -->
<?php if (time() > $abschluss) { ?>
    <div class="w3-panel w3-light-grey">
        <p class="w3-large">Die Abstimmung ist beendet.</p>
    </div>
<?php } //endif?>

<?php
include '../../templates/footer.tmp.php';