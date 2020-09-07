<!-- ABSCHLUSSTABELLE -->
<div class="w3-hide-small">
    <h3 class="w3-text-secondary w3-margin-top">Abschlusstabelle</h3>
   <div class="w3-responsive w3-card">
        <table class="w3-table <?php if($teamcenter && $akt_turnier->daten['phase'] == 'ergebnis'){?>w3-pale-green<?php }else{?>w3-striped<?php }//endif?>">
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
                    <td class="w3-center"><?=$index+1?></td>
                    <td><?=$table["teamname"]?></td>
                    <td class="w3-center"><?=$table["spiele"]?></td>
                    <td class="w3-center"><?=$table["punkte"]?></td>
                    <td class="w3-center"><?=$table["tore"]?></td>
                    <td class="w3-center"><?=$table["gegentore"]?></td>
                    <td class="w3-center"><?=$table["diff"]?></td>
                    <td class="w3-center"><?=$table["ligapunkte"]?></td>
                </tr>
            <?php }//end foreach?>
        </table>
   </div>
</div>