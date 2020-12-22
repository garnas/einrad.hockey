<!-- ÜBERSCHRIFT -->
<h1 class="w3-text-grey"><?= $spielplan->details['plaetze'] ?>er-Spielplan</h1>
<h2 class="w3-text-primary"><?= $spielplan->turnier->details['ort'] ?>
    <i>(<?= $spielplan->turnier->details['tblock'] ?>)</i>, <?= date("d.m.Y", strtotime($spielplan->turnier->details['datum'])) ?></h2>
<h3><?= $spielplan->turnier->details['tname'] ?></h3>

<!-- LINKS -->
<div class="pdf-hide">
    <p><?= Form::link("../liga/turnier_details.php?turnier_id=" . $turnier_id, "<i class='material-icons'>info</i> Alle Turnierdetails") ?></p>
    <?php if (isset($_SESSION['team_id'])){ ?>
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
    <p><?= Form::link("../liga/spielplan_pdf.php?turnier_id=" . $turnier_id, "<i class='material-icons'>print</i> PDF-Version anzeigen") ?></p>
</div>

<!-- TEAMLISTE -->
<h3 class="w3-text-secondary w3-margin-top">Teamliste</h3>
<div class="w3-responsive w3-card w3-section" style="max-width: 700px;">
    <table class="w3-table w3-striped" style="white-space: nowrap;">
        <tr class="w3-primary">
            <th>ID</th>
            <th class="w3-left-align">Name</th>
            <th class="w3-center w3-hide-small">Block</th>
            <th class="w3-center">Wertigkeit</th>
            <th></th>
        </tr>
        <?php foreach ($spielplan->teamliste as $team_id => $team) { ?>
            <tr>
                <td><?= $team_id ?></td>
                <td><?= $team["teamname"] ?></td>
                <td class="w3-center w3-hide-small"><?= $team["tblock"] ?></td>
                <td class="w3-center"><?= $team["wertigkeit"] ?></td>
                <td><span class="pdf-hide"><?=Form::mailto((new Kontakt($team_id))->get_emails('public'),'E-Mail')?></span></td>
            </tr>
        <?php }//end foreach?>
    </table>
</div>
<?php if (in_array('NL', array_column($spielplan->teamliste, 'tblock'))) { ?>
    <span class="w3-text-grey">* Nichtligateam</span>
<?php } //endif?>
