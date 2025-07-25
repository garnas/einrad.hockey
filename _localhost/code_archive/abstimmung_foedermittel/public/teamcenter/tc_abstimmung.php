<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/abstimmung.logic.php';
$teams = TeamRepository::get()->activeLigaTeams();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>
    <div class="w3-card-4 w3-panel">

        <h1 class="w3-text-primary">Abstimmung Fördermittel</h1>
        <?php 
            if (time() < $beginn):
                Html::message('notice',
                "Die Abstimmung startet hier am " . date("d.m.Y", $beginn) . " und endet am "
                . date("d.m.Y \u\m H:i", $abschluss) . " Uhr. ",
                Null);
            endif;
        ?>
        <p><strong>Informationen</strong></p>
        <p>Wie ihr bereits gehört habt, wurde der Ligabeitrag unter anderem erhöht, um Projekte wie die Förderung von Einradhockey-Initiativen zu unterstützen. Bislang haben wir vier Anträge erhalten. Um diese Mittel gerecht und transparent zu verteilen, haben wir ein vorläufiges Budget von 3.000,00 € festgelegt, über dessen Verteilung ihr mitentscheiden sollt.</p>
        <p>Alle derzeit eingegangenen Anträge können gefördert werden, und es besteht weiterhin die Möglichkeit, weitere Anträge zu stellen, wenn ihr ein neues Projekt einbringen möchtet.</p>
        <p>Jedes Team hat eine Stimme bei der Umfrage, sodass wir sicherstellen können, dass die Entscheidung gemeinsam getroffen wird.</p>
        <p>Bitte nehmt euch einen Moment Zeit, um an der Umfrage teilzunehmen und uns eure Meinung zu den Fördermaßnahmen mitzuteilen. Die Umfrage wird bis zum  <?= date("d.m.Y", $abschluss) ?> offen sein.</p>

        <?php if (time() > $beginn && time() < $abschluss): ?>
            <p><strong>Verbleibende Zeit</strong></p>
            <?php Html::countdown(strtotime(Abstimmung::ENDE)) ?>
        <?php endif; ?>

        <p><strong>Fragen</strong></p>
        <p>
            <?= Html::link(Nav::LINK_FORUM, "Discord", "true", "chat") ?>
            oder <?= Html::mailto(Env::LAMAIL) ?>
        </p>

    </div>

    <!-- Nach Beginn der Abstimmung -->
    <?php if (time() > $beginn && time() < $abschluss): ?>
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
                    <p id="stimme">Deine Stimme wird unten im Formular angezeigt.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (time() > $beginn && time() < $abschluss): ?>
        <!-- Formular zur Stimmabgabe -->
        <div class="w3-card-4 w3-panel">
            <h2 class="w3-text-primary"><?= (empty($abstimmung->team)) ? 'Jetzt abstimmen' : 'Stimme ändern' ?></h2>
            <form method="post">
                <p>Bitte bewertet die folgenden Fördermaßnahmen nach ihrer Wichtigkeit für die Liga (5 = sehr wichtig, 1 = weniger wichtig):</p>
                <?php foreach (Abstimmung::OPTIONS_WICHTIGKEIT as $id => $option): ?>
                    <p class="w3-hover-text-primary">
                        <!-- Erste Antwortmöglichkeit -->
                        <label style="cursor: pointer;" for="<?= $id ?>"><?= $option ?></label>
                        <select
                                id="<?= $id ?>"
                                name="<?= $id ?>"
                                class="w3-select w3-border w3-border-primary">
                            <option <?= Abstimmung::selected(name: $id, value: "0", value_chosen: $einsicht[$id] ?? "") ?>
                                    value="0">Enthaltung</option>
                            <option <?= Abstimmung::selected(name: $id, value: "1", value_chosen: $einsicht[$id] ?? "") ?>
                                    value="1">1 - weniger wichtig</option>
                            <option <?= Abstimmung::selected(name: $id, value: "2", value_chosen: $einsicht[$id] ?? "") ?>
                                    value="2">2</option>
                            <option <?= Abstimmung::selected(name: $id, value: "3", value_chosen: $einsicht[$id] ?? "") ?>
                                    value="3">3</option>
                            <option <?= Abstimmung::selected(name: $id, value: "4", value_chosen: $einsicht[$id] ?? "") ?>
                                    value="4">4</option>
                            <option <?= Abstimmung::selected(name: $id, value: "5", value_chosen: $einsicht[$id] ?? "") ?>
                                    value="5">5 - sehr wichtig</option>
                        </select>
                    </p>
                <?php endforeach; ?>
                <p>Weitere Ideen und Anmerkungen:</p>
                <textarea maxlength="1000" name="Weiteres" class="w3-input w3-border w3-border-primary"><?=$einsicht['Weiteres'] ?? ""?></textarea>
                <hr>
                <p>Seid Ihr damit einverstanden, dass die jährlichen Beiträge der Mitglieder der Liga für Fördermaßnahmen eingesetzt werden, die zur Förderung der deutschen Einradhockeyliga und des Sports beitragen?</p>
                <?php foreach (Abstimmung::OPTIONS as $id => $option): ?>
                    <p class="w3-hover-text-primary">
                        <!-- Erste Antwortmöglichkeit -->
                        <input required
                               <?= Abstimmung::selected(name: "option", value: $id, value_chosen: $einsicht["option"] ?? null) ?>
                               type="radio"
                               name="option"
                               id="<?= $id ?>"
                               value="<?= $id ?>"
                               class="w3-radio"
                        >
                        <label style="cursor: pointer;" for="<?= $id ?>"><?= $option ?></label>
                    </p>
                <?php endforeach; ?>
                <hr>
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
                    <button type="submit" name="abgestimmt" class="w3-block w3-primary w3-button">
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