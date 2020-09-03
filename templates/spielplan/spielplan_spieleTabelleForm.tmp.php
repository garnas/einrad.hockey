<!-- SPIELE -->
<div class="w3-hide-small">
    <h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
    <form method="post">
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped">
                <tr class="w3-primary">
                    <th class="w3-right-align">Zeit</th>
                    <th colspan="3" class="w3-center">Schiri</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th colspan="3" class="w3-center">Ergebnis</th>
                    <th colspan="3" class="w3-center">Penalty</th>
                </tr>
                <?php foreach ($spielliste as $index => $spiel){?>
                    <tr <?php if (!is_null($spiel["tore_b"]) && !is_null($spiel["tore_b"])){?>class="w3-pale-green"<?php }//endif?>>
                        <td class="w3-right-align"><?=$spiel["zeit"]?></td>
                        <td class="w3-center" style="padding-right: 0;" title="<?=Team::teamid_to_teamname($spiel["schiri_team_id_a"])?>"><i style='cursor:help;'><?=$spiel["schiri_team_id_a"]?></i></td>
                        <td style="padding-right: 0; padding-left: 0;">|</td>
                        <td class="w3-center" style="padding-left: 0;" title="<?=Team::teamid_to_teamname($spiel["schiri_team_id_b"])?>"><i style='cursor:help;'><?=$spiel["schiri_team_id_b"]?></i></td>
                        <td><?=$spiel["team_a_name"]?></td>
                        <td><?=$spiel["team_b_name"]?></td>
                        <!-- TORE A -->
                        <td class="w3-right-align" style="padding-right: 0;">
                            <input 
                                name='toreAPOST[<?=$index?>]' 
                                value='<?=$spiel["tore_a"]?>'
                                class='w3-input w3-border w3-round w3-center'
                                style='padding: 2px; width: 70px;'
                                type='number'
                                autocomplete='off'
                                min='0'
                                step='1'
                            >
                        </td>
                        <td class="w3-center" style="padding-left: 0; padding-right: 6px;">:</td>
                        <!-- TORE B -->
                        <td class="w3-left-align" style="padding-left: 0;">
                            <input 
                                name='toreBPOST[<?=$index?>]' 
                                value='<?=$spiel["tore_b"]?>'
                                class='w3-input w3-border w3-round w3-center'
                                style='padding: 2px; width: 70px'
                                type='number'
                                autocomplete='off'
                                min='0'
                                step='1'
                            >
                        </td>
                        <!-- PENALTY TORE A -->
                        <td class="w3-right-align" style="padding-right: 0;">
                            <input 
                                name='penAPOST[<?=$index?>]' 
                                value='<?=$spiel["penalty_a"]?>'
                                class='w3-input w3-border w3-round w3-center'
                                style='padding: 2px; width: 70px'
                                type='number'
                                autocomplete='off'
                                min='0'
                                step='1'
                            >
                        </td>
                        <td class="w3-center" style="padding-left: 0; padding-right: 6px;">:</td>
                        <!-- PENALTY TORE B -->
                        <td class="w3-left-align" style="padding-left: 0;">
                            <input 
                                name='penBPOST[<?=$index?>]' 
                                value='<?=$spiel["penalty_b"]?>'
                                class='w3-input w3-border w3-round w3-center'
                                style='padding: 2px; width: 70px'
                                type='number'
                                autocomplete='off'
                                min='0'
                                step='1'
                            >
                        </td>
                    </tr>
                <?php }//end foreach?>
            </table>
        </div>
        <p><input type="submit" name="gesendet_tur" class="w3-block w3-button w3-tertiary" value="Spiele zwischenspeichern"></p>
        <p class='w3-text-grey'>Spielergebnisse sollten zusätzlich schriftlich festgehalten werden</p>
    </form>
    <p class="w3-text-red"><?=$penalty_warning ?? ''?></p>
</div>