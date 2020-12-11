<div class="w3-hide-small">

    <!-- Penalty Warnung -->
    <p class="w3-text-secondary"><?=$spielplan->penalty_warning?></p>

    <!-- ABSCHLUSSTABELLE -->
    <h3 class="w3-text-secondary w3-margin-top">Tabelle</h3>
    <?php if(!$show_turnierergebnis){?><p class="w3-text-grey">Platzierungen und Ligapunkte werden angezeigt, sobald jedes Team mindestens ein Spiel gespielt hat.</p><?php }//endif?>
   <div class="w3-responsive w3-card">
        <table class="w3-table <?php if((($teamcenter ?? false) or ($ligacenter ?? false)) && $spielplan->akt_turnier->details['phase'] == 'ergebnis'){?>w3-pale-green<?php }else{?>w3-striped<?php }//endif?>">
            <tr class="w3-primary">
                <th class="w3-center">Pl.</th>
                <th>Team</th>
                <th class="w3-center">Spiele</th>
                <th class="w3-center">Punkte</th>
                <th class="w3-center">Tore</th>
                <th class="w3-center">Gegentore</th>
                <th class="w3-center">Differenz</th>
                <th class="w3-center">Ligapunkte</th>
            </tr>
            <?php foreach ($tabelle as $index => $table){?>
                <tr>
                    <td class="w3-center"><?php if($show_turnierergebnis){?><?=$index+1?><?php }else{?>--<?php }?></td>
                    <td><?=$table["teamname"]?></td>
                    <td class="w3-center"><?=$table["spiele"]?></td>
                    <td class="w3-center"><?=$table["punkte"]?></td>
                    <td class="w3-center"><?=$table["tore"]?></td>
                    <td class="w3-center"><?=$table["gegentore"]?></td>
                    <td class="w3-center"><?=$table["diff"]?></td>
                    <td class="w3-center"><?php if($show_turnierergebnis){?><?=$table["ligapunkte"]?><?php }else{?>--<?php }?></td>
                </tr>
            <?php }//end foreach?>
        </table>
   </div>
</div>
