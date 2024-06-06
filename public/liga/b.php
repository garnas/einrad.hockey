<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

// Turnier-ID
$turnier_id = 1191;

//// Gibt es einen Spielplan zu diesem Turnier?
//if (!Spielplan::check_exist($turnier_id)) {
//    Helper::not_found("Spielplan wurde nicht gefunden");
//}

// Spielplan laden
$turnier = nTurnier::get($turnier_id);
$datum =
    strftime("%d.%m.", strtotime($turnier->get_datum()))
    . " & "
    . strftime("%d.%m.", strtotime($turnier->get_datum()) + 24*60*60) ;
$spielplan = (new spielplan_final($turnier))->get_spielplan_b_2024();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Spielplan | Einradhockey";
Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->get_ort()
    . " am " . $datum;

include '../../templates/header.tmp.php';
if ($turnier->get_phase() == "ergebnis") {
    Html::set_confetti();
}

?>
    <!-- Überschrift -->
    <h1 class="w3-text-grey"><?= $spielplan->details['plaetze'] ?>er-Spielplan</h1>
    <h2 class="w3-text-primary">
        <?= $spielplan->turnier->get_ort() ?>
        <i>(<?= $spielplan->turnier->get_tblock() ?>)</i>, <?= $datum ?>
    </h2>
    <h3><?= $spielplan->turnier->get_tname() ?></h3>
<?php if ($spielplan->out_of_scope) {
    Html::message("notice",
        "Achtung es muss eine zweite Runde Penaltys gespielt werden. Bitte vermerkt dies im Turnierbericht und
                 tragt die Penaltys so ein, dass die Turniertabelle am Ende stimmt.",
        "Zweite Runde Penaltys");
} // end if?>
    <!-- Links -->
    <div class="pdf-hide">
        <?= Html::link("../liga/turnier_details.php?turnier_id=" . $spielplan->turnier->get_turnier_id(), "Alle Turnierdetails", true, 'launch') ?>
        <?php if (isset($_SESSION['logins']['team'])) { ?>
            <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $spielplan->turnier->get_turnier_id(), 'Zum Turnierreport', true, 'launch') ?>
        <?php } else { ?>
            <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $spielplan->turnier->get_turnier_id(), 'Zum Turnierreport', true, 'launch') ?>
        <?php } // endif?>
        <?php if (($_SESSION['logins']['team']['id'] ?? 0) == $spielplan->turnier->get_ausrichter() && !(Helper::$teamcenter ?? false) && $spielplan->turnier->get_phase() == 'spielplan') { ?>
            <?= Html::link($spielplan->turnier->get_spielplan_link('tc'), 'Ergebnisse eintragen', true, 'launch') ?>
        <?php }// endif?>
        <?php if (isset($_SESSION['logins']['la']) && !(Helper::$ligacenter ?? false)) { ?>
            <?= Html::link($spielplan->turnier->get_spielplan_link('lc'), 'Ergebnisse eintragen (Ligaausschuss)', true, 'launch') ?>
        <?php }// endif?>
        <?php if (isset($_SESSION['logins']['la'])) { ?>
            <?= Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $spielplan->turnier->get_turnier_id(), 'Turnierreport ausfüllen (Ligaausschuss)', true, 'launch') ?>
        <?php }// endif?>
    </div>

    <!-- Penalty-Warnungen -->
<?php if (!empty($spielplan->get_penalty_warnung())) { ?>
    <div class="pdf-hide">
        <?php Html::message('notice', $spielplan->get_penalty_warnung(), 'Penalty', false) ?>
    </div>
<?php } // endif?>

    <p>
        <b>
            <?= Html::link('https://einrad.hockey/spielplan_augustdorf.pdf', 'PDF-Spielplan', true, 'download')?>
        </b>
    </p>
<?php
include '../../templates/spielplan/spielplan_teamliste.tmp.php'; // Teamliste
if (Env::ACTIVE_FINAL_DISCORD) {
    include '../../templates/spielplan/spielplan_discord_read.tmp.php'; // Spiele
}
?>
    <!-- Spielzeiten -->
    <h1 class="w3-text-secondary">Vorrunde</h1>
    <span class="w3-text-grey w3-margin-top">
    Spielzeit: <?= $spielplan->details['anzahl_halbzeiten'] ?> x <?= $spielplan->details['halbzeit_laenge'] ?>&nbsp;min
    | Puffer: <?= $spielplan->details['puffer'] ?>&nbsp;min
</span>
    <div class="w3-responsive w3-card">
    <table class="w3-table w3-centered w3-striped">
    <tr class="w3-primary">
        <!-- DM Uhr -->
        <th>
            <?= Html::icon("home") ?>
            <br>
            Halle
        </th>
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
        <?php if ($spielplan->check_penalty_anzeigen()) { ?>
            <th>
                <?= Html::icon("priority_high") ?>
                <br>
                Penalty
            </th>
        <?php }//endif?>
    </tr>
<?php if ($spielplan->turnier->get_besprechung() === 'Ja') { ?>
    <tr class="w3-primary-3">
        <td><?= date('H:i', strtotime($spielplan->turnier->get_startzeit()) - 15 * 60) ?></td>
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
        <td><b><?= ($spiel_id % 2) ? 1 : 2 ?></b></td>
        <!-- Uhrzeit -->
        <td><?= $spiel["zeit"] ?></td>
        <!-- Schiri -->
        <td>
            <div class="w3-tooltip" style="cursor: help;">
                <!-- Desktop -->
                <table class="w3-table w3-centered w3-hide-small" style="width: auto; margin: auto;">
                    <tr>
                        <td style="width: 30px; padding:0;">
                            <i class="w3-text-primary"><?= Team::id_to_name($spiel["schiri_team_id_a"]) ?></i>
                        </td>
                        <td style="width: 30px; padding:0;">|</td>
                        <td style="width: 30px; padding:0;">
                            <i class="w3-text-primary"><?= $spiel["schiri_team_id_b"] ?></i>
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
                            <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_a"]]['teamname'] ?? Team::id_to_name($spiel["schiri_team_id_a"]) ?>
                            |
                            <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_b"]]['teamname'] ?? Team::id_to_name($spiel["schiri_team_id_a"]) ?>
                        </span>
                <!-- Mobil -->
                <span class="pdf-hide w3-hide-medium w3-hide-large w3-text-primary w3-hover-text-secondary">
                            <i><?= Team::id_to_name($spiel["schiri_team_id_a"]) ?></i>
                            <br class="pdf-hide">
                            <i><?= $spiel["schiri_team_id_b"] ?></i>
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
            <!-- Pen Desktop -->
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
        <?php } //endif?>
    </tr>
    <?php if ($spielplan->get_pause($spiel_id) > 0) { ?>
        <tr>
            <td></td>
            <td>
                <?= date("H:i",
                    strtotime($spielplan->spiele[$spiel_id+1]['zeit'])
                    - $spielplan->get_pause($spiel_id) * 60) ?>
            </td>
            <td></td>
            <td></td>
            <td style="white-space: nowrap;" colspan="3">
                <?= Html::icon("schedule") ?>
                <i><?= $spielplan->get_pause($spiel_id) ?>&nbsp;min Pause</i>
                <?= Html::icon("schedule") ?>
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
<?php

include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Abschlusstabelle
include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
?>
    <h1 class="w3-text-secondary">
        Finalspiele
    </h1>
    <div class="w3-center">
        <iframe
            width="100%"
            height="620px"
            style="max-width: 840px;"
            class="w3-leftbar w3-topbar w3-rightbar w3-bottombar w3-border-primary w3-card-4"
            src="https://docs.google.com/spreadsheets/d/e/2PACX-1vRGdeTbbzUs0HTz1bkTa8Yr4ccXynpz9RD4vmwKpQAM4GmM44f9m6IqGYrMi7LRqFU5qeXP8mUso378/pubhtml?gid=0&amp;single=true&amp;widget=true&amp;headers=true"></iframe>
    </div>
<?php
include '../../templates/footer.tmp.php';

