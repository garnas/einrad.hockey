<!-- SPIELPAARUNGEN MOBIL -->
<div class="w3-hide-large w3-hide-medium">
    <h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
    <?php if($spielplan->akt_turnier->daten['besprechung'] == 'Ja'){?><p><i>Alle Teams sollen sich um <?=date('H:i', strtotime($spielplan->akt_turnier->daten['startzeit']) - 15*60)?>&nbsp;Uhr zu einer gemeinsamen Turnierbesprechung einfinden.</i></p><?php }//endif?>
    <span class="w3-text-grey">Spielzeit: 2 x <?=$spielzeit['halbzeit_laenge']?>&nbsp;min | Puffer je Spiel: <?=$spielzeit['pause']?>&nbsp;min</span>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped" style="white-space: nowrap;">
            <tr class="w3-primary">
                <th class="w3-center">Zeit</th>
                <th class="w3-center">Schiri</th>
                <th class="w3-center">Teams</th>
                <th class="w3-center">Ergebnis</th>
                <?php if($penalty_anzeigen){?>
                    <th class="w3-center">Penalty</th>
                <?php }//endif?>
            </tr>
            <?php foreach ($spielliste as $index => $spiel){?>
                <tr>
                    <td class="w3-center"><?=$spiel["zeit"]?></td>
                    <td class="w3-center">
                        <span class="w3-tooltip"><i><?=$spiel["schiri_team_id_b"]?></i> <span class="w3-text w3-small"><em><?=Team::teamid_to_teamname($spiel["schiri_team_id_b"])?></em></span>
                        <br>
                        <span class="w3-tooltip"><i><?=$spiel["schiri_team_id_a"]?></i></span> <span class="w3-text w3-small"><em><?=Team::teamid_to_teamname($spiel["schiri_team_id_a"])?></em></span>
                    </td>
                    <td>
                        <div class="w3-center">
                            <?=$spiel["team_a_name"]?><br><?=$spiel["team_b_name"]?>
                        </div>
                    </td>
                    <td class="w3-center">
                        <span><?=$spiel["tore_a"]?></span>
                        <br>
                        <span><?=$spiel["tore_b"]?></span>
                    </td>
                    <?php if($penalty_anzeigen){?>
                        <td class="w3-center"><?=$spiel["penalty_a"]?><br><?=$spiel["penalty_b"]?></td>
                    <?php }//endif?>
                </tr>
            <?php }//end foreach?>
        </table>
    </div>
</div>