<!-- ÜBERSCHRIFT -->
<h1 class="w3-text-grey"><?=$spielzeit['plaetze']?>er-Spielplan</h1>
<h2 class="w3-text-primary"><?=$spielplan->akt_turnier->details['ort']?> <i>(<?=$spielplan->akt_turnier->details['tblock']?>)</i>, <?=date("d.m.Y", strtotime($spielplan->akt_turnier->details['datum']))?></h2>
<h3><?=$spielplan->akt_turnier->details['tname']?></h3>

<!-- LINKS -->
<div class="pdf-hide">
    <p><?=Form::link("../liga/turnier_details.php?turnier_id=" . $turnier_id, "<i class='material-icons'>info</i> Alle Turnierdetails")?>
    <?php if (isset($_SESSION['team_id'])){?>
        <?=Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">article</i> Zum Turnierreport')?></p>
    <?php }else{ ?>
        <?=Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">lock</i> Zum Turnierreport')?></p>
    <?php } //endif?>
    <?php if(($_SESSION['team_id'] ?? false) == $spielplan->akt_turnier->details['ausrichter'] && !($teamcenter ?? false) && $spielplan->akt_turnier->details['phase'] == 'spielplan'){?>
        <p><?=Form::link($spielplan->akt_turnier->get_tc_spielplan(), '<i class="material-icons">create</i> Ergebnisse eintragen')?></p>
    <?php }//endif?>
    <?php if(isset($_SESSION['la_id']) && !($ligacenter ?? false)){?>
        <p><?=Form::link($spielplan->akt_turnier->get_lc_spielplan(), '<i class="material-icons">create</i> Ergebnisse eintragen (Ligaausschuss)')?></p>
    <?php }//endif?>
    <?php if(isset($_SESSION['la_id'])){?>
        <p><?=Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">create</i> Turnierreport ausfüllen (Ligaausschuss)')?></p>
    <?php }//endif?>
    <p><?=Form::link("../liga/spielplan_pdf.php?turnier_id=" . $turnier_id, "<i class='material-icons'>print</i> PDF-Version anzeigen")?></p>
</div>

<!-- TEAMLISTE -->
<h3 class="w3-text-secondary w3-margin-top">Teamliste</h3>
<div class="w3-responsive w3-card w3-section">
    <table class="w3-table w3-striped" style="white-space: nowrap;">
        <tr class="w3-primary">
            <th class="w3-center">ID</th>
            <th class="w3-left-align">Name</th>
            <th class="w3-center w3-hide-small">Block</th>
            <th class="w3-center">Wertigkeit</th>
        </tr>
        <?php foreach ($teamliste as $index => $team){?>
            <tr>
                <td class="w3-center"><?= $team["team_id"]?></td>
                <td><?= $team["teamname"]?></td>
                <td class="w3-center w3-hide-small"><?= $team["tblock"]?></td>
                <td class="w3-center"><?= $team["wertigkeit"]?></td>
            </tr>
        <?php }//end foreach?>
    </table>
</div>
<?php if(array_search('NL', array_column($teamliste, 'tblock')) != false){ ?>
    <span class="w3-text-grey w3-small">* Nichtligateam</span>
<?php } //endif?>
