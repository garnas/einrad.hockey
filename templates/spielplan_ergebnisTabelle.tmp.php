<!-- ABSCHLUSSTABELLE -->
<h3 class="w3-text-secondary w3-margin-top">Abschlusstabelle</h3>
   <div class="w3-responsive w3-card">
        <table class="w3-table <?php if($teamcenter && $akt_turnier->daten['phase'] == 'ergebnis'){?>w3-pale-green<?php }else{?>w3-striped<?php }//endif?>">
            <tr class="w3-primary">
                <th class="w3-right-align">Pl.</th>
                <th>Team</th>
                <th class="w3-right-align">Spiele</th>
                <th class="w3-right-align">Punkte</th>
                <th class="w3-right-align">Tore</th>
                <th class="w3-right-align">Gegentore</th>
                <th class="w3-right-align">Differenz</th>
                <th class="w3-right-align">Ligapunkte</th>
            </tr>
            <?php foreach ($tabelle as $index => $table){?>
                <tr>
                    <td class="w3-right-align"><?=$index+1?></td>
                    <td><?=$table["teamname"]?></td>
                    <td class="w3-right-align"><?=$table["spiele"]?></td>
                    <td class="w3-right-align"><?=$table["punkte"]?></td>
                    <td class="w3-right-align"><?=$table["tore"]?></td>
                    <td class="w3-right-align"><?=$table["gegentore"]?></td>
                    <td class="w3-right-align"><?=$table["diff"]?></td>
                    <td class="w3-right-align"><?=$table["ligapunkte"]?></td>
                </tr>
            <?php }//end foreach?>
        </table>
   </div>