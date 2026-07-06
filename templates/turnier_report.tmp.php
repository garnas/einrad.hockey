<?php

use App\Entity\Team\Spieler;
use App\Service\Team\SpielerService;
use App\Service\Team\TeamSnippets;
use App\Service\Turnier\TurnierSnippets;

?>

<h2 class="w3-text-grey">Turnierreport</h2>
<h1 class="w3-text-primary">
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($turnier->getSaison())?></p>

<?php $turnier_datum = DateTimeImmutable::createFromMutable($turnier->getDatum());
if ($turnier_datum->modify("-8 days") < new DateTime()): ?>
    <!-- Kader -->
    <div class="w3-card-4 w3-panel w3-padding-24">
        <h3 class="w3-text-secondary">Kader und Schiedsrichter</h3>
        <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
            <p>
                Klicke auf die Teamnamen, um den Kader anzeigen zu lassen. Nur eingetragenen Spielerinnen und Spieler sind für das Team spielberechtigt.
            </p>
        </div>
        <?php foreach ($kader_array as $team_id => $kader): ?>
            <button 
                onclick="open_kader(<?=  $team_id ?>)" 
                class="w3-button w3-block w3-left-align w3-primary w3-border-bottom w3-border-white"
            >
                <?=Team::id_to_name($team_id)?>
            </button>
            
            <?php if (!empty($kader)): ?>
                <?php $schiri = false ?>
                <div id="kader_<?= $team_id ?>" class="w3-hide">
                    <ul style="column-count: 2">
                        <?php foreach ($kader as $spieler): ?>
                            <li>
                                <?= $spieler->getName(fullName: false) ?><?= SpielerService::isSchiri($spieler) ? "*" : "" ?>
                            </li>
                            <?php $schiri = $schiri || SpielerService::isSchiri($spieler) ?>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($schiri): ?>
                        <p class="w3-center">* Gültige Schirilizenz vorhanden.</p>
                    <?php endif; ?>
                </div>
            <?php elseif (!Team::is_ligateam($team_id)): ?>
                <div>
                    Nichtligateams haben keinen zugewiesenen Kader.
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Spielerausleihe -->
<div class="w3-card-4 w3-panel w3-padding-24">
    <h3 class="w3-text-secondary">Ausleihe</h3>
    <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
        <p>
            Ausleihen von Spielerinnen und Spielern sind vor dem Turnierbeginn zu prüfen und von dem leihenden Team anzumelden (Ligmaodus&nbsp;2.2.5). Passiert dies verspätet, sollte es unter den besonderen Vorkommnissen aufgenommen werden.
        </p>
    </div>
    <?php if (!empty($spieler_ausleihen)): ?>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped w3-centered">
                <tr class="w3-primary">
                    <th><?= Html::icon("account_circle") ?> Spieler</th>
                    <th><?= Html::icon("add") ?> Aufnehmendes Team</th>
                    <th><?= Html::icon("remove") ?> Abgebendes Team</th>
                    <?php if ($change_tbericht): ?>
                        <th>Löschen</th>
                    <?php endif; ?>
                </tr>
                <?php foreach ($spieler_ausleihen as $ausleihe): ?>
                    <tr>
                        <td><?=$ausleihe['spieler']?></td>
                        <td><?=$ausleihe['team_auf']?></td>
                        <td><?=$ausleihe['team_ab']?></td>
                        <?php if ($change_tbericht): ?>
                            <td>
                                <form method="post">
                                    <button type="submit"
                                        class="w3-button w3-text-secondary"
                                        name="del_ausleihe_<?=$ausleihe['ausleihe_id']?>">
                                        <?= Html::icon("delete") ?>
                                    </button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p class="w3-text-grey">Es sind keine Spielerausleihen eingetragen.</p>
    <?php endif; ?>

    <!-- Spielerausleihe hinzufügen -->
    <?php if ($change_tbericht): ?> 
        <button onclick="document.getElementById('modal_ausleihe').style.display='block'"
                class="w3-section w3-button w3-tertiary">
            <?= Html::icon("save_alt") ?> Spielerausleihe hinzufügen
        </button>
        <div id="modal_ausleihe" class="w3-modal">
            <form method="post" class="w3-card-4 w3-panel w3-round w3-container w3-modal-content">
                <span onclick="document.getElementById('modal_ausleihe').style.display='none'"
                    class="w3-button w3-large w3-text-secondary w3-display-topright">
                    &times;
                </span>
                <h2 class="w3-text-primary">Spielerausleihe hinzufügen</h2>
                <p>
                    <label for="ausleihe_name">Spieler</label>
                    <input required
                        class="w3-input w3-border w3-border-primary"
                        type="text"
                        name="ausleihe_name"
                        id="ausleihe_name">
                </p>
                <p>
                    <label for="ausleihe_team_auf">Aufnehmendes Team</label>
                    <select required
                            name="ausleihe_team_auf"
                            id="ausleihe_team_auf"
                            class="w3-select w3-input w3-border w3-border-primary"
                    >
                        <option selected disabled>--</option>
                        <?php foreach ($teams as $team): ?>
                            <option><?=$team->getName()?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p>
                    <label for="ausleihe_team_ab">Abgebendes Team</label>
                    <input class="w3-input w3-border w3-border-primary" placeholder="Team eingeben" type="text" list="teams" id="ausleihe_team_ab" name="ausleihe_team_ab" required>
                    <?=Html::datalist_teams()?>
                </p>
                <p>
                    <input type="submit" value="Hinzufügen" name="new_ausleihe" class="w3-button w3-tertiary">
                </p>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- Zeitstrafen -->
<div class="w3-card-4 w3-panel w3-padding-24">
    <h3 class="w3-text-secondary">Zeitstrafen</h3>
    <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
        <p>
            Es sind alle Zeitstrafen aufzuführen, die auf dem Turnier ausgesprochen wurden. Im Turnierbericht können diese weiter ausgeführt werden. Mit den entsprechenden Schiedsrichterinnen und Schiedsrichtern sollte zusätzlich gesprochen werden.
        </p>
    </div>
    <?php if (!empty($zeitstrafen)): ?>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped w3-centered">
                <tr class="w3-primary">
                    <th><?= Html::icon("account_circle") ?> Spieler</th>
                    <th><?= Html::icon("schedule") ?> Dauer</th>
                    <th><?= Html::icon("sports_hockey") ?> Spielpaarung</th>
                    <?php if ($change_tbericht): ?>
                        <th>Löschen</th>
                    <?php endif; ?>
                </tr>
                <?php foreach ($zeitstrafen as $zeitstrafe): ?>
                    <tr>
                        <td><?=$zeitstrafe['spieler']?></td>
                        <td><?=$zeitstrafe['dauer']?></td>
                        <td><?=$zeitstrafe['team_a']?> : <?=$zeitstrafe['team_b']?></td>
                        <?php if ($change_tbericht): ?>
                            <td>
                                <form method="post">
                                    <button type="submit"
                                        class="w3-button w3-text-secondary"
                                        name="del_zeitstrafe_<?=$zeitstrafe['zeitstrafe_id']?>"
                                    >
                                        <?= Html::icon("delete") ?>
                                    </button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td colspan="5" class="w3-left-align">
                            <span class="w3-text-secondary">Grund: </span>
                            <?= nl2br($zeitstrafe['grund'])?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php else: ?>
        <p class="w3-text-grey">Es sind keine Zeitstrafen eingetragen.</p>
    <?php endif; ?>

    <!-- Zeitstrafe hinzufügen -->
    <?php if ($change_tbericht): ?>
        <button onclick="document.getElementById('modal_zeitstrafe').style.display='block'"
                class="w3-section w3-button w3-tertiary">
            <?= Html::icon("save_alt") ?> Zeitstrafe hinzufügen
        </button>
        <div id="modal_zeitstrafe" class="w3-modal">
            <form method="post" class="w3-card-4 w3-panel w3-round w3-container w3-modal-content">
                <span onclick="document.getElementById('modal_zeitstrafe').style.display='none'" class="w3-button w3-large w3-text-secondary w3-display-topright">&times;</span>
                <h2 class="w3-text-primary">Zeitstrafe hinzufügen</h2>
                <p>
                    <label for="zeitstrafe_spieler">Spieler</label>
                    <input type="text" placeholder="Name eingeben" class="w3-input w3-border w3-border-primary" list="spielerliste" id="zeitstrafe_spieler" name="zeitstrafe_spieler">
                        <datalist id="spielerliste">
                            <?php
                            foreach ($spieler_liste as $spieler): ?>
                                <option value='<?= $spieler->getName(fullName: false) ?> | <?= $spieler->getTeam()->getName() ?>'>
                            <?php endforeach; ?>
                        </datalist>
                </p>
                <p>
                    <label for="zeitstrafe_dauer">Dauer</label>
                    <select name="zeitstrafe_dauer" id="zeitstrafe_dauer" class="w3-select w3-input w3-border w3-border-primary">
                        <option>2 min</option>
                        <option>5 min</option>
                        <option>Gesamtes Spiel</option>
                    </select>
                </p>
                <p>
                    <label for="zeitstrafe_team_a">Spielpaarung</label>
                    <select id="zeitstrafe_team_a"
                            name="zeitstrafe_team_a"
                            class="w3-select w3-input w3-border w3-border-primary"
                            required
                    >
                        <option disabled selected value="">--</option>
                        <?php foreach ($teams as $team): ?>
                            <option><?= $team->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="zeitstrafe_team_b" class="w3-text-grey">versus</label>
                    <select id="zeitstrafe_team_b"
                            name="zeitstrafe_team_b"
                            class="w3-select w3-input w3-border w3-border-primary"
                            required
                    >
                        <option disabled selected value="">--</option>
                        <?php foreach ($teams as $team): ?>
                            <option><?= $team->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <div>
                    <label for="zeitstrafe_bericht">Grund <i>(kurz)</i></label>
                    <textarea class="w3-input w3-border w3-border-primary" onkeyup="woerter_zaehlen(300, 'zeitstrafe_bericht','zeitstrafe_counter');" maxlength="300" rows="3" id="zeitstrafe_bericht" name="zeitstrafe_bericht" required><?=stripcslashes($_POST['text'] ?? '')?></textarea>
                    <p id="zeitstrafe_counter"></p>
                </div>
                <p>
                    <input type="submit" name="new_zeitstrafe" value="Hinzufügen" class="w3-button w3-tertiary">
                </p>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- Turnierbericht -->
<div class="w3-card-4 w3-panel w3-padding-24"> 
    <h3 class="w3-text-secondary">Turnierbericht</h3>
    <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
        <p>
            Ligamodus 4.16 benennt eine Auswahl an besonderen Vorkommnissen, die hier aufgeführt werden sollten. Beispielsweise eine falsche Spielerausleihe oder verspätete Anreise eines Teams. Nutz das Feld darüber hinaus für eine kurze Zusammenfassung des Turniers. Auffällige Situationen oder zerstrittene Spiele sollten auch immer dem Ligaausschuss gemeldet werden.
        </p>
    </div>
    <?php if ($change_tbericht): ?>
        <form method="post">
            <p>
                <input <?= $tbericht->kader_check() ? 'checked' : '' ?>
                    class="w3-check"
                    value="kader_checked"
                    type="checkbox"
                    name="kader_check"
                    id="kader_check"
                    onchange="this.form.submit()"
                >
                <label for="kader_check" class="w3-hover-text-secondary w3-text-primary" style="cursor: pointer;"> Es wurde auf richtige Teamkader geachet.</label>
            </p>
            <p>
                <textarea class="w3-input w3-border w3-border-primary"
                        onkeyup="woerter_zaehlen(1500, 'turnierbericht', 'turnierbericht_counter');"
                        maxlength="1500"
                        rows="12"
                        id="turnierbericht"
                        name="turnierbericht"
                ><?=$_POST['text'] ?? ''?><?=$tbericht->get_turnier_bericht()?></textarea>
                <p id="turnierbericht_counter"><p>
            </p>
            <input type="submit" value="Speichern" name="set_turnierbericht" class="w3-button w3-tertiary">
        </form>
    <?php else: ?>
        <p><?=$tbericht->get_turnier_bericht() ?: '<p class="w3-text-grey">Es ist kein Turnierbericht vorhanden.</p>'?></p>
    <?php endif; ?>
</div>

<script>
    // Get the modal
    var modal1 = document.getElementById('modal_ausleihe');
    var modal2 = document.getElementById('modal_zeitstrafe');
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal1) {
            modal1.style.display = "none";
        }
        if (event.target == modal2) {
            modal2.style.display = "none";
        }
    }

    function open_kader(id) {
        var x = document.getElementById("kader_" + id);
        if (x.className.indexOf("w3-show") == -1) {
            x.className += " w3-show";
            x.previousElementSibling.className += " w3-secondary";
        } else {
            x.className = x.className.replace("w3-show", "");
            x.previousElementSibling.className = x.previousElementSibling.className.replace("w3-secondary", "");
        } 
    }
</script>