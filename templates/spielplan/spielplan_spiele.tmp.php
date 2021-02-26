<h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
<!-- Teambesprechung -->

<!-- Spielzeiten -->
<p class="w3-text-grey">
    Spielzeit: <?= $spielplan->details['anzahl_halbzeiten'] ?> x <?= $spielplan->details['halbzeit_laenge'] ?>&nbsp;min
    | Puffer: <?= $spielplan->details['puffer'] ?>&nbsp;min
</p>

<div class="w3-responsive w3-card">
    <table class="w3-table w3-centered w3-striped">
        <tr class="w3-primary">
            <!-- DM Uhr -->
            <th>
                <i class="material-icons">schedule</i>
                <br>
                Uhr
            </th>
            <!-- DM Schiri -->
            <th>
                <i class="material-icons">sports</i>
                <br>
                Schiri
            </th>
            <!-- D Farbe A -->
            <th class="w3-hide-small"></th>
            <!-- D Team A -->
            <th class="w3-hide-small"></th>
            <!-- D - -->
            <th class="w3-hide-small">
                <i class="material-icons">sports_hockey</i>
                <br>
                Spiele
            </th>
            <!-- D Team B -->
            <th class="w3-hide-small"></th>
            <!-- D Farbe B -->
            <th class="w3-hide-small"></th>
            <!-- M Farben -->
            <th class="w3-hide-large w3-hide-medium"></th>
            <!-- 3xM Teams -->
            <th colspan="3" class="w3-hide-large w3-hide-medium">
                <span class="pdf-hide">
                    <i class="material-icons">sports_hockey</i>
                    <br>
                    Spiele
                </span>
            </th>
            <!-- DM Tore -->
            <th>
                <i class="material-icons">sports_baseball</i>
                <br>
                Tore
            </th>
            <?php if ($spielplan->check_penalty_anzeigen()) { ?>
                <th>
                    <i class="material-icons">priority_high</i>
                    <br>
                    Penalty
                </th>
            <?php }//endif?>
        </tr>
        <?php if ($spielplan->turnier->details['besprechung'] === 'Ja') { ?>
            <tr class="w3-primary-3">
                <td><?= date('H:i', strtotime($spielplan->turnier->details['startzeit']) - 15 * 60) ?></td>
                <td></td>
                <td></td>
                <td colspan="3">
                    <i><span class="w3-hide-small">Gemeinsame </span>Turnierbesprechung</i>
                </td>
                <td></td>
                <td class="w3-hide-small"></td>
                <?php if ($spielplan->check_penalty_anzeigen()) { ?>
                    <td></td>
                <?php } //endif ?>
            </tr>
        <?php }//endif?>
        <?php foreach ($spielplan->spiele as $spiel_id => $spiel) { ?>
            <tr>
                <!-- Uhrzeit -->
                <td><?= $spiel["zeit"] ?></td>
                <!-- Schiri -->
                <td>
                    <div class="w3-tooltip" style="cursor: help;">
                        <!-- Desktop -->
                        <table class="w3-table w3-centered w3-hide-small w3-hover-text-secondary" style="width: auto; margin: auto;">
                            <tr>
                                <td style="width: 30px; padding:0;">
                                    <i class="w3-text-primary"><?= $spiel["schiri_team_id_b"] ?></i>
                                </td>
                                <td style="width: 30px; padding:0;">|</td>
                                <td style="width: 30px; padding:0;">
                                    <i class="w3-text-primary"><?= $spiel["schiri_team_id_a"] ?></i>
                                </td>
                            </tr>
                        </table>
                        <span style="white-space: nowrap; position:absolute; left:38px; bottom:8px;" class="w3-text w3-small w3-primary w3-container">
                            <span class="w3-hide-small">
                               <?= Form::icon("keyboard_arrow_down") ?>
                            </span>
                            <span class="w3-hide-large w3-hide-medium">
                               <?= Form::icon("keyboard_arrow_left") ?>
                            </span>
                            <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_a"]]['teamname'] ?>
                            |
                            <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_b"]]['teamname'] ?>
                        </span>
                        <!-- Mobil -->
                        <span class="pdf-hide w3-hide-medium w3-hide-large w3-text-primary w3-hover-text-secondary">
                            <i><?= $spiel["schiri_team_id_b"] ?></i>
                            <br class="pdf-hide">
                            <i><?= $spiel["schiri_team_id_a"] ?></i>
                        </span>
                    </div>
                </td>
                <!-- Teams Desktop -->
                <td class="w3-hide-small">
                    <?= $spielplan->get_trikot_colors($spiel)[$spiel['team_id_a']] ?? '' ?>
                </td>
                <td style="white-space: nowrap;" class="w3-hide-small">
                    <?= $spiel["teamname_a"] ?>
                </td>
                <td class="w3-hide-small">-</td>
                <td style="white-space: nowrap;" class="w3-hide-small">
                    <?= $spiel["teamname_b"] ?>
                </td>
                <td class="w3-hide-small">
                    <?= $spielplan->get_trikot_colors($spiel)[$spiel['team_id_b']] ?? '' ?>
                </td>
                <!-- Teams Mobil -->
                <td class="w3-center w3-hide-large w3-hide-medium">
                    <?= $spielplan->get_trikot_colors($spiel)[$spiel['team_id_a']]  ?? '' ?>
                    <?= $spielplan->get_trikot_colors($spiel)[$spiel['team_id_b']]  ?? '' ?>
                </td>
                <td colspan="3" class="w3-hide-large w3-hide-medium" style="white-space: nowrap;">
                    <span class="pdf-hide"><?= $spiel["teamname_a"] ?></span>
                    <br class="pdf-hide">
                    <span class="pdf-hide"><?= $spiel["teamname_b"] ?></span>
                </td>
                <td>
                    <!-- Tore Desktop -->
                    <table class="w3-table w3-centered w3-hide-small" style="width: auto; margin: auto;">
                        <tr>
                            <td style="width: 30px; padding:0;">
                                <?= $spiel["tore_a"] ?>
                            </td>
                            <td style="width: 30px; padding:0;">:</td>
                            <td style="width: 30px; padding:0;">
                                <?= $spiel["tore_b"] ?>
                            </td>
                        </tr>
                    </table>
                    <!-- Tore Mobil -->
                    <span class="w3-center w3-hide-large w3-hide-medium">
                        <span class="pdf-hide">
                                <?= $spiel["tore_a"] ?>
                        </span>
                        <br class="pdf-hide">
                        <span class="pdf-hide">
                                <?= $spiel["tore_b"] ?>
                        </span>
                    </span>
                </td>
                <?php if ($spielplan->check_penalty_anzeigen()) { ?>
                    <!-- Pen Dekstop -->
                    <td>
                        <table class="w3-table w3-centered w3-hide-small w3-text-secondary" style="width: auto; margin: auto;">
                            <tr>
                                <td style="width: 30px; padding:0;">
                                    <?= $spiel["penalty_a"] ?>
                                </td>
                                <td style="width: 30px; padding:0;" class="w3-text-black">:</td>
                                <td style="width: 30px; padding:0;">
                                   <?= $spiel["penalty_b"] ?>
                                </td>
                            </tr>
                        </table>
                        <!-- Tore Mobil -->
                        <span class="w3-hide-large w3-hide-medium w3-text-secondary">
                            <span class="pdf-hide">
                                <?= $spiel["penalty_a"] ?>
                            </span>
                            <br class="pdf-hide">
                            <span class="pdf-hide">
                                <?= $spiel["penalty_b"] ?>
                            </span>
                        </span>
                    </td>
                <?php }//endif?>
            </tr>
            <?php if ($spielplan->get_pause($spiel_id) > 0) { ?>
                <tr>
                    <td>
                        <?= date("H:i",
                            strtotime($spielplan->spiele[$spiel_id+1]['zeit'])
                                        - $spielplan->get_pause($spiel_id) * 60) ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td style="white-space: nowrap;" colspan="3">
                        <i class="material-icons">schedule</i>
                        <i><?= $spielplan->get_pause($spiel_id) ?>&nbsp; min Pause</i>
                        <i class="material-icons">schedule</i>
                    </td>
                    <td></td>
                    <td class="w3-hide-small"></td>
                    <?php if ($spielplan->check_penalty_anzeigen()) { ?>
                        <td></td>
                    <?php } //endif ?>
                </tr>
            <?php }// endif?>
        <?php }// end foreach?>
    </table>
</div>