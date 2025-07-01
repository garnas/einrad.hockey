<!-- Überschrift -->
<h1 class="w3-text-grey"><?= $spielplan->details->getPlaetze()?>er-Spielplan</h1>
<h2 class="w3-text-primary">
    <?= $spielplan->turnier->getDetails()->getOrt() ?>
    <i>(<?= $spielplan->turnier->getBlock() ?>)</i>, <?= $spielplan->turnier->getDatum()->format('d.m.Y') ?>
</h2>
<h3><?= $spielplan->turnier->getName() ?></h3>
<?php if ($spielplan->out_of_scope) {
    Html::message("notice",
        "Achtung es muss eine zweite Runde Penaltys gespielt werden. Bitte vermerkt dies im Turnierbericht und
                 tragt die Penaltys so ein, dass die Turniertabelle am Ende stimmt.",
        "Zweite Runde Penaltys");
} // end if?>
<!-- Links -->
<div class="pdf-hide">
    <?= Html::link("../liga/turnier_details.php?turnier_id=" . $spielplan->turnier->id(), "Alle Turnierdetails", true, 'launch') ?>
    <?php if (isset($_SESSION['logins']['team'])) { ?>
        <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $spielplan->turnier->id(), 'Zum Turnierreport', true, 'launch') ?>
    <?php } else { ?>
        <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $spielplan->turnier->id(), 'Zum Turnierreport', true, 'launch') ?>
    <?php } // endif?>
    <?php if (($_SESSION['logins']['team']['id'] ?? 0) == $spielplan->turnier->getAusrichter()->id() && !(Helper::$teamcenter ?? false) && $spielplan->turnier->get_phase() == 'spielplan') { ?>
        <?= Html::link($spielplan->turnier->get_spielplan_link('tc'), 'Ergebnisse eintragen', true, 'launch') ?>
    <?php }// endif?>
    <?php if (isset($_SESSION['logins']['la']) && !(Helper::$ligacenter ?? false)) { ?>
        <?= Html::link($spielplan->turnier->get_spielplan_link('lc'), 'Ergebnisse eintragen (Ligaausschuss)', true, 'launch') ?>
    <?php }// endif?>
    <?php if (isset($_SESSION['logins']['la'])) { ?>
        <?= Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $spielplan->turnier->id(), 'Turnierreport ausfüllen (Ligaausschuss)', true, 'launch') ?>
    <?php }// endif?>
    <?= Html::link("../liga/spielplan_pdf.php?turnier_id=" . $spielplan->turnier->id(), "PDF-Version", true, 'print') ?>
</div>

<!-- Penalty-Warnungen -->
<?php if (!empty($spielplan->get_penalty_warnung())) { ?>
    <div class="pdf-hide">
        <?php Html::message('notice', $spielplan->get_penalty_warnung(), 'Penalty', false) ?>
    </div>
<?php } // endif?>
