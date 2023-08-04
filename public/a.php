<?php

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../init.php';
header("Refresh: " . 5);
// Turnier-ID

$teamliste = [
    1 => "Swiss Team",
    2 => "Deutschland 1",
    3 => "Deutschland 2",
    4 => "Swiss Team 2",
    5 => "Deutschland 3",
    6 => "Aussie Deutsch United",
    7 => "Deutschland 4",
    8 => "B&B"
];
$schiri = [
    1 => "Sw1",
    2 => "De1",
    3 => "De2",
    4 => "Sw2",
    5 => "De3",
    6 => "AuD",
    7 => "De4",
    8 => "B&B",
];
$vorlage = "euhc_a";
$turnier_id = 1;
$startzeit = "10:00:00";
require_once Env::BASE_PATH . '/logic/spielplan_euhc.logic.php';
require_once Env::BASE_PATH . '/logic/spielplan_euhc.logic.php';
$spiele_backup = $spielplan->spiele;
$spiele_freitag = array_slice($spiele_backup, 0, 20, preserve_keys: true);
$spiele_samstag = array_slice($spiele_backup, 20, preserve_keys: true);
include Env::BASE_PATH . '/templates/header_euhc.tmp.php'; ?>


<div class="w3-auto" style="margin-top: 10%">
    <h1 class="w3-text-grey w3-xxxlarge">EUHC A-Turnier</h1>
    <div id="tabelle_details_mobile" class="">
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-centered w3-striped w3-xlarge">
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
                    <?php if (in_array($spielplan->turnier->get_art(), Config::TURNIER_ARTEN)) { ?>
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
                        <?php if (in_array($spielplan->turnier->get_art(), Config::TURNIER_ARTEN)) { ?>
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
    <?php
    include Env::BASE_PATH . '/templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
    ?>
</div>
