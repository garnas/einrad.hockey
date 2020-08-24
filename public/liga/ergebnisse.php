<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

if (isset($_GET['saison']) && is_numeric($_GET['saison'])){
    $saison = $_GET['saison'];
}else{
    $saison = Config::SAISON;
}
$data_ergebnisse = Tabelle::get_all_ergebnisse($saison);

if (empty($data_ergebnisse)){
    Form::affirm("Es wurden noch keine Turnierergebnisse der Saison " . Form::get_saison_string($saison) . " eingetragen");
}
$data_turniere = Turnier::get_all_turniere("WHERE saison='$saison'");

//Farbe für die Plätze auf dem Turnier
$color[0] = "w3-text-tertiary";
$color[1] = "w3-text-grey";
$color[2] = "w3-text-brown";

//Turnierreport Icon
if(isset($_SESSION['team_id'])){
    $icon = 'article';
}else{
    $icon = 'lock';
}
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Turnierergebnisse " . Form::get_saison_string($saison) . " | Deutsche Einradhockeyliga";
$content = 'Hier kann man die Ergebnisse und Tabellen der Saison ' . Form::get_saison_string($saison) . ' sehen.';
include '../../templates/header.tmp.php';
?>

<!--Javascript für Suchfunktion-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
//Turnierergebnisse filtern
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myDIV section").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>

<!--Überschrift-->
<h1 class="w3-text-primary">Turnierergebnisse<br><span class="w3-text-grey">Saison <?=Form::get_saison_string($saison)?></span></h1>

<!-- Ergebnis suchen -->
<div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;" >
    <i class="material-icons">search</i><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;" type="text" placeholder="Ergebnis suchen">
</div>

<!--Turnierergebnisse-->
<div id="myDIV">
    <?php foreach($data_ergebnisse as $turnier_id => $ergebnisse){?>
        <section class="w3-card w3-panel" id="<?=$turnier_id?>">
            <h3>
                <?=date("d.m.Y", strtotime($data_turniere[$turnier_id]['datum']))?> <span class="w3-text-primary"><?=$data_turniere[$turnier_id]['ort']?></span> <i>(<?=$data_turniere[$turnier_id]['tblock']?>)</i>
                <br>
                <span class="<?php if($data_turniere[$turnier_id]['art'] == 'final'){?>w3-text-secondary<?php }else{ ?>w3-text-grey<?php } //endif?>"> <?=$data_turniere[$turnier_id]['tname'] ?? ''?></span> 
            </h3>  
            <div class="w3-responsive w3-margin-left">
                <table class="w3-table w3-striped w3-leftbar w3-border-tertiary" style="max-width: 600px">
                <!--<thead class="">
                        <tr>
                            <th colspan="2"><b>Turnierergebnis</b></th>
                            <th>Punkte</th>
                        </tr>
                    </thead>-->
                    <?php foreach ($ergebnisse as $key => $ergebnis){?>
                        <tr class="<?=$color[$key] ?? ''?>">
                            <td><?=$key + 1 . "."?></td>
                            <td><?=$ergebnis['teamname']?></td>
                            <td><?=$ergebnis['ergebnis'] ?: '-'?></td>
                        </tr> 
                    <?php } //end foreach?>
                </table>
            </div>
            <p class="">
                <?php if ($saison <= 25){?>
                    <?=Form::link('archiv.php','<i class="material-icons">info</i> Details')?>
                <?php }else{ ?>
                    <span class="<?php if(empty($data_turniere[$turnier_id]['link_spielplan'])){?>w3-opacity-max<?php }// end if?>">
                        <?=Form::link($data_turniere[$turnier_id]['link_spielplan'] ?: ('#' . $turnier_id), '<i class="material-icons">info</i> Details')?>
                </span>
                    <?=Form::link("../teamcenter/tc_turnier_report.php?turnier_id=$turnier_id", ' <i class="material-icons">' . $icon . '</i> Turnierreport')?>
                    <?php if(isset($_SESSION['la_id'])){?><?=Form::link("../ligacenter/lc_turnier_report.php?turnier_id=$turnier_id", '<i class="material-icons">article</i> Turnierreport (Ligaausschuss)')?><?php }//endif?>
                <?php }//end if?>
            </p>
        </section>
    <?php } //end foreach?>
</div>

<?php include '../../templates/footer.tmp.php';