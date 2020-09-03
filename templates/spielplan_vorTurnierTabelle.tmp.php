<!-- ÜBERSCHRIFT -->
<h1 class="w3-text-primary w3-border-primary">Spielplan<span class="w3-right w3-text-secondary"><?=$spielplan->akt_turnier->daten['ort']?> <i>(<?=$spielplan->akt_turnier->daten['tblock']?>)</i>, <?=date("d.m.Y", strtotime($spielplan->akt_turnier->daten['datum']))?></span></h1>
<h1 class=""><?=$spielplan->akt_turnier->daten['tname']?></h1>

<!-- LINKS -->
<div class="drucken-hide">
    <p><?=Form::link("../liga/turnier_details.php?turnier_id=" . $turnier_id, "<i class='material-icons'>info</i> Alle Turnierdetails</i>")?></p>
    <?php if(isset($_SESSION['la_id']) && !$ligacenter){?>
        <p><?=Form::link($spielplan->akt_turnier->get_lc_spielplan(), '<i class="material-icons">create</i> Ergebnisse eintragen (Ligaausschuss)')?></p>
    <?php }//endif?>
    <?php if(isset($_SESSION['la_id'])){?>
        <p><?=Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">create</i> Turnierreport ausfüllen (Ligaausschuss)')?></p>
    <?php }//endif?>
    <?php if(($_SESSION['team_id'] ?? false) == $spielplan->akt_turnier->daten['ausrichter']){?>
        <?php if(!$teamcenter){?><p><?=Form::link($spielplan->akt_turnier->get_tc_spielplan(), '<i class="material-icons">create</i> Ergebnisse eintragen')?></p><?php }//endif?>
        <p><?=Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">create</i> Turnierreport ausfüllen')?></p>
    <?php }//endif?>
</div>

<!-- TEAMLISTE -->
<h3 class="w3-text-secondary w3-margin-top">Teamliste</h3>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped" style="white-space: nowrap;">
        <tr class="w3-primary">
            <th class="w3-center">ID</th>
            <th>Name</th>
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