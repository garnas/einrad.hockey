<!-- Überschrift -->
<h1 class="w3-text-grey"><?= $spielplan->details['plaetze'] ?>er-Spielplan</h1>
<h2 class="w3-text-primary"><?= $spielplan->turnier->details['ort'] ?>
    <i>(<?= $spielplan->turnier->details['tblock'] ?>)</i>, <?= date("d.m.Y", strtotime($spielplan->turnier->details['datum'])) ?></h2>
<h3><?= $spielplan->turnier->details['tname'] ?></h3>

<!-- Links -->
<div class="pdf-hide">
    <p><?= Form::link("../liga/turnier_details.php?turnier_id=" . $turnier_id, "<i class='material-icons'>info</i> Alle Turnierdetails") ?></p>
    <?php if (isset($_SESSION['team_id'])) { ?>
        <p><?= Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">article</i> Zum Turnierreport') ?></p>
    <?php } else { ?>
        <p><?= Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">lock</i> Zum Turnierreport') ?></p>
    <?php } // endif?>
    <?php if (($_SESSION['team_id'] ?? false) == $spielplan->turnier->details['ausrichter'] && !($teamcenter ?? false) && $spielplan->turnier->details['phase'] == 'spielplan') { ?>
        <p><?= Form::link($spielplan->turnier->get_spielplan_link_tc(), '<i class="material-icons">create</i> Ergebnisse eintragen') ?></p>
    <?php }// endif?>
    <?php if (isset($_SESSION['la_id']) && !($ligacenter ?? false)) { ?>
        <p><?= Form::link($spielplan->turnier->get_spielplan_link_lc(), '<i class="material-icons">create</i> Ergebnisse eintragen (Ligaausschuss)') ?></p>
    <?php }// endif?>
    <?php if (isset($_SESSION['la_id'])) { ?>
        <p><?= Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">create</i> Turnierreport ausfüllen (Ligaausschuss)') ?></p>
    <?php }// endif?>
    <p><?= Form::link("../liga/spielplan_pdf.php?turnier_id=" . $turnier_id, "<i class='material-icons'>print</i> PDF-Version anzeigen", true) ?></p>
</div>

<!-- Penalty-Warnungen -->
<?php if (!empty($spielplan->get_penalty_warnung())){ ?>
    <div class="pdf-hide">
        <?php Form::schreibe_attention($spielplan->get_penalty_warnung(), 'Penalty') ?>
    </div>
<?php } // endif?>
