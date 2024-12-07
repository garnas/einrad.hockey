<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$saison = (int) ($_GET['saison'] ?? Config::SAISON);

//Aktuellen Spieltag bekommen. Der aktuelle Spieltag ist der Spieltag, an dem das nächste Turnier eingetragen wird.
$akt_spieltag = Tabelle::get_aktuellen_spieltag($saison);

if (Tabelle::check_spieltag_live($akt_spieltag)){
    $live_spieltag = $akt_spieltag;
}else{
    $live_spieltag = -1;
    $akt_spieltag--;
}

if (isset($_GET['spieltag'])){
    $gew_spieltag = (int)$_GET['spieltag'];
}else{
    $gew_spieltag = $akt_spieltag;
}



//Tabellen und Strafen, um sie an das Layout zu übergeben
$meisterschafts_tabelle = Tabelle::get_meisterschafts_tabelle($gew_spieltag,$saison);
$rang_tabelle = Tabelle::get_rang_tabelle($gew_spieltag,$saison);
$strafen = Team::get_strafen($saison);

//Testen ob Verwarnungen oder Strafen existieren.
$verwarnung_not_empty = $strafe_not_empty = false;
foreach ($strafen as $key => $strafe){
    if ($strafe['verwarnung'] == 'Ja'){
        $verwarnung_not_empty = true;
    }elseif ($strafe['verwarnung'] == 'Nein'){
        $strafe_not_empty = true;
    }
    if (!empty($strafe['datum'])){
        $strafen[$key]['datum'] = date("d.m.Y", strtotime($strafe['datum']));
    }else{
        $strafen[$key]['datum'] = "-";
    }
}

// Den Plätzen der Meisterschaftstabelle eine Farbe zuordnen:
for ($i = 1; $i <= 10 ; $i++){
    $platz_color[$i] = "w3-text-tertiary";
}
for ($i = 11; $i <= 16; $i++){
    $platz_color[$i] = "w3-text-grey";
}
for ($i = 17; $i <= 22; $i++){
    $platz_color[$i] = "w3-text-brown";
}

// Spieltag wählen:
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
Html::$titel = "Aktuelle Tabellen der Deutschen Einradhockeyliga";
Html::$content = "Die Rang- und Meisterschaftstabelle welche aus den Turnieren der Deutschen Einradhockeyliga entstehen.";
include '../../templates/header.tmp.php';?>

<!-- Erklärungen zur Tabelle -->

<!-- Trigger/Open the Modal -->
<button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-text-primary">
    <?= Html::icon("info") ?> Infos zu den Tabellen
</button>
<!-- The Modal -->
<div id="id01" class="w3-modal">
  <div class="w3-modal-content w3-card-4" style="max-width:660px">
    <div class="w3-panel">
        <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright">&times;</span>
        <h3 class="w3-text-primary w3-border-bottom w3-border-grey">Spieltage</h3>
        <div class="w3-margin-left">
            <ul class="w3-ul w3-leftbar w3-border-tertiary">
                <li>Alle Turniere welche an einem Wochenende stattfinden, werden einem Spieltag zugeordnet.</li>
                <li>Oberhalb der jeweiligen Tabelle kann man auch vergangene Spieltage auswählen.</li>
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
                <li><b>Finalturniere</b> <i>(Plätze)</i><br>Finale der Deutschen Einradhockeyliga (<span class="w3-text-tertiary">1-10</span>)
                    <br>B-Finale der Deutschen Einradhockeyliga (<span class="w3-text-brown">11-16</span>)
                    <br>C-Finale der Deutschen Einradhockeyliga (<span class="w3-text-primary">17-22</span>)
                </li>
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
<p class="w3-border-top w3-border-grey w3-text-grey"><a href="#rang" class="no w3-hover-text-secondary">Zur Rangtabelle</a><span class="w3-right">Saison <?=Html::get_saison_string($saison)?></span></p>

<!-- Spieltag wählen -->
<div class="w3-bar">
    <?php foreach ($spieltage_array as $spieltag_dict){?>
        <a class='no w3-hover-text-secondary' href='tabelle.php?saison=<?=$saison?>&spieltag=<?=$spieltag_dict[0]?>#meister'><span class= 'w3-bar-item w3-button <?=$spieltag_dict['spieltag_button']?> w3-hover-primary'><?=$spieltag_dict["spieltag_string"]?></span></a>
    <?php } //endforeach?>
</div>

<!--Tabelle-->
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <thead class="w3-primary">
            <tr>
                <th class="w3-right-align"><b>Platz</b></th>
                <th class="w3-left-align"><b>Team</b></th>
                <th class="w3-left-align"><b>Turnierergebnisse</b></th>
                <th class="w3-right-align"><b>&sum;</b></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($meisterschafts_tabelle as $spalte){?>
                <tr>
                    <td class="w3-right-align <?=$platz_color[$spalte['platz']] ?? ''?>"><?=$spalte['platz'] ?? ''?></td>
                    <td class="w3-left-align"><?=$spalte['teamname']?></td>
                    <td class="w3-left-align"><?=$spalte['string']?></td>
                    <td class="w3-right-align"><?=number_format($spalte['summe'] ?: 0, 0, ",", ".")?><a class="no w3-text-primary w3-hover-text-secondary" href="#pranger"><?=$spalte['strafe_stern'] ?? ''?></a></td>
                </tr>
            <?php } //end foreach?>
        </tbody>
    </table>    
</div>

<!--Rangtabelle-->
<h1 id="rang" class="w3-text-primary w3-border-primary">Rangtabelle</h1>
<p class="w3-border-top w3-border-grey w3-text-grey"><a href="#meister" class="no w3-hover-text-secondary">Zur Meisterschaftstabelle</a><span class="w3-right">Saison <?=Html::get_saison_string($saison)?></span></p>

<!-- Spieltag wählen -->
<div class="w3-bar">
    <?php foreach ($spieltage_array as $spieltag_dict){?>
        <a class='no w3-hover-text-secondary' href='tabelle.php?saison=<?=$saison?>&spieltag=<?=$spieltag_dict[0]?>#rang'><span class='w3-bar-item w3-button <?=$spieltag_dict['spieltag_button']?> w3-hover-primary'><?=$spieltag_dict["spieltag_string"]?></span></a>
    <?php } //endforeach?>
</div>


<!--Tabelle -->
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <thead>
            <tr class="w3-primary">
                <th class="w3-right-align"><b>#</b></th>
                <th class="w3-left-align"><b>Block</b></th>
                <th class="w3-right-align"><b>Wertung</b></th>
                <th class="w3-left-align"><b>Team</b></th>
                <th class="w3-left-align"><b>Turnierergebnisse</b></th>
                <th class="w3-right-align"><b>&empty;</b></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rang_tabelle as $spalte){?>
                <tr>
                    <td class="w3-right-align w3-text-grey"><?=$spalte['rang']?></td>
                    <td class="w3-left-align"><?=Tabelle::rang_to_block($spalte['rang'])?></td>
                    <td class="w3-right-align"><?=Tabelle::rang_to_wertigkeit($spalte['rang'])?></td>
                    <td class="w3-left-align"><?=$spalte['teamname']?></td>
                    <td class="w3-left-align"><?=$spalte['string']?></td>
                    <td class="w3-right-align"><?=number_format($spalte['avg'] ?: 0, 1, ",", ".")?></td>
                </tr>
            <?php } //end foreach?>
        </tbody>
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
                    <th class="w3-center">Turnier</th>
                </tr>
                <?php foreach ($strafen as $strafe){ if ($strafe['verwarnung'] == 'Ja'){?>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: middle;"><?=$strafe['teamname']?></td>
                        <td style="vertical-align: middle"><?=$strafe['grund']?></td>
                        <td class="w3-center" style="vertical-align: middle"><?=$strafe['datum']?><br><?=($strafe['ort'] ?? '')?></td>
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
                    <th class="w3-center">Turnier</th>
                </tr>
                <?php foreach ($strafen as $strafe){ if ($strafe['verwarnung'] == 'Nein'){?>
                    <tr>
                        <td style="white-space: nowrap; vertical-align: middle;"><?=$strafe['teamname']?></td>
                        <td style="vertical-align: middle">
                            <?=$strafe['grund']?>
                            <?php if ($strafe['prozentsatz'] != 0){?>(<?=$strafe['prozentsatz']?> %)<?php } //endif?>
                        </td>
                        <td class="w3-center" style="vertical-align: middle"><?=$strafe['datum']?><br><?=($strafe['ort'] ?? '')?></td>
                    </tr>
                <?php }/*end if*/ }/*end foreach*/?>
        </table>
    </div>
<?php }else{?>
    <p><i>Sehr gut! Es wurden noch keine Strafen vergeben.</i></p>
<?php } //endif
include '../../templates/footer.tmp.php';