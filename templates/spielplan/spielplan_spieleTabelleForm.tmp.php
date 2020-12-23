<!-- Falsches Eintragen durch Scrollen in Number-Inputs verhindern -->
<script>
    document.addEventListener("wheel", function(event){
        if(document.activeElement.type === "number"){
            document.activeElement.blur();
        }
    });
</script>

<!-- SPIELE -->
<div class="w3-hide-small">
    <h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
    <p class='w3-text-grey'>Spielergebnisse müssen zusätzlich schriftlich festgehalten werden.</p>
    <form method="post">
        <p>
            <input type="submit" name="tore_speichern_oben" class="w3-block w3-button w3-tertiary"
                   value="Spiele zwischenspeichern">
        </p>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped" style="white-space: nowrap;">
                <tr class="w3-primary">
                    <th class="w3-center">Beginn</th>
                    <th colspan="3" class="w3-center">Schiri</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th class="w3-center">Ergebnis</th>
                    <th class="w3-center">Penalty</th>
                </tr>
                <?php foreach ($spiele as $spiel_id => $spiel) { ?>
                    <tr <?php if (($teamcenter or $ligacenter) && !is_null($spiel["tore_a"]) && !is_null($spiel["tore_b"])){ ?>class="w3-pale-green"<?php }//endif?>>

                        <!-- Uhrzeit -->
                        <td class="w3-center"><?= $spiel["zeit"] ?></td>

                        <!-- Schiris -->
                        <td class="w3-center"
                            title="<?= $spielplan->teamliste[$spiel["schiri_team_id_a"]]['teamname'] ?>">
                            <i style='cursor:help;'><?= $spiel["schiri_team_id_a"] ?></i>
                        </td>
                        <td>|</td>
                        <td class="w3-center"
                            title="<?= $spielplan->teamliste[$spiel["schiri_team_id_b"]]['teamname'] ?>">
                            <i style='cursor:help;'><?= $spiel["schiri_team_id_b"] ?></i>
                        </td>

                        <!-- Teams -->
                        <td><label for='tore_a[<?= $spiel_id ?>]'><?= $spiel["teamname_a"] ?></label></td>
                        <td><label for='tore_b[<?= $spiel_id ?>]'><?= $spiel["teamname_b"] ?></label></td>

                        <!-- Tore -->
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
                            <span>:</span>
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

                        <!-- PENALTY -->
                        <td class="w3-center">
                            <label for='penalty_a[<?= $spiel_id ?>]'>
                                <input id="penalty_a[<?= $spiel_id ?>]"
                                       name="penalty_a[<?= $spiel_id ?>]"
                                       value='<?= $spiel["penalty_a"] ?>'
                                       class='w3-input w3-border w3-round w3-center <?= !(!is_null($spiel["penalty_a"]) && !$spielplan->check_penalty_spiel($spiel_id)) ?: 'w3-secondary' ?>'
                                       style='padding: 2px; width: 65px; display: inline-block;'
                                        <?= (!is_null($spiel["penalty_a"]) or $spielplan->check_penalty_spiel($spiel_id)) ?: 'disabled'?>
                                       type='number'
                                       autocomplete='off'
                                       min='0'
                                       step='1'
                                >
                            </label>
                            <span>:</span>
                            <label for="penalty_b[<?= $spiel_id ?>]">
                                <input id="penalty_b[<?= $spiel_id ?>]"
                                       name="penalty_b[<?= $spiel_id ?>]" value='<?= $spiel["penalty_b"] ?>'
                                       class='w3-input w3-border w3-round w3-center <?= !(!is_null($spiel["penalty_b"]) && !$spielplan->check_penalty_spiel($spiel_id)) ?: 'w3-secondary' ?>'
                                       style='padding: 2px; width: 65px; display: inline-block;'
                                        <?= (!is_null($spiel["penalty_b"]) or $spielplan->check_penalty_spiel($spiel_id)) ?: 'disabled'?>
                                       type='number'
                                       autocomplete='off'
                                       min='0'
                                       step='1'
                                >
                            </label>
                        </td>
                    </tr>
                <?php } //end foreach?>
            </table>
        </div>
        <p>
            <input type="submit"
                   name="tore_speichern_unten"
                   class="w3-block w3-button w3-tertiary"
                   value="Spiele zwischenspeichern"
            >
        </p>
    </form>
</div>