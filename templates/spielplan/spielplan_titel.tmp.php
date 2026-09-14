<?php use App\Service\Turnier\TurnierLinks;

/** @var \App\Model\Spielplan\Spielplan $spielplan */
$turnier = $spielplan->getTurnier();
?>
<!-- Überschrift -->
<h1 class="w3-text-grey"><?= $spielplan->getDetails()->getPlaetze() ?>er-Spielplan</h1>
<h2 class="w3-text-primary">
    <?= $turnier->getDetails()->getOrt() ?>
    <i>(<?= $turnier->getBlock() ?>)</i>, <?= $turnier->getDatum()->format("d.m.Y") ?>
</h2>
<h3><?= $turnier->getName() ?></h3>
<?php if ($spielplan->isOutOfScope()) {
    Html::message(
        "notice",
        "Achtung es muss eine zweite Runde Penaltys gespielt werden. Bitte vermerkt dies im Turnierbericht und
                 tragt die Penaltys so ein, dass die Turniertabelle am Ende stimmt.",
        "Zweite Runde Penaltys",
    );
} // end if?>
<!-- Links -->
<div class="pdf-hide">
    <?= Html::link("../liga/turnier_details.php?turnier_id=" . $turnier->id(), "Alle Turnierdetails", true, 'launch') ?>
    <?php if (isset($_SESSION['logins']['team'])) { ?>
        <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->id(), 'Zum Turnierreport', true, 'launch') ?>
    <?php } else { ?>
        <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->id(), 'Zum Turnierreport', true, 'launch') ?>
    <?php } // endif?>
    <?php if (($_SESSION['logins']['team']['id'] ?? 0) == $turnier->getAusrichter()->id() && !(Helper::$teamcenter ?? false) && $turnier->getPhase() == 'spielplan') { ?>
        <?= Html::link(TurnierLinks::spielplan($turnier, 'tc'), 'Ergebnisse eintragen', true, 'launch') ?>
    <?php }// endif?>
    <?php if (isset($_SESSION['logins']['la']) && !(Helper::$ligacenter ?? false)) { ?>
        <?= Html::link(TurnierLinks::spielplan($turnier, 'lc'), 'Ergebnisse eintragen (Ligaausschuss)', true, 'launch') ?>
    <?php }// endif?>
    <?php if (isset($_SESSION['logins']['la'])) { ?>
        <?= Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier->id(), 'Turnierreport ausfüllen (Ligaausschuss)', true, 'launch') ?>
    <?php }// endif?>
    <?= Html::link("../liga/spielplan_pdf.php?turnier_id=" . $turnier->id(), "PDF-Version", true, 'print') ?>
</div>

<!-- Penalty-Warnungen -->
<?php if (!empty($spielplan->getPenaltyWarnung())) { ?>
    <div class="pdf-hide">
        <?php Html::message('notice', $spielplan->getPenaltyWarnung(), 'Penalty', false) ?>
    </div>
<?php } // endif?>
