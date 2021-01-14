<!-- ABSCHLUSSTABELLE -->
<h3 class="w3-text-secondary w3-margin-top">Tabelle</h3>
<?php if (!$spielplan->check_tabelle_einblenden()) { ?>
    <p class="w3-text-grey">Platzierungen und Ligapunkte werden angezeigt, sobald jedes Team mindestens ein Spiel
        gespielt hat.</p>
<?php } // endif?>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-centered <?= (($teamcenter or $ligacenter) && $spielplan->turnier->details['phase'] == 'ergebnis') ? 'w3-pale-green' : 'w3-striped' ?>">
        <tr class="w3-primary">
            <th class="w3-center"><i class="material-icons">bar_chart</i><br>Platz</th>
            <th><i class="material-icons">group</i><br>Team</th>
            <th class="w3-hide-small"><i class="material-icons">sports_hockey</i><br>Spiele</th>
            <th class="w3-hide-small"><i class="material-icons">workspaces</i><br>Punkte</th>
            <th class="w3-hide-small"><i class="material-icons">add</i><br>Tore</th>
            <th class="w3-hide-small"><i class="material-icons">remove</i><br>Gegentore</th>
            <th class="w3-hide-small"><i class="material-icons">drag_handle</i><br>Differenz</th>
            <th><i class="material-icons">emoji_events</i><br>Ligapunkte</th>
        </tr>
        <?php foreach ($spielplan->platzierungstabelle as $team_id => $x) { ?>
            <tr>
                <td class="w3-center" style="white-space: nowrap;">
                    <?php  if (!$spielplan->check_tabelle_einblenden()){ ?>
                        <span>--</span>
                    <?php }elseif($spielplan->check_penalty_team($team_id)){ ?>
                        <span class='w3-text-secondary'>
                            <i class='material-icons'>priority_high</i>PENALTY
                        </span>
                    <?php }else{ ?>
                        <?= $x['platz'] ?>
                    <?php } // end if ?>
                </td>
                <td class="w3-center" style="white-space: nowrap;">
                    <?= $x["teamname"] ?>
                </td>
                <td class="w3-hide-small"><?= $x['statistik']["spiele"] ?></td>
                <td class="w3-hide-small"><?= $x['statistik']["punkte"] ?></td>
                <td class="w3-hide-small"><?= $x['statistik']["tore"] ?></td>
                <td class="w3-hide-small"><?= $x['statistik']["gegentore"] ?></td>
                <td class="w3-hide-small"><?= $x['statistik']["tordifferenz"] ?></td>
                <td><?= ($spielplan->check_penalty_team($team_id) or !$spielplan->check_tabelle_einblenden()) ? '--' : $x["ligapunkte"] ?></td>
            </tr>
        <?php }//end foreach?>
    </table>
</div>

<!-- Modal -->
<span class="w3-button w3-hide-large w3-hide-medium w3-text-primary"
      onclick="document.getElementById('tabelle_details_mobile').style.display='block'"><i
            class='material-icons'>info</i> Details anzeigen</span>
<div id="tabelle_details_mobile" class="w3-modal">
    <div class="w3-modal-content w3-card-4">
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-centered w3-striped">
                <tr class="w3-primary">
                    <th><i class="material-icons">bar_chart</i><br>Platz</th>
                    <th><i class="material-icons">group</i><br>Team</th>
                    <th><i class="material-icons">sports_hockey</i><br>Spiele</th>
                    <th><i class="material-icons">workspaces</i><br>Punkte</th>
                    <th><i class="material-icons">add</i><br>Tore</th>
                    <th><i class="material-icons">remove</i><br>Gegentore</th>
                    <th><i class="material-icons">drag_handle</i><br>Differenz</th>
                    <th><i class="material-icons">emoji_events</i><br>Ligapunkte</th>
                </tr>
                <?php foreach ($spielplan->platzierungstabelle as $team_id => $x) { ?>
                    <tr>
                        <td><?= ($spielplan->check_tabelle_einblenden()) ? $x['platz'] : '--' ?></td>
                        <td style="white-space: nowrap;"><?= $x["teamname"] ?></td>
                        <td><?= $x['statistik']["spiele"] ?></td>
                        <td><?= $x['statistik']["punkte"] ?></td>
                        <td><?= $x['statistik']["tore"] ?></td>
                        <td><?= $x['statistik']["gegentore"] ?></td>
                        <td><?= $x['statistik']["tordifferenz"] ?></td>
                        <td><?= (($x['penalty'] && empty($x['statistik']['penalty_diff'])) or !$spielplan->check_tabelle_einblenden()) ? '--' : $x["ligapunkte"] ?></td>
                    </tr>
                <?php }//end foreach?>
            </table>
        </div>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById('tabelle_details_mobile');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

