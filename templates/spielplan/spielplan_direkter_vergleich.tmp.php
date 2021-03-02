<?php if (!empty($spielplan->direkter_vergleich_tabellen)){ ?>
    <!-- Buttons zum Ein/Ausblenden der Vergleiche -->
    <div class="w3-section w3-margin-top">
        <div id='button_da'>
            <button  class="w3-primary w3-block w3-button"
                     onclick='modal("vergleichs_tabellen");modal("button_da");modal("button_weg");'>
                <?= Html::icon("keyboard_arrow_down") ?>
                Direkter Vergleich
                <?= Html::icon("keyboard_arrow_down") ?>
            </button>
        </div>
        <div id='button_weg' style='display: none;'>
            <button class="w3-primary w3-card-4 w3-block w3-button"
                    onclick='modal("vergleichs_tabellen");modal("button_da");modal("button_weg");'>
                <?= Html::icon("keyboard_arrow_up") ?>
                Direkter Vergleich
                <?= Html::icon("keyboard_arrow_up") ?>
            </button>
        </div>
    </div>
<?php } //endif ?>

<div id="vergleichs_tabellen" style="display: none">
    <!-- Tabellen für den direkten Vergleich -->
    <h3 class="w3-text-secondary">Direkter Vergleich</h3>
    <?php foreach ($spielplan->direkter_vergleich_tabellen as $direkter_vergleich) { ?>
        <div class="w3-card w3-responsive">
            <table class="w3-table w3-centered">
                <tr class="w3-primary">
                    <th>
                        <?= Html::icon("bar_chart") ?>
                        <br>
                        Platz
                    </th>
                    <th>
                        <?= Html::icon("group") ?>
                        <br>
                        Team
                    </th>
                    <th>
                        <?= Html::icon("sports_hockey") ?>
                        <br>
                        Spiele
                    </th>
                    <th>
                        <?= Html::icon("workspaces") ?>
                        <br>
                        Punkte
                    </th>
                    <th>
                        <?= Html::icon("drag_handle") ?>
                        <br>
                        Differenz
                    </th>
                    <th>
                        <?= Html::icon("add") ?>
                        <br>
                        Tore
                    </th>
                    <th>
                        <?= Html::icon("remove") ?>
                        <br>Gegentore
                    </th>
                </tr>
                <?php foreach ($direkter_vergleich as $team_id => $ergebnis) { ?>
                    <tr>
                        <td><?= $spielplan->platzierungstabelle[$team_id]['platz'] ?></td>
                        <td style="white-space: nowrap"><?= $spielplan->teamliste[$team_id]['teamname'] ?></td>
                        <td><?= $ergebnis['spiele'] ?></td>
                        <td><?= $ergebnis['punkte'] ?></td>
                        <td><?= $ergebnis['tordifferenz'] ?></td>
                        <td><?= $ergebnis['tore'] ?></td>
                        <td><?= $ergebnis['gegentore'] ?></td>
                    </tr>
                <?php } // end foreach ?>
            </table>
        </div>
    <?php }//end foreach ?>
    <!-- Tabellen für den direkten Vergleich -->
    <?php if (!empty($spielplan->penalty_tabellen)){ ?>
        <h3 class="w3-text-secondary">Penalty Vergleich</h3>
    <?php }//end if ?>
    <?php foreach ($spielplan->penalty_tabellen as $penalty) { ?>
        <div class="w3-card w3-responsive">
            <table class="w3-table w3-centered">
                <tr class="w3-primary">
                    <th>
                        <?= Html::icon("bar_chart") ?>
                        <br>
                        Platz
                    </th>
                    <th>
                        <?= Html::icon("group") ?>
                        <br>
                        Team
                    </th>
                    <th>
                        <?= Html::icon("sports_hockey") ?>
                        <br>
                        Penaltys
                    </th>
                    <th>
                        <?= Html::icon("priority_high") ?>
                        <br>
                        Punkte
                    </th>
                    <th>
                        <?= Html::icon("priority_high") ?>
                        <br>
                        Differenz
                    </th>
                    <th>
                        <?= Html::icon("priority_high") ?>
                        <br>
                        Tore
                    </th>
                    <th>
                        <?= Html::icon("priority_high") ?>
                        <br>
                        Gegentore
                    </th>
                </tr>
                <?php foreach ($penalty as $team_id => $ergebnis) { ?>
                    <tr>
                        <td><?= $spielplan->platzierungstabelle[$team_id]['platz'] ?></td>
                        <td style="white-space: nowrap"><?= $spielplan->teamliste[$team_id]['teamname'] ?></td>
                        <td><?= $ergebnis['penalty_spiele'] ?></td>
                        <td>
                            <?= $ergebnis['penalty_punkte'] ?? "--" ?>
                        </td>
                        <td>
                            <?= $ergebnis['penalty_diff'] ?? "--" ?></td>
                        <td>
                            <?= $ergebnis['penalty_tore'] ?? "--" ?>
                        </td>
                        <td>
                            <?= $ergebnis['penalty_gegentore'] ?? "--" ?>
                        </td>
                    </tr>
                <?php } // end foreach ?>
            </table>
        </div>
    <?php }//end foreach ?>
</div>
