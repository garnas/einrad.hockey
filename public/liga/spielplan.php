<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

/*<!-- 
TODO:
- Datum und Ort des Turniers in den "Spielplan" einfügen
- Title entsprechend den anderen anpassen
-->*/

$turnier_id=$_GET['turnier_id'];
$akt_turnier=new Turnier($turnier_id);
//Existiert das Turnier?
if(empty($akt_turnier->daten)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location : ../public/turniere.php');
    die();
}
//Ist das Turnier in der richtigen Phase?
if(!in_array($akt_turnier->daten['phase'], array('ergebnis', 'spielplan'))){
    Form::error("Turnier befindet sich in der falschen Phase");
    header('Location : ../public/turniere.php');
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
$content = "Der Spielplan für das Einradhockey-Turnier in ". $akt_turnier->daten['ort'] . "am" . date("d.m.Y", strtotime($akt_turnier->daten['datum']));
include '../../templates/header.tmp.php';
?>

<!-- TEAMLISTE -->
<?php
include '../../templates/spielplan_vorTurnierTabelle.tmp.php';
?>

<!-- LINKS -->
<p><?=Form::link("../liga/turnier_details.php?turnier_id=" . $turnier_id, "<i class='material-icons'>info</i> Alle Turnierdetails</i>")?></p>
<?php if(isset($_SESSION['la_id'])){?>
    <p><?=Form::link($akt_turnier->get_lc_spielplan(), '<i class="material-icons">create</i> Ergebnisse eintragen (Ligaausschuss)')?></p>
<?php }//endif?>
<?php if(($_SESSION['team_id'] ?? '') == $akt_turnier->daten['ausrichter']){?>
    <p><?=Form::link($akt_turnier->get_tc_spielplan(), '<i class="material-icons">create</i> Ergebnisse eintragen')?></p>
<?php }//endif?>

<!-- SPIELE -->
<h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
<?php if($akt_turnier->daten['besprechung'] == 'Ja'){?><p><i>Alle Teams sollen sich um <?=date('h:i', strtotime($akt_turnier->daten['startzeit']) - 15*60)?>&nbsp;Uhr zu einer gemeinsamen Turnierbesprechung einfinden.</i></p><?php }//endif?>
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
<?php
include '../../templates/spielplan_ergebnisTabelle.tmp.php';
include '../../templates/footer.tmp.php';