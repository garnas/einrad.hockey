<h3 class="w3-text-primary">Teamkader der <?= Team::id_to_name($team_id) ?></h3>
<!-- Aktuelle Saison -->
<div class="w3-responsive w3-section w3-card">
    <table class="w3-table w3-striped">
        <thead>
        <tr>
            <th class="w3-primary">ID</th>
            <th class="w3-primary">Name</th>
            <th class="w3-primary w3-center">J/G</th>
            <th class="w3-primary w3-center">Schiri</th>
            <?php if (Helper::$ligacenter): ?>
                <th class="w3-primary w3-center">Hinzugefügt am:</th>
            <?php endif; // endif?>
        </tr>
        </thead>
        <?php foreach ($kader as $spieler): ?>
            <tr>
                <td><?= $spieler->id() ?></td>
                <?php if (Helper::$ligacenter): // Link zum Bearbeiten als LA ?>
                    <td>
                        <?= Html::link('lc_spieler_aendern.php?spieler_id=' . $spieler->id(), $spieler->get_name()) ?>
                    </td>
                <?php else: ?>
                    <td>
                        <?= $spieler->get_name() ?>
                    </td>
                <?php endif; ?>
                <td class='w3-center'>
                    <?= $spieler->get_jahrgang() . " " . $spieler->get_geschlecht() ?>
                </td>
                <td class='w3-center'>
                    <?php if ($spieler->schiri): ?>
                        <?= Html::icon("check_circle_outline") ?>
                        <?= $spieler->get_schiri() ?>
                        <?= ($spieler->junior === 'Ja') ? "<i class='w3-text-grey'>junior</i>" : "" ?>
                        <?= ($spieler->check_ausbilder()) ? "<i class='w3-text-grey'>Ausbilder/in</i>" : "" ?>
                    <?php endif; ?>
                </td>
                <?php if (Helper::$ligacenter): ?>
                    <td class="w3-center">
                        <?= $spieler->get_timestamp() ?>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- Aus Vorsaison übernehmen -->
<?php if (!empty($kader_vorsaison)): ?>
    <form method="post" class="w3-section w3-text-grey">
        <h3>Spieler aus der Vorsaison übernehmen</h3>
        <div class="w3-responsive w3-section w3-card">
            <table class="w3-table w3-striped">
                <tr>
                    <th class="w3-primary">ID</th>
                    <th class="w3-primary">Name</th>
                    <th class="w3-primary w3-center">J/G</th>
                    <th class="w3-primary w3-center">Schiri</th>
                    <th class="w3-primary ">Übernehmen</th>
                </tr>
                <?php foreach ($kader_vorsaison as $spieler): ?>
                    <tr>
                        <td><?= $spieler->id() ?></td>

                        <?php if (Helper::$ligacenter): // Link zum Bearbeiten als LA ?>
                            <td>
                                <?= Html::link('lc_spieler_aendern.php?spieler_id=' . $spieler->id(), $spieler->get_name()) ?>
                            </td>
                        <?php else: ?>
                            <td>
                                <?= $spieler->get_name() ?>
                            </td>
                        <?php endif; ?>

                        <td class='w3-center'>
                            <?= $spieler->get_jahrgang() . " " . $spieler->get_geschlecht() ?>
                        </td>
                        <td class='w3-center'>
                            <?php if ($spieler->schiri): ?>
                                <?= Html::icon("check_circle_outline") ?>
                                <?= $spieler->get_schiri() ?>
                                <?= ($spieler->junior === 'Ja') ? "<i class='w3-text-grey'>junior</i>" : "" ?>
                                <?= ($spieler->check_ausbilder()) ? "<i class='w3-text-grey'>Prüfender</i>" : "" ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="checkbox"
                                   class="w3-check"
                                   id="<?= $spieler->id() ?>"
                                   name="takeover[]"
                                   value="<?= $spieler->id() ?>">
                            <label style="cursor: pointer"
                                   class="w3-hover-text-secondary w3-text-primary"
                                   for="<?= $spieler->id() ?>">
                                Spieler übernehmen
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <p>
            <input type="checkbox" class="w3-check" value="zugestimmt" name="dsgvo" id="dsgvo">
            <label for="dsgvo" style="cursor: pointer;" class="w3-text-black">
                Alle ausgewählten Spieler haben die aktuellen <?= Html::link(Nav::LINK_DSGVO, 'Datenschutz-Hinweise') ?>
                gelesen und ihnen zugestimmt. Bei unter 16-Jährigen wurde die Erlaubnis der Eltern eingeholt.
            </label>
        </p>
        <input type="submit" name="submit_takeover" value="Ausgewählte Spieler übernehmen" class="w3-button w3-primary">
    </form>
<?php endif; ?>

<!-- Form zum Eintragen eines neuen Spielers -->
<div class="w3-section">
    <!--<p class="w3-text-grey">Neue Spieler können bis zum <?= Config::SAISON_ENDE ?> 23:59:59&nbsp;Uhr hinzugefügt werden.</p>-->
    <p class="w3-text-grey">
        Um einen neuen Spieler aus einem anderen Team zu übernehmen, bitte den Spieler neu eintragen. Die Übernahme
        geschieht dann automatisch, wenn die Daten identisch sind und dieser Spieler noch nicht in einen aktuellen Kader
        übernommen wurde. Der Schiedsrichterstatus wird dann ebenfalls übernommen.
    </p>
    <button class="w3-button w3-tertiary" onclick="document.getElementById('spieler_eintragen').style.display='block'">
        Neuen Spieler eintragen
    </button>
    <div class="w3-modal" id="spieler_eintragen" style="display: none;">
        <form class="w3-card-4 w3-modal-content w3-panel" style="max-width: 400px;" method='POST'>
            <span onclick="document.getElementById('spieler_eintragen').style.display='none'"
                  class="w3-button w3-large w3-text-secondary w3-display-topright">
                &times;
            </span>
            <h3 class="w3-text-primary">Neuen Spieler eintragen</h3>
            <p>
                <label class="w3-text-primary" for="vorname">Vorname</labeL>
                <input class="w3-input w3-border w3-border-primary"
                       value="<?= $_POST['vorname'] ?? '' ?>"
                       type="text"
                       name="vorname"
                       id="vorname"
                       autocomplete="off"
                       required>
            </p>
            <p>
                <label class="w3-text-primary" for="nachname">Nachname</labeL>
                <input class="w3-input w3-border w3-border-primary"
                       type="text"
                       value="<?= $_POST['nachname'] ?? '' ?>"
                       name="nachname"
                       id="nachname"
                       autocomplete="off"
                       required>
            </p>
            <p>
                <label class="w3-text-primary" for="jahrgang">Jahrgang</labeL>
                <input class="w3-input w3-border w3-border-primary"
                       value="<?= $_POST['jahrgang'] ?? '' ?>"
                       placeholder="vierstellig"
                       type="number"
                       name="jahrgang"
                       id="jahrgang"
                       autocomplete="off"
                       required>
            </p>
            <p>
                <label class="w3-text-primary" for="geschlecht">Geschlecht</labeL>
                <select style="height:40px" class='w3-input w3-border w3-border-primary' name='geschlecht' id='geschlecht'>
                    <option <?= $_POST['geschlecht'] ?? 'selected' ?> disabled></option>
                    <option <?php if (($_POST['geschlecht'] ?? '') === 'm'){ ?>selected<?php } ?> value='m'>m</option>
                    <option <?php if (($_POST['geschlecht'] ?? '') === 'w'){ ?>selected<?php } ?> value='w'>w</option>
                    <option <?php if (($_POST['geschlecht'] ?? '') === 'd'){ ?>selected<?php } ?> value='d'>d</option>
                </select>
            </p>
            <p>
                <input type="checkbox" class="w3-check" value="zugestimmt" name="dsgvo" id="dsgvo_neu">
                <label for="dsgvo_neu" style="cursor: pointer;" class="">
                    Der Spieler hat die aktuellen <?= Html::link(Nav::LINK_DSGVO, "Datenschutz-Hinweise", true) ?>
                    gelesen und der Verwendung seiner Daten zugestimmt.
                </label>
            </p>
            <p>
                <input class="w3-button w3-tertiary" type='submit' name='neuer_eintrag' value='Spieler eintragen'>
            </p>
        </form>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById('spieler_eintragen');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>