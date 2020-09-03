<!-- ABSCHLUSSTABELLE -->
<div class="w3-hide-large w3-hide-medium">
    <h3 class="w3-text-secondary w3-margin-top">Abschlusstabelle</h3>
    <div class="w3-responsive w3-card">
        <table class="w3-table <?php if($teamcenter && $akt_turnier->daten['phase'] == 'ergebnis'){?>w3-pale-green<?php }else{?>w3-striped<?php }//endif?>" style="white-space: nowrap;">
            <tr class="w3-primary">
                <th class='w3-center'>Pl.</th>
                <th>Team</th>
                <th class="w3-center">Ligapunkte</th>
            </tr>
            <?php foreach ($tabelle as $index => $table){?>
                <tr>
                    <td class="w3-center"><?=$index+1?></td>
                    <td><?=$table["teamname"]?></td>
                    <td class="w3-center"><?=$table["ligapunkte"]?></td>
                </tr>
            <?php }//end foreach?>
        </table>
    </div>
    
    <!-- Modal -->
    <span class="w3-button w3-text-primary" onclick="document.getElementById('tabelle_details_mobile').style.display='block'" ><i class='material-icons'>info</i> Details anzeigen</span>
    <div id="tabelle_details_mobile" class="w3-modal">
        <div class="w3-modal-content w3-card-4">
            <div class="w3-container">
                <span onclick="document.getElementById('tabelle_details_mobile').style.display='none'" class="w3-button w3-text-secondary w3-display-topright">&times;</span>
                <h3 class="w3-text-secondary w3-margin-top">Details</h3>
                <div class="w3-responsive w3-card w3-section">
                    <table class="w3-table <?php if($teamcenter && $akt_turnier->daten['phase'] == 'ergebnis'){?>w3-pale-green<?php }else{?>w3-striped<?php }//endif?>">
                        <tr class="w3-primary">
                            <th>Pl.</th>
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
                                <td><?=$index+1?></td>
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
                <p class="w3-text-grey"><i>(Faktor 6/<?=$spielzeit['faktor']?>)</i></p>
            </div>
        </div>
    </div>
</div>
<script>
// Get the modal
var modal = document.getElementById('tabelle_details_mobile');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>