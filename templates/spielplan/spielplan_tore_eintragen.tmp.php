<h3 class="w3-text-secondary w3-margin-top">Tore eintragen</h3>
<form method="post">
    <!-- Tore zwischenspeichern -->
    <p>
        <button type="submit"
               name="tore_speichern"
               class="w3-block w3-card w3-button w3-tertiary"
        >
            <span class="material-icons">save</span>
            Tore zwischenspeichern
        </button>
    </p>
    <!-- Tabelle: Tore eintippen -->
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-centered w3-striped">
            <tr class="w3-primary">
                <th>
                    <i class="material-icons">schedule</i>
                    <br>
                    Zeit
                </th>
                <th>
                    <i class="material-icons">sports</i>
                    <br>
                    Schiri
                </th>
                <th>
                    <i class="material-icons">sports_hockey</i>
                    <br>
                    Spiele
                </th>
                <th>
                    <i class="material-icons">sports_baseball</i>
                    <br>Tore
                </th>
                <th>
                    <i class="material-icons">priority_high</i>
                    <br>
                    Penalty
                </th>
            </tr>
            <?php foreach ($spielplan->spiele as $spiel_id => $spiel) { ?>
                <tr <?php if (!is_null($spiel["tore_a"])
                        && !is_null($spiel["tore_b"])
                        && !$spielplan->check_penalty_spiel($spiel_id, true)) { ?>
                        class="w3-pale-green"
                    <?php } //endif?>
                >
                    <td><?= $spiel["zeit"] ?></td>
                    <!-- Schiris -->
                    <td class="w3-center   w3-text-primary">
                        <span class="w3-tooltip">
                            <i><?= $spiel["schiri_team_id_b"] ?></i>
                            <span style="white-space: nowrap; position:absolute;left:0;bottom:15px"
                                  class="w3-text w3-small w3-primary w3-tag">
                                <i class="material-icons" style="vertical-align: -30%">keyboard_arrow_down</i>
                                <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_b"]]['teamname'] ?>
                            </span>
                        </span>
                        <br>
                        <span class="w3-tooltip">
                            <i><?= $spiel["schiri_team_id_a"] ?></i>
                            <span style="white-space: nowrap; position:absolute;left:0;top:15px"
                                  class="w3-text w3-small w3-primary w3-tag">
                                <i class="material-icons" style="vertical-align: -30%">keyboard_arrow_up</i>
                                <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_a"]]['teamname'] ?>
                            </span>
                        </span>
                    </td>
                    <!-- Teams -->
                    <td class="w3-center" style="white-space: nowrap;">
                        <span><?= $spiel["teamname_a"] ?></span>
                        <br>
                        <span><?= $spiel["teamname_b"] ?></span>
                    </td>
                    <!-- Tore Mobil -->
                    <td class="w3-center">
                        <input id="tore_a[<?= $spiel_id ?>]"
                               name="tore_a[<?= $spiel_id ?>]"
                               value='<?= $spiel["tore_a"] ?>'
                               class='w3-input w3-border w3-round w3-center'
                               style='padding: 2px; width: 65px; display: inline-block;'
                               type='number'
                               autocomplete='off'
                               min='0'
                               step='1'
                        >
                        <br>
                        <input id="tore_b[<?= $spiel_id ?>]"
                               name="tore_b[<?= $spiel_id ?>]"
                               value='<?= $spiel["tore_b"] ?>'
                               class='w3-input w3-border w3-round w3-center'
                               style='padding: 2px; width: 65px; display: inline-block;'
                               type='number'
                               autocomplete='off'
                               min='0'
                               step='1'
                        >
                    </td>
                    <!-- Penalty -->
                    <td class="w3-center <?= (!$spielplan->validate_penalty_spiel($spiel)) ?: 'w3-secondary' ?>">
                        <input id="penalty_a[<?= $spiel_id ?>]"
                               name="penalty_a[<?= $spiel_id ?>]"
                               value='<?= $spiel["penalty_a"] ?>'
                               class='w3-input w3-border w3-round w3-center w3-text-secondary'
                               style='padding: 2px; width: 65px; display: inline-block;'
                               <?= ($spielplan->check_penalty_spiel($spiel_id) || $spielplan->validate_penalty_spiel($spiel)) ?: 'disabled placeholder = "/"' ?>
                               type='number'
                               autocomplete='off'
                               min='0'
                               step='1'
                        >
                        <br>
                        <input id="penalty_b[<?= $spiel_id ?>]"
                               name="penalty_b[<?= $spiel_id ?>]"
                               value='<?= $spiel["penalty_b"] ?>'
                               class='w3-input w3-border w3-round w3-center w3-text-secondary'
                               style='padding: 2px; width: 65px; display: inline-block;'
                               <?= ($spielplan->check_penalty_spiel($spiel_id) || $spielplan->validate_penalty_spiel($spiel)) ?: 'disabled placeholder = "/"' ?>
                               type='number'
                               autocomplete='off'
                               min='0'
                               step='1'
                        >
                    </td>
                </tr>
                <?php if ($get_pause($spiel) > 0) { ?>
                    <!-- Spielpause -->
                    <tr>
                        <td>
                            <?= date("H:i", strtotime($spiel["zeit"]) + $spiel_dauer * 60) ?>
                        </td>
                        <td></td>
                        <td class="w3-center">
                            <i class="material-icons">schedule</i>
                            <i><?= round($get_pause($spiel) / 60) ?>&nbsp; min Pause</i>
                            <i class="material-icons">schedule</i>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                <?php }// endif pause ?>
            <?php }// end foreach spiele?>
        </table>
    </div>
    <!-- Formular für die Ergebniseintragung in den Centern erstellen -->
    <p>
        <button type="submit"
                name="tore_speichern"
                class="w3-block w3-card w3-button w3-tertiary"
        >
            <span class="material-icons">save </span>
            Tore zwischenspeichern
        </button>
    </p>
</form>

<script>
    document.addEventListener("wheel", function (event) {
        if (document.activeElement.type === "number") {
            document.activeElement.blur();
        }
    });
</script>