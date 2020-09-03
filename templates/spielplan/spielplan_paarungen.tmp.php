<h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
<?php if($akt_turnier->daten['besprechung'] == 'Ja'){?><p><i>Alle Teams sollen sich um <?=date('h:i', strtotime($akt_turnier->daten['startzeit']) - 15*60)?>&nbsp;Uhr zu einer gemeinsamen Turnierbesprechung einfinden.</i></p><?php }//endif?>
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
                <td class="w3-right-align" style="padding-right: 0;"><?=$spiel["tore_a"]?></td>
                <td class="w3-center" style="padding-left: 0; padding-right: 0;">:</td>
                <td class="w3-left-align" style="padding-left: 0;"><?=$spiel["tore_b"]?></td>
                <td class="w3-right-align" style="padding-right: 0;"><?=$spiel["penalty_a"]?></td>
                <td class="w3-center" style="padding-left: 0; padding-right: 0;">:</td>
                <td class="w3-left-align" style="padding-left: 0;"><?=$spiel["penalty_b"]?></td>
                </tr>
            <?php }//end foreach?>
        </table>
   </div>