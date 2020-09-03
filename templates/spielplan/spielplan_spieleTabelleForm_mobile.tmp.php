<!-- SPIELE MOBIL-->
<div class="w3-hide-large w3-hide-medium">
    <h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
    <form method="post">
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped" style="white-space: nowrap;">
                <tr class="w3-primary">
                    <th class="w3-center">Zeit</th>
                    <th class="w3-center">Schiri</th>
                    <th class="w3-center">Teams</th>
                    <th>Ergebnis</th>
                    <th>Penalty</th>
                </tr>
                <?php foreach ($spielliste as $index => $spiel){?>
                    <tr <?php if (!is_null($spiel["tore_b"]) && !is_null($spiel["tore_b"])){?>class="w3-pale-green w3-opacity"<?php }//endif?>>
                        <td class="w3-center"><?=$spiel["zeit"]?></td>
                        <td class="w3-center">
                            <span class="w3-tooltip"><i><?=$spiel["schiri_team_id_b"]?></i> <span class="w3-text w3-small"><em><?=Team::teamid_to_teamname($spiel["schiri_team_id_b"])?></em></span>
                            <br>
                            <span class="w3-tooltip"><i><?=$spiel["schiri_team_id_a"]?></i></span> <span class="w3-text w3-small"><em><?=Team::teamid_to_teamname($spiel["schiri_team_id_a"])?></em></span>
                        </td>
                        <td>
                            <div class="w3-center">
                                <b>
                                    <label for='toreAPOST[<?=$index?>]'><?=$spiel["team_a_name"]?></label>
                                    <br>
                                    <label for='toreBPOST[<?=$index?>]'><?=$spiel["team_b_name"]?></label>
                                </b>
                            </div>
                        </td>
                        <!-- TORE A -->
                        <td class="w3-center">
                            <input 
                                name='toreAPOST[<?=$index?>]'
                                id='toreAPOST[<?=$index?>]'
                                value='<?=$spiel["tore_a"]?>'
                                class='w3-input w3-border w3-round w3-center'
                                style='padding: 2px; max-width: 75px;'
                                autocomplete='off'
                                type='number'
                                min='0'
                            >
                        <!-- TORE B -->
                            <input 
                                name='toreBPOST[<?=$index?>]'
                                id='toreBPOST[<?=$index?>]' 
                                value='<?=$spiel["tore_b"]?>'
                                class='w3-input w3-border w3-round w3-center'
                                style='padding: 2px; max-width: 75px;'
                                autocomplete='off'
                                type='number'
                                min='0'
                            >
                        </td>
                        <!-- PENALTY TORE A -->
                        <td class="w3-right-align">
                            <input 
                                name='penAPOST[<?=$index?>]' 
                                value='<?=$spiel["penalty_a"]?>'
                                class='w3-input w3-border w3-round w3-center'
                                style='padding: 2px; max-width: 75px;'
                                autocomplete='off'
                                type='number'
                                min='0'
                            >
                            <input 
                                name='penBPOST[<?=$index?>]' 
                                value='<?=$spiel["penalty_b"]?>'
                                class='w3-input w3-border w3-round w3-center'
                                style='padding: 2px; max-width: 75px;'
                                autocomplete='off'
                                type='number'
                                min='0'
                            >
                        </td>
                    </tr>
                <?php }//end foreach?>
            </table>
        </div>
        <p><input type="submit" name="gesendet_tur" class="w3-block w3-button w3-tertiary" value="Spiele zwischenspeichern"></p>
        <p class='w3-text-grey'>Spielergebnisse sollten zusätzlich schriftlich festgehalten werden</p>
    </form>
    <p class="w3-text-red"><?=$penalty_warning?></p>
</div>
