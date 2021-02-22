<!-- ABSCHLUSSTABELLE -->
<h3 class="w3-text-secondary w3-margin-top">Tabelle</h3>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-centered w3-striped">
        <tr class="w3-primary">
            <th>
                <i class="material-icons">bar_chart</i>
                <br>Platz
            </th>
            <th>
                <i class="material-icons">group</i>
                <br>Team
            </th>
            <th class="w3-hide-small">
                <i class="material-icons">sports_hockey</i>
                <br>Spiele
            </th>
            <th>
                <i class="material-icons">workspaces</i>
                <br>Punkte
            </th>
            <th class="w3-hide-small">
                <i class="material-icons">drag_handle</i>
                <br>Differenz
            </th>
            <th class="w3-hide-small">
                <i class="material-icons">add</i>
                <br>Tore
            </th>
            <th class="w3-hide-small">
                <i class="material-icons">remove</i>
                <br>Gegentore
            </th>
            <?php if (in_array($spielplan->turnier->details['art'], ['I', 'II', 'III'])) { ?>
                <th>
                    <i class="material-icons">emoji_events</i>
                    <br>Ligapunkte
                </th>
            <?php } //end if ?>
        </tr>
        <?php foreach ($spielplan->platzierungstabelle as $team_id => $x) { ?>
            <tr>
                <td style="white-space: nowrap;">
                    <?php if (!$spielplan->check_tabelle_einblenden()) { ?>
                        <span>--</span>
                    <?php } elseif ($spielplan->check_penalty_team($team_id)) { ?>
                        <span class='w3-text-secondary'>
                            <i class='material-icons'>priority_high</i>PENALTY
                        </span>
                    <?php } else { ?>
                        <?= $x['platz'] ?>
                    <?php } // end if ?>
                </td>
                <td style="white-space: nowrap;">
                    <?= $x["teamname"] ?>
                </td>
                <td class="w3-hide-small"><?= $x['statistik']["spiele"] ?></td>
                <td><?= $x['statistik']["punkte"] ?? '--' ?></td>
                <td class="w3-hide-small"><?= $x['statistik']["tordifferenz"] ?? '--' ?></td>
                <td class="w3-hide-small"><?= $x['statistik']["tore"] ?? '--' ?></td>
                <td class="w3-hide-small"><?= $x['statistik']["gegentore"] ?? '--' ?></td>
                <?php if (in_array($spielplan->turnier->details['art'], ['I', 'II', 'III'])) { ?>
                    <td>
                        <?= ($spielplan->check_penalty_team($team_id) || !$spielplan->check_tabelle_einblenden())
                            ? '--'
                        : $x["ligapunkte"] ?>
                    </td>
                <?php } //end if ?>
            </tr>
        <?php }//end foreach?>
    </table>
</div>

<!-- Modal -->
<span class="w3-button w3-hide-large w3-hide-medium w3-text-primary" onclick="document.getElementById('tabelle_details_mobile').style.display='block'">
    <i class='material-icons'>info</i> Details anzeigen
</span>
<div id="tabelle_details_mobile" class="w3-modal">
    <div class="w3-modal-content w3-card-4">
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-centered w3-striped">
                <tr class="w3-primary">
                    <th>
                        <i class="material-icons">bar_chart</i>
                        <br>Platz
                    </th>
                    <th>
                        <i class="material-icons">group</i>
                        <br>Team
                    </th>
                    <th>
                        <i class="material-icons">sports_hockey</i>
                        <br>Spiele
                    </th>
                    <th>
                        <i class="material-icons">workspaces</i>
                        <br>Punkte
                    </th>
                    <th>
                        <i class="material-icons">drag_handle</i>
                        <br>Differenz
                    </th>
                    <th>
                        <i class="material-icons">add</i>
                        <br>Tore
                    </th>
                    <th>
                        <i class="material-icons">remove</i>
                        <br>Gegentore
                    </th>
                    <?php if (in_array($spielplan->turnier->details['art'], ['I', 'II', 'III'])) { ?>
                        <th>
                            <i class="material-icons">emoji_events</i>
                            <br>Ligapunkte
                        </th>
                    <?php } //end if ?>
                </tr>
                <?php foreach ($spielplan->platzierungstabelle as $team_id => $x) { ?>
                    <tr>
                        <td><?= ($spielplan->check_tabelle_einblenden()) ? $x['platz'] : '--' ?></td>
                        <td style="white-space: nowrap;"><?= $x["teamname"] ?></td>
                        <td><?= $x['statistik']["spiele"] ?></td>
                        <td><?= $x['statistik']["punkte"] ?? '--' ?></td>
                        <td><?= $x['statistik']["tordifferenz"] ?? '--' ?></td>
                        <td><?= $x['statistik']["tore"] ?? '--' ?></td>
                        <td><?= $x['statistik']["gegentore"] ?? '--' ?></td>
                        <?php if (in_array($spielplan->turnier->details['art'], ['I', 'II', 'III'])) { ?>
                            <td>
                                <?= ($spielplan->check_penalty_team($team_id) || !$spielplan->check_tabelle_einblenden())
                                    ? '--'
                                    : $x["ligapunkte"] ?>
                            </td>
                        <?php } //end if ?>
                    </tr>
                <?php }//end foreach?>
            </table>
        </div>
    </div>
</div>

<script>
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == document.getElementById('tabelle_details_mobile')) {
            document.getElementById('tabelle_details_mobile').style.display = "none";
        }
    }
</script>

