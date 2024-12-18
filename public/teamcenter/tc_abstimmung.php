<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

if (!Abstimmung::darf_abstimmen($teamEntity->id())) {
    Html::notice("Nur Teams bis Platz 24 in der Rangtabelle des 13. Spieltages können für das Finalturnier abstimmen.");
    Helper::reload("/teamcenter/tc_start.php");
}

require_once '../../logic/abstimmung.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>
    <div class="w3-card-4 w3-panel">

        <h1 class="w3-text-primary">Abstimmung Finalturniermodus</h1>
        <?php Html::message('notice',
            "Die Abstimmung startet hier am " . date("d.m.Y", $beginn) . " und endet am "
            . date("d.m.Y \u\m H:i", $abschluss) . " Uhr. "
            . "Es haben bisher " . $ergebnisse["gesamt"] . " von " . Abstimmung::ANZAHL_TEAMS . " Teams abgestimmt.",
            Null); ?>
        <!-- Informationstext für die Abstimmung -->
        Der Ligaausschuss hat sich in den letzten Monaten intensiv mit dem Modus der Deutschen Meisterschaft
        auseinandergesetzt.  Schlussendlich haben sich zwei Varianten herauskristallisiert, die der
        Ligaausschuss zusammen mit dem alten Modus zur Abstimmung stellt.

        <p><strong>Informationen</strong></p>

        <p>
            <?= Html::link(Env::BASE_URL . "/dokumente/abstimmung_finalmodi/rundschreiben.pdf",
                "Rundmail", true, "insert_drive_file") ?>
        </p>

        <p>
            <?= Html::link(Env::BASE_URL . "/dokumente/abstimmung_finalmodi/finalmodi_beispielhaft.pdf",
                "Anhang Beispiel-Spielpläne", true, "insert_drive_file") ?>
        </p>

        <p><strong>Verbleibende Zeit</strong></p>

        <?php Html::countdown(strtotime(Abstimmung::ENDE)) ?>

        <p><strong>Fragen</strong></p>
        <p>
            <?= Html::link(Nav::LINK_FORUM, "Forum", "true", "chat") ?>
            oder <?= Html::mailto(Env::LAMAIL) ?>
        </p>

    </div>

    <!-- Nach Beginn der Abstimmung -->
    <?php if (time() > $beginn): ?>
        <!-- Informationstext für die Stimmeinsicht -->
        <div class="w3-card-4 w3-panel">
            <h2 class="w3-text-primary">Status</h2>

            <?php if (empty($abstimmung->team)): ?>
                <!-- Noch nicht abgestimmt hinterlegt -->
                <p class="w3-text-secondary">Es ist keine Stimme für dein Team hinterlegt.</p>
            <?php else: ?>
                <p class="w3-text-green">Es wurde eine Stimme für dein Team hinterlegt.
                                         (<?= $abstimmung->team['aenderungen'] ?> mal geändert)</p>
                <?php if (empty($einsicht)): ?>
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
                <?php else: ?>
                    <p id="stimme">Dein Team hat wie folgt abgestimmt:</p>
                    <p>
                        <?= Abstimmung::OPTIONS[$einsicht] ?? "<span class='w3-text-red'>Fehler, bitte melde dich bei </span>" . Html::mailto(Env::TECHNIKMAIL) ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Während der Abstimmung -->
    <?php if (time() > $beginn && time() < $abschluss): ?>
        <!-- Formular zur Stimmabgabe -->
        <div class="w3-card-4 w3-panel">
            <h2 class="w3-text-primary"><?= (empty($abstimmung->team)) ? 'Jetzt abstimmen' : 'Stimme ändern' ?></h2>
            <form method="post">
                <?php foreach (Abstimmung::OPTIONS as $id => $option): ?>
                    <p class="w3-hover-text-primary">
                        <!-- Erste Antwortmöglichkeit -->
                        <input required
                               type="radio"
                               name="abstimmung"
                               id="<?= $id ?>"
                               value="<?= $id ?>"
                               class="w3-radio"
                        >
                        <label style="cursor: pointer;" for="<?= $id ?>"><?= $option ?></label>
                    </p>
                <?php endforeach; ?>
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
                        <?php if (empty($abstimmung->team)): ?>
                            Stimme abgeben
                        <?php else: ?>
                            Stimme ändern
                        <?php endif; ?>
                    </button>
                </p>
            </form>
        </div>
        <div class="w3-card-4 w3-panel">

            <!-- Hinweise zur Abstimmung -->
            <h2 class="w3-text-primary">Hinweise zur Abstimmung</h2>
            <p>
                <i>
                    Die Abstimmung ist anonym - die Stimmzuordnung wird mit eurem Teamcenter-Passwort verschlüsselt.
                    Eine Einsicht und Änderung eurer abgegebenen Stimme ist im Nachhinein möglich.
                </i>
            </p>
            <p>
                <i>
                    Dafür muss immer das Teamcenter-Passwort eingegeben werden, mit welchem ihr das erste Mal abgestimmt habt
                    (unabhängig davon, ob es danach geändert wurde). Ohne euer Passwort ist eine Einsicht oder Änderung eurer
                    Stimme nicht mehr möglich.
                </i>
            </p>
        </div>

    <?php endif; ?>

    <!-- NACH DER ABSTIMMUNG -->
    <?php if (time() > $abschluss): ?>
        <div class="w3-panel w3-light-grey">
            <p class="w3-large">Die Abstimmung ist beendet.</p>
            <p><?= Html::link('../liga/abstimmung.php', "<i class='material-icons'>info</i> Zu den Ergebnissen") ?></p>
        </div>
    <?php endif; ?>

<?php
include '../../templates/footer.tmp.php';