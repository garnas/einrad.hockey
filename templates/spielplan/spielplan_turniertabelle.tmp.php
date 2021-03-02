<!-- ABSCHLUSSTABELLE -->
<h2 class="w3-text-secondary w3-margin-top">Tabelle</h2>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-centered w3-striped">
        <tr class="w3-primary">
            <th>
                <?= Html::icon("bar_chart") ?>
                <br>Platz
            </th>
            <th>
                <?= Html::icon("group") ?>
                <br>Team
            </th>
            <th class="w3-hide-small">
                <?= Html::icon("workspaces") ?>
                <br>Punkte
            </th>
            <?php if (in_array($spielplan->turnier->details['art'], ['I', 'II', 'III'])) { ?>
                <th>
                    <?= Html::icon("emoji_events") ?>
                    <br>Ergebnis
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
                            <?= Html::icon("priority_high") ?>PENALTY
                        </span>
                    <?php } else { ?>
                        <?= $x['platz'] ?>
                    <?php } // end if ?>
                </td>
                <td style="white-space: nowrap;">
                    <?= $x["teamname"] ?>
                </td>
                <td class="w3-hide-small"><?= $x['statistik']["punkte"] ?? '--' ?></td>
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
<span class="w3-button w3-text-primary" onclick="document.getElementById('tabelle_details_mobile').style.display='block'">
    <i class='material-icons'>info</i> Details anzeigen
</span>
<div id="tabelle_details_mobile" class="w3-modal">
    <div class="w3-modal-content w3-card-4">
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-centered w3-striped">
                <tr class="w3-primary">
                    <th>
                        <?= Html::icon("bar_chart") ?>
                        <br>Platz
                    </th>
                    <th>
                        <?= Html::icon("group") ?>
                        <br>Team
                    </th>
                    <th>
                        <?= Html::icon("sports_hockey") ?>
                        <br>Spiele
                    </th>
                    <th>
                        <?= Html::icon("workspaces") ?>
                        <br>Punkte
                    </th>
                    <th>
                        <?= Html::icon("drag_handle") ?>
                        <br>Differenz
                    </th>
                    <th>
                        <?= Html::icon("add") ?>
                        <br>Tore
                    </th>
                    <th>
                        <?= Html::icon("remove") ?>
                        <br>Gegentore
                    </th>
                    <?php if (in_array($spielplan->turnier->details['art'], ['I', 'II', 'III'])) { ?>
                        <th>
                            <?= Html::icon("emoji_events") ?>
                            <br>Ergebnis
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
                            <?= Html::icon("priority_high") ?>PENALTY
                        </span>
                            <?php } else { ?>
                                <?= $x['platz'] ?>
                            <?php } // end if ?>
                        </td>
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

