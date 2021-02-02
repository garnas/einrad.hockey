<?php if ($spielplan->check_tabelle_einblenden()){ ?>

    <?php foreach ($spielplan->direkter_vergleich_tabellen as $direkter_vergleich) { ?>
        <!-- Tabellen für den direkten Vergleich -->
        <h4 class="w3-text-secondary">Direkter Vergleich</h4>
        <div class="w3-card w3-responsive">
            <table class="w3-table w3-centered">
                <tr class="w3-primary">
                    <th>
                        <i class="material-icons">bar_chart</i>
                        <br>
                        Platz
                    </th>
                    <th>
                        <i class="material-icons">group</i>
                        <br>
                        Team
                    </th>
                    <th>
                        <i class="material-icons">sports_hockey</i>
                        <br>
                        Spiele
                    </th>
                    <th>
                        <i class="material-icons">workspaces</i>
                        <br>
                        Punkte
                    </th>
                    <th>
                        <i class="material-icons">add</i>
                        <br>
                        Tore
                    </th>
                    <th>
                        <i class="material-icons">remove</i>
                        <br>Gegentore
                    </th>
                    <th>
                        <i class="material-icons">drag_handle</i>
                        <br>
                        Differenz
                    </th>
                    <th >
                        <i class="material-icons">priority_high</i>
                        <br>
                        Pen. Punkte
                    </th>
                    <th >
                        <i class="material-icons">priority_high</i>
                        <br>
                        Pen. Diff
                    </th>
                    <th >
                        <i class="material-icons">priority_high</i>
                        <br>
                        Pen. Tore
                    </th>
                </tr>
                <?php $platz = 1; foreach ($direkter_vergleich as $team_id => $ergebnis) { ?>
                    <tr>
                        <td><?= $platz++ ?></td>
                        <td><?= $spielplan->teamliste[$team_id]['teamname'] ?></td>
                        <td><?= $ergebnis['spiele'] ?></td>
                        <td><?= $ergebnis['punkte'] ?></td>
                        <td><?= $ergebnis['tore'] ?></td>
                        <td><?= $ergebnis['gegentore'] ?></td>
                        <td><?= $ergebnis['tordifferenz'] ?></td>
                        <td>
                            <?= $ergebnis['penalty_punkte'] ?? "--" ?>
                        </td>
                        <td>
                            <?= $ergebnis['penalty_diff'] ?? "--" ?></td>
                        <td>
                            <?= $ergebnis['penalty_tore'] ?? "--" ?>
                        </td>
                    </tr>
                <?php } // end foreach ?>
            </table>
        </div>
    <?php }//end foreach ?>

<?php } //endif?>
