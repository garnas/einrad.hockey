<!-- Überschrift -->
<h1 class="w3-text-grey"><?= $spielplan->details['plaetze'] ?>er-Spielplan</h1>
<h2 class="w3-text-primary">
    <?= $spielplan->turnier->details['ort'] ?>
    <i>(<?= $spielplan->turnier->details['tblock'] ?>)</i>, <?= date("d.m.Y", strtotime($spielplan->turnier->details['datum'])) ?>
</h2>
<h3><?= $spielplan->turnier->details['tname'] ?></h3>
<?php if ($spielplan->out_of_scope) {
    Form::message("notice",
        "Achtung es muss eine zweite Runde Penaltys gespielt werden. Bitte vermerkt dies im Turnierbericht und
                 tragt die Penaltys so ein, dass die Turniertabelle am Ende stimmt.",
        "Zweite Runde Penaltys");
} // end if?>
<!-- Links -->
<div class="pdf-hide">
    <p><?= Form::link("../liga/turnier_details.php?turnier_id=" . $turnier_id, "<i class='material-icons'>info</i> Alle Turnierdetails") ?></p>
    <?php if (isset($_SESSION['logins']['team'])) { ?>
        <p><?= Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">article</i> Zum Turnierreport') ?></p>
    <?php } else { ?>
        <p><?= Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">lock</i> Zum Turnierreport') ?></p>
    <?php } // endif?>
    <?php if (($_SESSION['logins']['team']['id'] ?? 0) == $spielplan->turnier->details['ausrichter'] && !(Config::$teamcenter ?? false) && $spielplan->turnier->details['phase'] == 'spielplan') { ?>
        <p><?= Form::link($spielplan->turnier->get_spielplan_link('tc'), '<i class="material-icons">create</i> Ergebnisse eintragen') ?></p>
    <?php }// endif?>
    <?php if (isset($_SESSION['logins']['la']) && !(Config::$ligacenter ?? false)) { ?>
        <p><?= Form::link($spielplan->turnier->get_spielplan_link('lc'), '<i class="material-icons">create</i> Ergebnisse eintragen (Ligaausschuss)') ?></p>
    <?php }// endif?>
    <?php if (isset($_SESSION['logins']['la'])) { ?>
        <p><?= Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">create</i> Turnierreport ausfüllen (Ligaausschuss)') ?></p>
    <?php }// endif?>
    <p><?= Form::link("../liga/spielplan_pdf.php?turnier_id=" . $turnier_id, "<i class='material-icons'>print</i> PDF-Version anzeigen", true) ?></p>
</div>

<!-- Penalty-Warnungen -->
<?php if (!empty($spielplan->get_penalty_warnung())) { ?>
    <div class="pdf-hide">
        <?php Form::message('notice', $spielplan->get_penalty_warnung(), 'Penalty', false) ?>
    </div>
<?php } // endif?>
