<!-- SPIELE -->

    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped ">
            <tr class="w3-primary">
                <th class="w3-right-align">Zeit</th>
                <th colspan="2" class="w3-center">Schiri</th>
                <th>Team 1</th>
                <th>Team 2</th>
                <th colspan="3" class="w3-center">Ergebnis</th>
                <th colspan="3" class="w3-center">Penalty</th>
            </tr>
            <?php foreach ($spielliste as $index => $spiel){?>
                <tr>
                <td class="w3-right-align"><?=$spiel["zeit"]?></td>
                <td class="w3-right-align"><?=$spiel["schiri_team_id_a"]?></td>
                <td class="w3-right-align"><?=$spiel["schiri_team_id_b"]?></td>
                <td><?=$spiel["team_a_name"]?></td>
                <td><?=$spiel["team_b_name"]?></td>
                <td class="w3-right-align" style="padding-right: 0;"><input name='toreAPOST[<?=$index?>]' value='<?=$spiel["tore_a"]?>' size='3'></td>
                <td class="w3-center" style="padding-left: 0; padding-right: 0;">:</td>
                <td class="w3-left-align" style="padding-left: 0;"><input name='toreBPOST[<?=$index?>]' value='<?=$spiel["tore_b"]?>' size='3'></td>
                <td class="w3-right-align" style="padding-right: 0;"><input name='penAPOST[<?=$index?>]' value='<?=$spiel["penalty_a"]?>' size='3'></td>
                <td class="w3-center" style="padding-left: 0; padding-right: 0;">:</td>
                <td class="w3-left-align" style="padding-left: 0;"><input name='penBPOST[<?=$index?>]' value='<?=$spiel["penalty_b"]?>' size='3'></td>
                </tr>
            <?php }//end foreach?>
        </table>
   </div>
   <p><input type="submit" name="gesendet_tur" class="w3-block w3-button w3-tertiary" value="Spiele senden"></p>
</form>