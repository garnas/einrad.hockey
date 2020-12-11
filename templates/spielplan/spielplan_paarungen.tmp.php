<div class="w3-hide-small">
    <h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
    <?php if($spielplan->akt_turnier->details['besprechung'] == 'Ja'){?><p class="nicht-drucken"><i>Alle Teams sollen sich um <?=date('H:i', strtotime($spielplan->akt_turnier->details['startzeit']) - 15*60)?>&nbsp;Uhr zu einer gemeinsamen Turnierbesprechung einfinden.</i></p><?php }//endif?>
    <p class="w3-text-grey w3-small">Spielzeit: 2 x <?=$spielzeit['halbzeit_laenge']?>&nbsp;min | Puffer je Spiel: <?=$spielzeit['pause']?>&nbsp;min</p>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th class="w3-center">Beginn</th>
                <th colspan="3" class="w3-center">Schiri</th>
                <th class="w3-left-align"></th>
                <th class="w3-left-align"></th>
                <th class="w3-left-align"></th>
                <th></th>
                <th class="w3-center">Tore</th>
                <th></th>
                <?php if($penalty_anzeigen){?>
                    <th colspan="3" class="w3-center"><i>Penalty</i></th>
                <?php }//endif?>
            </tr>
            <?php foreach ($spielliste as $index => $spiel){?>
                <tr>
                    <td class="td-N w3-center"><?=$spiel["zeit"]?></td>
                    <td class="td-N w3-right-align" title="<?=Team::teamid_to_teamname($spiel["schiri_team_id_a"])?>"><i style='cursor:help;'><?=$spiel["schiri_team_id_a"]?></i></td>
                    <td class="td-N w3-center">|</td>
                    <td class="w3-left-align" title="<?=Team::teamid_to_teamname($spiel["schiri_team_id_b"])?>"><i style='cursor:help;'><?=$spiel["schiri_team_id_b"]?></i></td>
                    <td class="w3-right-align" style="white-space: nowrap"><?=$spiel["team_a_name"]?></td>
                    <td class="w3-center">-</td>
                    <td class="w3-left-align" style="white-space: nowrap"><?=$spiel["team_b_name"]?></td>
                    <td class="w3-right-align"><?=$spiel["tore_a"]?></td>
                    <td class="w3-center">:</td>
                    <td class="w3-left-align"><?=$spiel["tore_b"]?></td>
                    <?php if($penalty_anzeigen){?>
                        <td class="w3-right-align w3-text-secondary"><?=$spiel["penalty_a"]?></td>
                        <td class="w3-center w3-text-black">:</td>
                        <td class="w3-left-align w3-text-secondary"><?=$spiel["penalty_b"]?></td>
                    <?php }//endif?>
                </tr>
                <?php if(($zeitdiff = strtotime($spielliste[$index+1]['zeit'] ?? $spiel['zeit']) - strtotime($spiel['zeit']) - $spielzeit['dauer']*60) > 0){?>
                    <tr>
                        <td class="w3-center"><?=date("H:i", strtotime($spiel["zeit"]) + $spielzeit['dauer']*60)?></td>
                        <td colspan="3"></td>
                        <td colspan="3" class="w3-center"><i><?=round($zeitdiff/60)?>&nbsp;min Pause</i></td>
                        <td colspan="<?php if($penalty_anzeigen){?>6<?php }else{?>3<?php }//endif?>"></td>
                    </tr>
                <?php }//endif?>
            <?php }//end foreach?>
        </table>
   </div>
</div>