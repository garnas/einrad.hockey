<?php

use App\Repository\Team\TeamRepository;

/** @var \App\Model\Spielplan\Spielplan $spielplan */
$schiri_name = static fn(int $team_id): ?string => ($spielplan->getPlatzierungstabelle()[$team_id] ?? null)?->teamname
    ?? TeamRepository::get()->team($team_id)?->getName();
?>
<!-- Spielzeiten -->
<h1 class="w3-text-secondary">Spiele</h1>
<span class="w3-text-grey w3-margin-top">
    Spielzeit: <?= $spielplan->getDetails()->getAnzahlHalbzeiten() ?> x <?= $spielplan->getDetails()->getHalbzeitLaenge() ?>&nbsp;min
    | Puffer: <?= $spielplan->getDetails()->getPuffer() ?>&nbsp;min
</span>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-centered w3-striped">
        <tr class="w3-primary">
            <!-- DM Uhr -->
            <th>
                <?= Html::icon("schedule") ?>
                <br>
                Uhr
            </th>
            <!-- DM Schiri -->
            <th>
                <?= Html::icon("sports") ?>
                <br>
                Schiri
            </th>
            <!-- D Farbe A -->
            <th class="w3-hide-small"></th>
            <!-- D Team A -->
            <th class="w3-hide-small"></th>
            <!-- D - -->
            <th class="w3-hide-small">
                <?= Html::icon("sports_hockey") ?>
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
                    <?= Html::icon("sports_hockey") ?>
                    <br>
                    Spiele
                </span>
            </th>
            <!-- DM Tore -->
            <th>
                <?= Html::icon("sports_baseball") ?>
                <br>
                Tore
            </th>
            <?php if ($spielplan->zeigePenaltySpalte()) { ?>
                <th>
                    <?= Html::icon("priority_high") ?>
                    <br>
                    Penalty
                </th>
            <?php }//endif?>
        </tr>
        <?php if ($spielplan->getTurnier()->hasBesprechung()) { ?>
            <tr class="w3-primary-3">
                <td><?= date('H:i', $spielplan->getTurnier()->getDetails()->getStartzeit()->getTimestamp() - 15 * 60) ?></td>
                <td></td>
                <td></td>
                <td colspan="3">
                    <i><span class="w3-hide-small">Gemeinsame </span>Turnierbesprechung</i>
                </td>
                <td></td>
                <td class="w3-hide-small"></td>
                <?php if ($spielplan->zeigePenaltySpalte()) { ?>
                    <td></td>
                <?php } //endif?>
            </tr>
        <?php }//endif?>
        <?php foreach ($spielplan->getSpiele() as $spiel_id => $spiel) { ?>
            <?php $farben = $spielplan->getTrikotColors($spiel); ?>
            <tr>
                <!-- Uhrzeit -->
                <td><?= $spiel->getZeit() ?></td>
                <!-- Schiri -->
                <td>
                    <div class="w3-tooltip" style="cursor: help;">
                        <!-- Desktop -->
                        <table class="w3-table w3-centered w3-hide-small" style="width: auto; margin: auto;">
                            <tr>
                                <td style="width: 30px; padding:0;">
                                    <i class="w3-text-primary"><?= $spiel->getSchiriIdB() ?></i>
                                </td>
                                <td style="width: 30px; padding:0;">|</td>
                                <td style="width: 30px; padding:0;">
                                    <i class="w3-text-primary"><?= $spiel->getSchiriIdA() ?></i>
                                </td>
                            </tr>
                        </table>
                        <span style="white-space: nowrap; position:absolute; left:38px; bottom:8px;" class="w3-text w3-small w3-primary w3-container">
                            <span class="w3-hide-small">
                               <?= Html::icon("keyboard_arrow_down") ?>
                            </span>
                            <span class="w3-hide-large w3-hide-medium">
                               <?= Html::icon("keyboard_arrow_left") ?>
                            </span>
                            <?= $schiri_name($spiel->getSchiriIdA()) ?>
                            |
                            <?= $schiri_name($spiel->getSchiriIdB()) ?>
                        </span>
                        <!-- Mobil -->
                        <span class="pdf-hide w3-hide-medium w3-hide-large w3-text-primary w3-hover-text-secondary">
                            <i><?= $spiel->getSchiriIdB() ?></i>
                            <br class="pdf-hide">
                            <i><?= $spiel->getSchiriIdA() ?></i>
                        </span>
                    </div>
                </td>
                <!-- Teams Desktop -->
                <td class="w3-hide-small">
                    <?= $farben[$spiel->getTeamIdA()] ?? '' ?>
                </td>
                <td style="white-space: nowrap;" class="w3-hide-small">
                    <?= $spiel->getTeamnameA() ?>
                </td>
                <td class="w3-hide-small">-</td>
                <td style="white-space: nowrap;" class="w3-hide-small">
                    <?= $spiel->getTeamnameB() ?>
                </td>
                <td class="w3-hide-small">
                    <?= $farben[$spiel->getTeamIdB()] ?? '' ?>
                </td>
                <!-- Teams Mobil -->
                <td class="w3-center w3-hide-large w3-hide-medium">
                    <?= $farben[$spiel->getTeamIdA()] ?? "<span style='height:14px;width:14px;border-radius:50%;display:inline-block;'></span>" ?>
                    <?= $farben[$spiel->getTeamIdB()] ?? "<span style='height:14px;width:14px;border-radius:50%;display:inline-block;'></span>" ?>
                </td>
                <td colspan="3" class="w3-hide-large w3-hide-medium" style="white-space: nowrap;">
                    <span class="pdf-hide"><?= $spiel->getTeamnameA() ?></span>
                    <br class="pdf-hide">
                    <span class="pdf-hide"><?= $spiel->getTeamnameB() ?></span>
                </td>
                <td>
                    <!-- Tore Desktop -->
                    <table class="w3-table w3-centered w3-hide-small" style="width: auto; margin: auto;">
                        <tr>
                            <td style="width: 30px; padding:0;">
                                <?= $spiel->getToreA() ?>
                            </td>
                            <td style="width: 30px; padding:0;">:</td>
                            <td style="width: 30px; padding:0;">
                                <?= $spiel->getToreB() ?>
                            </td>
                        </tr>
                    </table>
                    <!-- Tore Mobil -->
                    <span class="w3-center w3-hide-large w3-hide-medium">
                        <span class="pdf-hide">
                                <?= $spiel->getToreA() ?>
                        </span>
                        <br class="pdf-hide">
                        <span class="pdf-hide">
                                <?= $spiel->getToreB() ?>
                        </span>
                    </span>
                </td>
                <?php if ($spielplan->zeigePenaltySpalte()) { ?>
                    <!-- Pen Desktop -->
                    <td>
                        <table class="w3-table w3-centered w3-hide-small w3-text-secondary" style="width: auto; margin: auto;">
                            <tr>
                                <td style="width: 30px; padding:0;">
                                    <?= $spiel->getPenaltyA() ?>
                                </td>
                                <td style="width: 30px; padding:0;" class="w3-text-black">:</td>
                                <td style="width: 30px; padding:0;">
                                   <?= $spiel->getPenaltyB() ?>
                                </td>
                            </tr>
                        </table>
                        <!-- Tore Mobil -->
                        <span class="w3-hide-large w3-hide-medium w3-text-secondary">
                            <span class="pdf-hide">
                                <?= $spiel->getPenaltyA() ?>
                            </span>
                            <br class="pdf-hide">
                            <span class="pdf-hide">
                                <?= $spiel->getPenaltyB() ?>
                            </span>
                        </span>
                    </td>
                <?php } //endif?>
            </tr>
            <?php if ($spielplan->getPause($spiel_id) > 0) { ?>
                <tr>
                    <td>
                        <?= date("H:i",
                            strtotime(($spielplan->getSpiele()[$spiel_id + 1] ?? null)?->getZeit() ?? '')
                                        - $spielplan->getPause($spiel_id) * 60) ?>
                    </td>
                    <td></td>
                    <td></td>
                    <td style="white-space: nowrap;" colspan="3">
                        <?= Html::icon("schedule") ?>
                        <i><?= $spielplan->getPause($spiel_id) ?>&nbsp; min Pause</i>
                        <?= Html::icon("schedule") ?>
                    </td>
                    <td></td>
                    <td class="w3-hide-small"></td>
                    <?php if ($spielplan->zeigePenaltySpalte()) { ?>
                        <td></td>
                    <?php } //endif?>
                </tr>
            <?php }// endif?>
        <?php }// end foreach?>
    </table>
</div>
