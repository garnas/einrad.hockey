<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$alle_teamdaten = Team::get_teamdata_all_teams();
$emails = Kontakt::get_all_public_emails_per_team();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Teamliste | Deutsche Einradhockeyliga";
$content = "Liste der Teams der Deutschen Einradhockeyliga mit Teamfoto und Kontaktmöglichkeit.";
include '../../templates/header.tmp.php';
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
//Turnierergebnisse filtern
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myDIV tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>

<h1 class='w3-text-primary w3-border-bottom w3-border-grey'>Ligateams<span class="w3-right w3-hide-small">Saison <?=Form::get_saison_string()?></span></h1>
<p>
  <!-- Legende -->
  <span class="w3-right">
  <i class='material-icons w3-text-blue w3-hover-text-secondary'>home</i> Homepage
  <i class='material-icons w3-text-blue w3-hover-text-secondary'>group</i> Teamfoto
  <i class='material-icons w3-text-blue w3-hover-text-secondary'>mail</i>&nbsp;Email
  </span>
</p>
<br class="w3-hide-large w3-hide-medium">
<p><?=Form::link("ligakarte.php", '<i class="material-icons">place</i> Ligakarte aller Teams')?></p>

<!-- Team suchen -->
<div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
    <i class="material-icons">search</i><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;" type="text" placeholder="Team suchen">
</div>

<!-- Teams Tabelle -->
<div id="myDIV" class="w3-responsive w3-card">
  <table class="w3-table w3-striped ">
    <thead>
    <tr class="w3-primary">
      <th></th>
      <th>Teamname</th>
      <th>Ort</th>
      <th class="w3-hide-small">Verein</th>
      <th>Ligavertreter</th>
    </tr>
    </thead>
    <?php foreach ($alle_teamdaten as $team) {?>
      <tr>
        <!-- Icons -->
        <td style='vertical-align: middle; text-align: right; white-space: nowrap;'>
          <?php if (!empty($team['homepage'])) {?>
            <?=Form::Link($team['homepage'], "<i style='vertical-align: middle;' class='material-icons'>home</i>", true)?>
          <?php } //endif?>
          <?php if (!empty($team['teamfoto'])) {?>
            <?=Form::Link($team['teamfoto'], "<i style='vertical-align: middle;' class='material-icons'>group</i>", true)?>
          <?php } //endif?>
          <?php if (!empty($emails[$team['team_id']])){?>
            <a class="w3-hover-text-secondary w3-text-blue" href='mailto:<?=$emails[$team['team_id']]?>'> 
              <i style='vertical-align: middle;' class='material-icons'>mail</i>
            </a>
          <?php } //endif?>
        </td>
        <!-- Text -->
        <td id='<?=$team['team_id']?>' style='vertical-align: middle;'><?=$team['teamname']?></td>
        <td style='vertical-align: middle;'><?=$team['plz']?> <?=$team['ort']?></td>
        <td style='vertical-align: middle;' class='w3-hide-small'><?=$team['verein']?></td>
        <td style='vertical-align: middle;'><?=$team['ligavertreter']?></td>
      </tr>
    <?php } //Ende foreach?>
  </table>
</div>

<?php include '../../templates/footer.tmp.php';

    



        



           
   