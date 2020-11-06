<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

//Aktuellen Spieltag bekommen. Der aktuelle Spieltag ist der Spieltag, an dem das nächste Turnier eingetragen wird.
$akt_spieltag = Tabelle::get_aktuellen_spieltag();

if (Tabelle::check_spieltag_live($akt_spieltag)){
    $live_spieltag = $akt_spieltag;
}else{
    $live_spieltag = -1;
    $akt_spieltag -= 1;
}

if (isset($_GET['spieltag']) && is_numeric($_GET['spieltag'])){
    $gew_spieltag=$_GET['spieltag'];
}else{
    $gew_spieltag = $akt_spieltag;
}

//Tabellen und Strafen, um sie an das Layout zu übergeben
$meisterschafts_tabelle = Tabelle::get_meisterschafts_tabelle($gew_spieltag);
$rang_tabelle = Tabelle::get_rang_tabelle($gew_spieltag);
$strafen = Team::get_all_strafen();

//Testen ob Verwarnungen oder Strafen existieren.
$verwarnung_not_empty = $strafe_not_empty = false;
foreach ($strafen as $strafe){
    if ($strafe['verwarnung'] == 'Ja'){
        $verwarnung_not_empty = true;
    }elseif ($strafe['verwarnung'] == 'Nein'){
        $strafe_not_empty = true;
    }
}

//Den Plätzen der Meisterschaftstabelle eine Farbe zuordnen:
for ($i = 1; $i < 5; $i++){
    $platz_color[$i] = "w3-text-tertiary";
}
for ($i = 5; $i < 11; $i++){
    $platz_color[$i] = "w3-text-grey";
}
for ($i = 11; $i < 17; $i++){
    $platz_color[$i] = "w3-text-brown";
}
for ($i = 17; $i < 23; $i++){
    $platz_color[$i] = "w3-text-primary";
}
for ($i = 23; $i < 29; $i++){
    $platz_color[$i] = "w3-text-green";
}

//Spieltag wählen:
for ($spieltag = $akt_spieltag; $spieltag >= 0; $spieltag--){
    if($spieltag == $gew_spieltag){
        $spieltag_color = 'w3-text-white';
        $spieltag_button = 'w3-primary w3-border w3-border-primary';
    }else{
        $spieltag_color = 'w3-text-grey';
        $spieltag_button = 'w3-light-grey w3-border';
    }
    $spieltag_string = "<span class='$spieltag_color'>$spieltag</span>";
    if($spieltag == $live_spieltag){
        $spieltag_string .= "<span class='$spieltag_color'> <i>(unvollständig)</i></span>";
    }

    $spieltage_array[$akt_spieltag-$spieltag] = array(
        $spieltag,
        "spieltag_string" =>$spieltag_string,
        "spieltag_button" =>$spieltag_button
    );
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Aktuelle Tabellen der Deutschen Einradhockeyliga";
$content = "Die Rang- und Meisterschaftstabelle welche aus den Turnieren der Deutschen Einradhockeyliga entstehen.";
include '../../templates/header.tmp.php';
?>

<!-- Erklärungen zur Tabelle -->

<!-- Trigger/Open the Modal -->
<button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-text-blue"><i class="material-icons">info</i> Infos zu den Tabellen</button>
<!-- The Modal -->
<div id="id01" class="w3-modal">
  <div class="w3-modal-content w3-card-4" style="max-width:660px">
    <div class="w3-panel">
        <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright">&times;</span>
        <h3 class="w3-text-primary w3-border-bottom w3-border-grey">Spieltage</h3>
        <div class="w3-margin-left">
            <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>Alle Turniere welche an einem Wochenende stattfinden, werden einem Spieltag zugeordnet.</li>
            <li>Der in der Liste "Spieltag wählen" rot angezeigte Spieltag, ist der aktuelle Spieltag mit Live-Turnierergebnissen, falls sie schon vorhanden sind.</li>
            <li>Für die Turnieranmeldungen ist jedoch immer der vorherige (und damit vollständige) Spieltag relevant, also nicht der aktuelle Spieltag.</li>
            </ul>
        </div>
        <h3 class="w3-text-primary w3-border-bottom w3-border-grey">Die Rangtabelle</h3>
        <div class="w3-margin-left">
            <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>Für jedes Team, welches du auf einem Turnier besiegst, bekommt dein Team Punkte. Starke Teams geben die meisten Punkte.</li>
            <li>Wie viele Punkte du für welches Team bekommst und auf welchen Turnieren du spielen kannst, wird in der Rangtabelle festgelegt.</li>
            <li>Die Rangtabelle ist nach dem durchschnittlichen Turnierergebnis deiner bis zu fünf letzten Turniere sortiert.</li>
            </ul>
        </div>

        <h3 class="w3-text-primary w3-border-bottom w3-border-grey">Die Meisterschaftstabelle</h3>
        <div class="w3-panel w3-container">
            <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>In der Meisterschaftstabelle werden die besten fünf Turnierergebnisse deines Teams in der aktuellen Saison aufaddiert.</li>
            <li>Der Platz in der Meisterschaftstabelle bestimmt, für welche Meisterschaft sich dein Team qualifizieren kann.</li>
            <li><b>Meisterschaften</b> <i>(Plätze)</i><br>Deutsche Meisterschaft (<span class="w3-text-tertiary">1-4</span>)<br>Quali zur Deutschen (<span class="w3-text-grey">5-10</span>)<br>B-Meisterschaft (<span class="w3-text-brown">11-16</span>)<br>C-Meisterschaft (<span class="w3-text-blue">17-22</span>)<br>D-Meisterschaft (<span class="w3-text-green">23-28</span>)</li>
            </ul>
        </div>
    </div>
  </div>
</div>
<script>
// Get the modal
var modal = document.getElementById('id01');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>

<!-- Meisterschaftstabelle -->
<h1 class="w3-text-primary w3-border-primary" id='meister'>Meisterschaftstabelle</h1>
<p class="w3-border-top w3-border-grey w3-text-grey"><a href="#rang" class="no w3-hover-text-secondary">Zur Rangtabelle</a><span class="w3-right">Saison <?=Form::get_saison_string()?></span></p>

<!-- Spieltag wählen -->
<p class="w3-text-grey">Spieltag wählen:<p>

<div class="w3-bar">
    <?php foreach ($spieltage_array as $spieltag_dict){?>
        <?php echo "<a class='no w3-hover-text-secondary' href='tabelle.php?spieltag={$spieltag_dict[0]}#meister'><button class= 'w3-bar-item w3-button {$spieltag_dict['spieltag_button']} w3-hover-primary' type='button'>"?><?=$spieltag_dict["spieltag_string"]?></button></a>
    <?php } //endforeach?>
</div>

<!--Tabelle-->
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped" style="">
        <thead class="w3-primary">
            <tr>
                <th><b>Platz</b></th>
                <th><b>Team</b></th>
                <th><b>Turnierergebnisse</b></th>
                <th><b>&sum;</b></th>
            </tr>
        </thead>
        <?php foreach ($meisterschafts_tabelle as $spalte){?>
            <tr>
                <td class="<?=$platz_color[$spalte['platz']]?>"><?=$spalte['platz'] ?? ''?></td>
                <td style="white-space: nowrap"><?=$spalte['teamname']?></td>
                <td><?=htmlspecialchars_decode($spalte['string'])?></td>
                <td><?=$spalte['summe'] ?: 0?><a class="no w3-text-blue w3-hover-text-secondary" href="#pranger"><?=$spalte['strafe_stern'] ?? ''?></a></td>
            </tr>
        <?php } //end foreach?>
    </table>    
</div>

<!--Rangtabelle-->
<h1 id="rang" class="w3-text-primary w3-border-primary">Rangtabelle</h1>
<p class="w3-border-top w3-border-grey w3-text-grey"><a href="#meister" class="no w3-hover-text-secondary">Zur Meisterschaftstabelle</a><span class="w3-right">Saison <?=Form::get_saison_string()?></span></p>

<!-- Spieltag wählen -->
<p class="w3-text-grey">Spieltag wählen:<p>


<div class="w3-bar">
    <?php foreach ($spieltage_array as $spieltag_dict){?>
        <?php echo "<a class='no w3-hover-text-secondary' href='tabelle.php?spieltag={$spieltag_dict[0]}#rang'><button class= 'w3-bar-item w3-button {$spieltag_dict['spieltag_button']} w3-hover-primary' type='button'>"?><?=$spieltag_dict["spieltag_string"]?></button></a>
    <?php } //endforeach?>
</div>


<!--Tabelle -->
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <thead class="w3-primary">
            <tr>
                <th><b>#</b></th>
                <th><b>Block</b></th>
                <th><b>Wertung</b></th>
                <th><b>Team</b></th>
                <th><b>Turnierergebnisse</b></th>
                <th><b>&empty;</b></th>
            </tr>
        </thead>
        <?php foreach ($rang_tabelle as $spalte){?>
            <tr>
                <td><span class="w3-text-grey"><?=$spalte['platz']?></span></td>
                <td class="w3-center"><?=Tabelle::platz_to_block($spalte['platz'])?></td>
                <td class="w3-center"><?=Tabelle::platz_to_wertigkeit($spalte['platz'])?></td>
                <td style="white-space: nowrap"><?=$spalte['teamname']?></td>
                <td><?=htmlspecialchars_decode($spalte['string'])?></td>
                <td><?=$spalte['avg'] ?: 0?></td>
            </tr>
        <?php } //end foreach?>
    </table>    
</div>


<!-- Pranger -->
<h3 id="pranger" class="w3-text-primary">Verwarnungen</h3>
<?php if ($verwarnung_not_empty) {?>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
                <tr class="w3-primary">
                    <th>Team</th>
                    <th>Grund</th>
                    <th>Turnier</th>
                </tr>
                <?php foreach ($strafen as $strafe){ if ($strafe['verwarnung'] == 'Ja'){?>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: middle;"><?=$strafe['teamname']?></td>
                        <td style="vertical-align: middle"><?=$strafe['grund']?></td>
                        <td style="vertical-align: middle"><?=($strafe['datum'] ?? '') . ' - ' . ($strafe['ort'] ?? '')?></td>
                    </tr>
                <?php }/*end if*/ }/*end foreach*/?>
            
        </table>
    </div>
<?php }else{?>
    <p><i>Sehr gut! Es wurden noch keine Verwarnungen vergeben.</i></p>
<?php } //endif?>  
<h3 class="w3-text-primary">Strafen</h3>
<?php if ($strafe_not_empty) {?>  
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
                <tr class="w3-primary">
                    <th>Team</th>
                    <th>Grund</th>
                    <th>Turnier</th>
                </tr>
                <?php foreach ($strafen as $strafe){ if ($strafe['verwarnung'] == 'Nein'){?>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: middle;"><?=$strafe['teamname']?></td>
                        <td style="vertical-align: middle">
                            <?=$strafe['grund']?> 
                            <?php if (!empty($strafe['prozentsatz'])){?>(<?=$strafe['prozentsatz']?> %)<?php } //endif?>
                        </td>
                        <td style="vertical-align: middle"><?=($strafe['datum'] ?? '') . ' - ' . ($strafe['ort'] ?? '')?></td>
                    </tr>
                <?php }/*end if*/ }/*end foreach*/?>
        </table>
    </div>
<?php }else{?>
    <p><i>Sehr gut! Es wurden noch keine Strafen vergeben.</i></p>
<?php } //endif
include '../../templates/footer.tmp.php';