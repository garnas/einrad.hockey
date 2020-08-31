<!-- 
TODO:
- Datum und Ort des Turniers in den "Spielplan" einfügen
- Title entsprechend den anderen anpassen
-->
<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
//require_once '../../logic/session_la.logic.php';//Auth

$turnier_id = 914;
//$turnier_id=$_GET['turnier_id'];
$akt_turnier=new Turnier($turnier_id);
//Existiert das Turneir??
if(empty($akt_turnier->daten)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location : ../irgendeine/url.php');
    die();
}
$spielplan = new Spielplan($turnier_id);
$spielplan->create_spielplan_jgj();
$tabelle=$spielplan->get_turnier_tabelle();
$teamliste=$spielplan->teamliste;
$spielliste=$spielplan->get_spiele();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Spielplan";

include '../../templates/header.tmp.php';
?>
<div class="">
<h1 class="w3-text-primary w3-border-primary">Spielplan</h1>

<!-- TEAMLISTE -->
<h3 class="w3-text-secondary w3-margin-top">Teamliste</h3>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th class="w3-right-align">Team ID</th>
                <th>Teamname</th>
                <th class="w3-right-align">Teamblock</th>
                <th class="w3-right-align">Wertigkeit</th>
            </tr>

        <?php foreach ($teamliste as $index => $team){?>
            <tr>
            <td class="w3-right-align"><?= $team["team_id"]?></td>
            <td><?= $team["teamname"]?></td>
            <td class="w3-right-align"><?= $team["tblock"]?></td>
            <td class="w3-right-align"><?= $team["wertigkeit"]?></td>
            </tr>
        <?php }//end foreach?>
        </table>
    </div>

<!-- SPIELE -->
<h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped ">
            <tr class="w3-primary">
                <th class="w3-right-align">Zeit</th>
                <th colspan="2" class="w3-center">Schiri</th>
                <th>Team 1</th>
                <th>Team 2</th>
                <th colspan="3" class="w3-center">Ergebnis</th>
                <th colspan="3" class="w3-center">Penalty</th>
            </tr>
            <?php foreach ($spielliste as $index => $spiel){?>
                <tr>
                <td class="w3-right-align"><?=$spiel["zeit"]?></td>
                <td class="w3-right-align"><?=$spiel["schiri_team_id_a"]?></td>
                <td class="w3-right-align"><?=$spiel["schiri_team_id_b"]?></td>
                <td><?=$spiel["team_a_name"]?></td>
                <td><?=$spiel["team_b_name"]?></td>
                <td class="w3-right-align" style="padding-right: 0;"><?=$spiel["tore_a"]?></td>
                <td class="w3-center" style="padding-left: 0; padding-right: 0;">:</td>
                <td class="w3-left-align" style="padding-left: 0;"><?=$spiel["tore_b"]?></td>
                <td class="w3-right-align" style="padding-right: 0;"><?=$spiel["penalty_a"]?></td>
                <td class="w3-center" style="padding-left: 0; padding-right: 0;">:</td>
                <td class="w3-left-align" style="padding-left: 0;"><?=$spiel["penalty_b"]?></td>
                </tr>
            <?php }//end foreach?>
        </table>
   </div>

<!-- ABSCHLUSSTABELLE -->
<h3 class="w3-text-secondary w3-margin-top">Abschlusstabelle</h3>
   <div class="w3-responsive w3-card">
    <table class="w3-table w3-striped ">
        <tr class="w3-primary">
            <th class="w3-right-align">Pl.</th>
            <th>Mannschaft</th>
            <th class="w3-right-align">Spiele</th>
            <th class="w3-right-align">Punkte</th>
            <th class="w3-right-align">Tore</th>
            <th class="w3-right-align">Gegentore</th>
            <th class="w3-right-align">Differenz</th>
            <th class="w3-right-align">Ligapunkte</th>
        </tr>
        <?php foreach ($tabelle as $index => $table){?>
            <tr>
            <td class="w3-right-align"><?=$index+1?></td>
            <td><?=$table["teamname"]?></td>
            <td class="w3-right-align"><?=$table["spiele"]?></td>
            <td class="w3-right-align"><?=$table["punkte"]?></td>
            <td class="w3-right-align"><?=$table["tore"]?></td>
            <td class="w3-right-align"><?=$table["gegentore"]?></td>
            <td class="w3-right-align"><?=$table["diff"]?></td>
            <td class="w3-right-align"><?=$table["ligapunkte"]?></td>
            </tr>
            <?php }//end foreach?>
        </table>
   </div>
</div>
<?php
include '../../templates/footer.tmp.php';