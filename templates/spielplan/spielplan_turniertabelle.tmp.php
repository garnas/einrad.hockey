<!-- ABSCHLUSSTABELLE -->
<h3 class="w3-text-secondary w3-margin-top">Tabelle</h3>
<?php if (!$spielplan->check_tabelle_einblenden()) { ?>
    <p class="w3-text-grey">Platzierungen und Ligapunkte werden angezeigt, sobald jedes Team mindestens ein Spiel gespielt hat.</p>
<?php } // endif?>
<div class="w3-responsive w3-card">
    <table class="w3-table <?= (($teamcenter or $ligacenter) && $spielplan->turnier->details['phase'] == 'ergebnis') ? 'w3-pale-green' : 'w3-striped' ?>">
        <tr class="w3-primary">
            <th class="w3-center">Pl.</th>
            <th>Team</th>
            <th class="w3-center">Spiele</th>
            <th class="w3-center w3-hide-small">Punkte</th>
            <th class="w3-center w3-hide-small">Tore</th>
            <th class="w3-center w3-hide-small">Gegentore</th>
            <th class="w3-center w3-hide-small">Differenz</th>
            <th class="w3-center w3-hide-small">Ligapunkte</th>
        </tr>
        <?php foreach ($spielplan->platzierungstabelle as $team_id => $x) { ?>
            <tr class="<?= ($x['penalty'] && empty($x['statistik']['penalty_diff'])) ? "w3-secondary" : "" ?>">
                <td class="w3-center"><?= ($spielplan->check_tabelle_einblenden()) ? $x['platz'] : '--' ?></td>
                <td style="white-space: nowrap;"><?= $x["teamname"] ?></td>
                <td class="w3-center w3-hide-small"><?= $x['statistik']["spiele"] ?></td>
                <td class="w3-center w3-hide-small"><?= $x['statistik']["punkte"] ?></td>
                <td class="w3-center w3-hide-small"><?= $x['statistik']["tore"] ?></td>
                <td class="w3-center w3-hide-small"><?= $x['statistik']["gegentore"] ?></td>
                <td class="w3-center w3-hide-small"><?= $x['statistik']["tordifferenz"] ?></td>
                <td class="w3-center"><?= (($x['penalty'] && empty($x['statistik']['penalty_diff'])) or !$spielplan->check_tabelle_einblenden()) ? '--' : $x["ligapunkte"] ?></td>
            </tr>
        <?php }//end foreach?>
    </table>
</div>
</div>

<!-- Modal -->
<span class="w3-button w3-hide-large w3-hide-medium w3-text-primary" onclick="document.getElementById('tabelle_details_mobile').style.display='block'" ><i class='material-icons'>info</i> Details anzeigen</span>
<div id="tabelle_details_mobile" class="w3-modal">
    <div class="w3-modal-content w3-card-4">
        <div class="w3-responsive w3-card" style="white-space: nowrap;">
            <table class="w3-table w3-striped">
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
                <?php foreach ($spielplan->platzierungstabelle as $team_id => $x) { ?>
                    <tr>
                        <td class="w3-center"><?= ($spielplan->check_tabelle_einblenden()) ? $x['platz'] : '--' ?></td>
                        <td style="white-space: nowrap;"><?=$x["teamname"]?></td>
                        <td class="w3-center"><?=$x['statistik']["spiele"]?></td>
                        <td class="w3-center"><?=$x['statistik']["punkte"]?></td>
                        <td class="w3-center"><?=$x['statistik']["tore"]?></td>
                        <td class="w3-center"><?=$x['statistik']["gegentore"]?></td>
                        <td class="w3-center"><?=$x['statistik']["tordifferenz"]?></td>
                        <td class="w3-center"><?= (($x['penalty'] && empty($x['statistik']['penalty_diff'])) or !$spielplan->check_tabelle_einblenden()) ? '--' : $x["ligapunkte"] ?></td>
                    </tr>
                <?php }//end foreach?>
            </table>
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

<?= $spielplan->direkter_vergleich_tabellen ?>