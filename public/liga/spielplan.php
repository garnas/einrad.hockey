<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php';//Auth

$turnier_id = 913;
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
//$spielplan->update_spiel(1, 3,4,NULL,NULL);
$tabelle=$spielplan->get_turnier_tabelle();
$teamliste=$spielplan->teamliste;
$spielliste=$spielplan->get_spiele();
db::debug($tabelle);
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Spielplan";

include '../../templates/header.tmp.php';
?>
<div  class="w3-white w3-container w3-card-4">
<h3>Spielplan:</h3>
    <div class="w3-responsive w3-section ">
        <table class="w3-table w3-striped ">
            <tr class="w3-primary">
                <th>Team ID</th>
                <th >Teamname</th>
                <th >Teamblock</th>
                <th >Wertigkeit</th>
            </tr>

        <?php foreach ($teamliste as $index => $team){?>
            <tr>
            <td><?= $team["team_id"]?></td>
            <td><?= $team["teamname"]?></td>
            <td><?= $team["tblock"]?></td>
            <td><?= $team["wertigkeit"]?></td>
            </tr>
        <?php }//end foreach?>
        </table>
    </div>
    <div class="w3-responsive w3-section">
        <table class="w3-table w3-striped ">
            <tr class="w3-primary">
                <th>Zeit</th>
                <th colspan="2">Schiri</th>
                
                <th >Mannschaft 1</th>
                <th>Mannschaft 2</th>
                <th>Tore 1</th>
                <th>Tore 2</th>
                <th>Pen. 1</th>
                <th>Pen. 2</th>
            </tr>
            <?php foreach ($spielliste as $index => $spiel){?>
                <tr>
                <td><?=$spiel["zeit"]?></td>
                <td><?=$spiel["schiri_team_id_a"]?></td>
                <td><?=$spiel["schiri_team_id_b"]?></td>
                <td><?=$spiel["team_a_name"]?></td>
                <td><?=$spiel["team_b_name"]?></td>
                <td><?=$spiel["tore_a"]?></td>
                <td><?=$spiel["tore_b"]?></td>
                <td><?=$spiel["penalty_a"]?></td>
                <td><?=$spiel["penalty_b"]?></td>
                </tr>
            <?php }//end foreach?>
        </table>
   </div>
   <div class="w3-responsive w3-section">
    <table class="w3-table w3-striped ">
        <tr class="w3-primary">
            <th>Platzierung</th>
            <th>Mannschaft</th>
            <th >Spiele</th>
            <th >Tore</th>
            <th >Gegentore</th>
            <th >Differenz</th>
            <th >Punkte</th>
            <th>Ligapunkte</th>
        </tr>
        <?php foreach ($tabelle as $index => $table){?>
            <tr>
            <td><?=$index+1?></td>
            <td><?=$table["team_id_a"]?></td>
            <td><?=$table["spiele"]?></td>
            <td><?=$table["tore"]?></td>
            <td><?=$table["gegentore"]?></td>
            <td><?=$table["diff"]?></td>
            <td><?=$table["punkte"]?></td>
            <td><?=$table["ligapunkte"]?><td>
            </tr>
            <?php }//end foreach?>
        </table>
   </div>





</div>
