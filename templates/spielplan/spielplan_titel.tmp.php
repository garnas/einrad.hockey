<!-- Überschrift -->
<h1 class="w3-text-grey"><?= $spielplan->details['plaetze'] ?>er-Spielplan</h1>
<h2 class="w3-text-primary">
    <?= $spielplan->turnier->details['ort'] ?>
    <i>(<?= $spielplan->turnier->details['tblock'] ?>)</i>, <?= date("d.m.Y", strtotime($spielplan->turnier->details['datum'])) ?>
</h2>
<h3><?= $spielplan->turnier->details['tname'] ?></h3>
<?php if ($spielplan->out_of_scope) {
    Html::message("notice",
        "Achtung es muss eine zweite Runde Penaltys gespielt werden. Bitte vermerkt dies im Turnierbericht und
                 tragt die Penaltys so ein, dass die Turniertabelle am Ende stimmt.",
        "Zweite Runde Penaltys");
} // end if?>
<!-- Links -->
<div class="pdf-hide">
    <?= Html::link("../liga/turnier_details.php?turnier_id=" . $turnier_id, "Alle Turnierdetails", true, 'launch') ?>
    <?php if (isset($_SESSION['logins']['team'])) { ?>
        <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, 'Zum Turnierreport', true, 'launch') ?>
    <?php } else { ?>
        <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, 'Zum Turnierreport', true, 'launch') ?>
    <?php } // endif?>
    <?php if (($_SESSION['logins']['team']['id'] ?? 0) == $spielplan->turnier->details['ausrichter'] && !(Config::$teamcenter ?? false) && $spielplan->turnier->details['phase'] == 'spielplan') { ?>
        <?= Html::link($spielplan->turnier->get_spielplan_link('tc'), 'Ergebnisse eintragen', true, 'launch') ?>
    <?php }// endif?>
    <?php if (isset($_SESSION['logins']['la']) && !(Config::$ligacenter ?? false)) { ?>
        <?= Html::link($spielplan->turnier->get_spielplan_link('lc'), 'Ergebnisse eintragen (Ligaausschuss)', true, 'launch') ?>
    <?php }// endif?>
    <?php if (isset($_SESSION['logins']['la'])) { ?>
        <?= Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier_id, 'Turnierreport ausfüllen (Ligaausschuss)', true, 'launch') ?>
    <?php }// endif?>
    <?= Html::link("../liga/spielplan_pdf.php?turnier_id=" . $turnier_id, "PDF-Version", true, 'print') ?>
</div>

<!-- Penalty-Warnungen -->
<?php if (!empty($spielplan->get_penalty_warnung())) { ?>
    <div class="pdf-hide">
        <?php Html::message('notice', $spielplan->get_penalty_warnung(), 'Penalty', false) ?>
    </div>
<?php } // endif?>
