<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

// Waehle uebergebene Saison, sonst aktuelle Saison
$saison = (int) ($_GET['saison'] ?? Config::SAISON);

// Erhalte aktuellen Spieltag. Der aktuelle Spieltag ist der Spieltag, an dem das nächste Turnier eingetragen wird.
$akt_spieltag = Tabelle::get_aktuellen_spieltag($saison);

// Anpassungen, sollte der Spieltag gerade gespielt werden
if (Tabelle::check_spieltag_live($akt_spieltag)) {
    $live_spieltag = $akt_spieltag;
} else {
    $live_spieltag = -1;
    $akt_spieltag--;
}

// Waehle uebergebenen Spieltag, sonst aktuellen Spieltag
$gew_spieltag = isset($_GET['spieltag']) ? (int) $_GET['spieltag'] : $akt_spieltag;

// Tabellen und Strafen, um sie an das Layout zu übergeben
$meisterschafts_tabelle = Tabelle::get_meisterschafts_tabelle($gew_spieltag, $saison);
$rang_tabelle = Tabelle::get_rang_tabelle($gew_spieltag, $saison);
$strafen = Team::get_strafen($saison);

// Testen ob Verwarnungen oder Strafen existieren.
$verwarnung_not_empty = $strafe_not_empty = false;
foreach ($strafen as $key => $strafe) {
    $verwarnung_not_empty = $verwarnung_not_empty || $strafe['verwarnung'] == 'Ja';
    $strafe_not_empty = $strafe_not_empty || $strafe['verwarnung'] == 'Nein';
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
    let modal = document.getElementById('id01');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<!-- ------------------------- MEISTERSCHAFTSTABELLE ------------------------- -->
<h1 id='meister' class="w3-text-primary">Meisterschaftstabelle</h1>
<p class="w3-border-top w3-border-grey w3-text-grey"><a href="#rang" class="no w3-hover-text-secondary">Zur Rangtabelle</a><span class="w3-right">Saison <?=Html::get_saison_string($saison)?></span></p>

<!-- Auswahl des Spieltages ueber der Meisterschaftstabelle -->
<div class="w3-row w3-xlarge w3-text-primary w3-padding">
    <div class="w3-col w3-right w3-right-align" style="width: 36px;">
        <a href="tabelle.php?saison=<?=$saison?>#meister" class="w3-hover-text-secondary"><?=Html::icon('last_page')?></a>
    </div>
    <div class="w3-col w3-right w3-right-align" style="width: 36px;">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=min($gew_spieltag+1, $akt_spieltag)?>#meister" class="w3-hover-text-secondary"><?=Html::icon('keyboard_arrow_right')?></a>
    </div>
    <div class="w3-col w3-right w3-right-align" style="width: 36px;">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=max($gew_spieltag-1, 0)?>#meister" class="w3-hover-text-secondary"><?=Html::icon('keyboard_arrow_left')?></a>
    </div>
    <div class="w3-col w3-right w3-right-align" style="width: 36px;">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=0#meister" class="w3-hover-text-secondary"><?=Html::icon('first_page')?></a>
    </div>
    <div class="w3-rest">Spieltag <?=$gew_spieltag?></div>
</div>

<!-- Beginn der eigentlichen Meisterschaftstabelle -->
<div class="w3-responsive w3-card">
    <!-- Header der Meisterschaftstabelle -->
    <div class="w3-row w3-primary">
        <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width: 50px;"><b>#</b></div>
        <div class="w3-col w3-right w3-padding-8" style="width: 42px;"></div>
        <div class="w3-rest">
            <div class="w3-row">
                <div class="w3-col l10 m10 s10 w3-padding-8 w3-left-align"><b>Team</b></div>
                <div class="w3-col l2 m2 s2 w3-padding-8 w3-right-align"><b>Summe</b></div>
            </div>
        </div>
    </div>
    <!-- Zeilen der Meisterschaftstabelle -->
    <div>
        <?php $counter = 0; ?>
        <?php foreach ($meisterschafts_tabelle as $key => $zeile): ?>
            <?php $row_class = $counter % 2 == 1 ? "w3-light-grey" : ""; ?>
            <!-- Kopfzeile fuer das Team -->
            <div id="m-head-<?=$key?>" class="w3-row <?=$row_class?>">
                <div class="w3-col w3-left w3-padding-8 w3-right-align <?=$platz_color[$zeile['platz']] ?? ''?>" style="width: 50px;"><?=$zeile['platz']?></div>
                <div class="w3-col w3-right w3-padding-8" style="width: 42px;">
                    <?php if (!empty($zeile['details'])): ?>
                        <span onclick="show_results('m', <?=$key?>)" style="cursor:pointer">
                            <span id="m-icon-on-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:block">arrow_drop_down</span>
                            <span id="m-icon-off-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:none">arrow_drop_up</span>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 80px;">
                    <?=number_format($zeile['summe'] ?: 0, 0, ",", ".") . (!empty($zeile['hat_strafe']) ? '<a class="no w3-text-primary w3-hover-text-secondary" href="#strafen">*</a>' : '')?>
                </div>
                <div class="w3-rest w3-padding-8 w3-left-align"><?=$zeile['teamname']?></div>
            </div>
            <!-- Details zu den Turnieren des Teams -->
            <?php if (!empty($zeile['details'])): ?>
                <?php foreach ($zeile['details'] as $ergebnis): ?>
                    <div class="m-result-<?=$key?> w3-row <?=$row_class?>" style="display: none;">
                        <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width:100px;"><?=date_format(date_create($ergebnis['datum']), "d.m.y")?></div>
                        <div class="w3-col w3-hide-small w3-left w3-padding-8 w3-left-align" style="width:85px;"><?=$ergebnis['tblock']?></div>
                        <div class="w3-col w3-hide-small w3-right w3-padding-8" style="width: 42px;"></div>
                        <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 80px;"><a href="ergebnisse.php#<?=$ergebnis['turnier_id']?>" class="no w3-text-primary w3-hover-text-secondary"><?=number_format($ergebnis['ergebnis'] ?: 0, 0, ",", ".")?></a></div>
                        <div class="w3-col w3-hide-small w3-right w3-padding-8 w3-right-align" style="width: 65px;"><?=$ergebnis['platz']?> / <?=$ergebnis['teilnehmer']?></div>
                        <div class="w3-rest w3-padding-8 w3-left-align"><?=$ergebnis['ort']?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php $counter++; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- Auswahl des Spieltages unter der Meisterschaftstabelle-->    
<div class="w3-row w3-text-primary w3-padding w3-small">
    <div class="w3-col l3 m1 s3 w3-left-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=0#meister" class="no w3-hover-text-secondary"><?=Html::icon('first_page')?> <span class="w3-hide-small w3-hide-medium">Erster Spieltag</span></a>
    </div>
    <div class="w3-col l3 m5 s3 w3-left-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=max($gew_spieltag-1, 0)?>#meister" class="no w3-hover-text-secondary"><?=Html::icon('keyboard_arrow_left')?> <span class="w3-hide-small">Vorheriger Spieltag </span></a>
    </div>
    <div class="w3-col l3 m5 s3 w3-right-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=min($gew_spieltag+1, $akt_spieltag)?>#meister" class="no w3-hover-text-secondary"><span class="w3-hide-small"> Nächster Spieltag</span> <?=Html::icon('keyboard_arrow_right')?></a>
    </div>
    <div class="w3-col l3 m1 s3 w3-right-align">
        <a href="tabelle.php?saison=<?=$saison?>#meister" class="no w3-hover-text-secondary"><span class="w3-hide-small w3-hide-medium">Aktueller Spieltag</span> <?=Html::icon('last_page')?></a>
    </div>
</div>


<!-- ------------------------- RANGTABELLE ------------------------- -->
<h1 id="rang" class="w3-text-primary w3-border-primary">Rangtabelle</h1>
<p class="w3-border-top w3-border-grey w3-text-grey"><a href="#meister" class="no w3-hover-text-secondary">Zur Meisterschaftstabelle</a><span class="w3-right">Saison <?=Html::get_saison_string($saison)?></span></p>

<!-- Auswahl des Spieltages ueber der Rangtabelle -->  
<div class="w3-row w3-xlarge w3-text-primary w3-padding">
    <div class="w3-col w3-right w3-right-align" style="width: 36px;">
        <a href="tabelle.php?saison=<?=$saison?>#rang" class="no w3-hover-text-secondary"><?=Html::icon('last_page')?></a>
    </div>  
    <div class="w3-col w3-right w3-right-align" style="width: 36px;">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=min($gew_spieltag+1, $akt_spieltag)?>#rang" class="no w3-hover-text-secondary"><?=Html::icon('keyboard_arrow_right')?></a>
    </div>
    <div class="w3-col w3-right w3-right-align" style="width: 36px;">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=max($gew_spieltag-1, 0)?>#rang" class="no w3-hover-text-secondary"><?=Html::icon('keyboard_arrow_left')?></a>
    </div>
    <div class="w3-col w3-right w3-right-align" style="width: 36px;">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=0#rang" class="no w3-hover-text-secondary"><?=Html::icon('first_page')?></a>
    </div>
    <div class="w3-rest">Spieltag <?=$gew_spieltag?></div>
</div>

<!-- Beginn der eigentlichen Rangtabelle -->
<div class="w3-responsive w3-card">
    <!-- Header der Rangtabelle -->
    <div class="w3-row w3-primary"> 
        <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width:50px;"><b>#</b></div>
        <div class="w3-col w3-left w3-padding-8 w3-left-align" style="width:50px;"><b>Bl.</b></div>
        <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width:60px;"><b>Wert</b></div>
        <div class="w3-col w3-right w3-padding-8" style="width: 42px;"></div>
        <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width:100px;"><b>Mittelwert</b></div>
        <div class="w3-rest w3-padding-8 w3-left-align"><b>Team</b></div>
    </div>
    <!-- Zeilen der Rangtabelle -->
    <div>
        <?php $counter = 0; ?>
        <?php foreach ($rang_tabelle as $key => $zeile): ?>
            <?php $row_class = $counter % 2 == 1 ? "w3-light-grey" : ""; ?>
            <!-- Kopfzeile fuer das Team -->
            <div id="r-head-<?=$key?>" class="w3-row <?=$row_class?>" >
                <div class="w3-col w3-left w3-padding-8 w3-right-align w3-text-gray" style="width: 50px;"><?=$zeile['rang']?></div>
                <div class="w3-col w3-left w3-padding-8 w3-left-align" style="width: 50px;"><?=Tabelle::rang_to_block($zeile['rang'])?></div>
                <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width: 60px;"><?=Tabelle::rang_to_wertigkeit($zeile['rang'])?></div>
                <div class="w3-col w3-right w3-padding-8 w3-center" style="width: 42px;">
                    <?php if (!empty($zeile['details'])): ?>
                        <span onclick="show_results('r', <?=$key?>)" style="cursor:pointer">
                            <span id="r-icon-on-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:block">arrow_drop_down</span>
                            <span id="r-icon-off-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:none">arrow_drop_up</span>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width:100px"><?=number_format($zeile['avg'] ?: 0, 1, ",", ".")?></div>
                <div class="w3-rest w3-padding-8 w3-left-align"><?=$zeile['teamname']?></div>
            </div>
            <!-- Details zu den Turnieren des Teams -->
            <?php if (!empty($zeile['details'])): ?>
                <?php foreach ($zeile['details'] as $dey => $ergebnis): ?>
                    <div class="r-result-<?=$key?> w3-row <?=$row_class?>" style="display: none;">
                        <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width: 100px;"><?=date_format(date_create($ergebnis['datum']), "d.m.y")?></div>
                        <div class="w3-col w3-hide-small w3-left w3-padding-8 w3-left-align" style="width: 85px;"><?=$ergebnis['tblock']?></div>
                        <div class="w3-col w3-hide-small w3-right w3-padding-8" style="width: 42px;"></div>
                        <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 100px;">
                            <a href="ergebnisse.php?saison=<?=$ergebnis['saison']?>#<?=$ergebnis['turnier_id']?>" class="no <?=$saison != $ergebnis['saison'] ? 'w3-text-green' : 'w3-text-primary'?> w3-hover-text-secondary"> 
                                <?=number_format($ergebnis['ergebnis'] ?: 0, 1, ",", ".")?>
                            </a>
                        </div>
                        <div class="w3-col w3-hide-small w3-right w3-padding-8 w3-right-align" style="width: 65px;"><?=$ergebnis['platz']?> / <?=$ergebnis['teilnehmer']?></div>
                        <div class="w3-rest w3-padding-8 w3-left-align"><?=$ergebnis['ort']?></div>
                    </div>
                <?php endforeach; ?>    
            <?php endif; ?>
            <?php $counter++; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- Auswahl des Spieltages unter der Rangtabelle -->
<div class="w3-row w3-text-primary w3-padding w3-small">
    <div class="w3-col l3 m1 s3 w3-left-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=0#rang" class="no w3-hover-text-secondary"><?=Html::icon('first_page')?> <span class="w3-hide-small w3-hide-medium">Erster Spieltag</span></a>
    </div>
    <div class="w3-col l3 m5 s3 w3-left-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=max($gew_spieltag-1, 0)?>#rang" class="no w3-hover-text-secondary"><?=Html::icon('keyboard_arrow_left')?> <span class="w3-hide-small">Vorheriger Spieltag</span></a>
    </div>
    <div class="w3-col l3 m5 s3 w3-right-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=min($gew_spieltag+1, $akt_spieltag)?>#rang" class="no w3-hover-text-secondary"><span class="w3-hide-small">Nächster Spieltag</span> <?=Html::icon('keyboard_arrow_right')?></a>
    </div>
    <div class="w3-col l3 m1 s3 w3-right-align">
        <a href="tabelle.php?saison=<?=$saison?>#rang" class="no w3-hover-text-secondary"><span class="w3-hide-small w3-hide-medium">Aktueller Spieltag</span> <?=Html::icon('last_page')?></a>
    </div>
</div>


<!-- ---------------------------- -->
<!-- - Verwarnungen und Strafen - -->
<!-- ---------------------------- -->

<?php if ($verwarnung_not_empty && $strafe_not_empty): ?>
    <h1 id="strafen" class="w3-text-primary">Verwarnungen & Strafen</h1>
<?php endif; ?>

<?php if ($verwarnung_not_empty): ?>
    <?php if ($strafe_not_empty): ?>
        <h2 class="w3-text-secondary">Verwarnungen</h2>
    <?php else: ?>
        <h1 id="strafen" class="w3-text-primary">Verwarnungen</h1>
    <?php endif; ?>
    <div class="w3-responsive w3-card">
        <!-- Header der Verwarnungen -->
        <div class="w3-row w3-primary"> 
            <div class="w3-col w3-padding-8 l3 m3 s3 w3-left-align"><b>Team</b></div>
            <div class="w3-col w3-padding-8 l7 m7 s7 w3-left-align"><b>Grund</b></div>
            <div class="w3-col w3-padding-8 l2 m2 s2 w3-left-align"><b>Datum (Ort)</b></div>
        </div>
        <!-- Zeilen der Verwarnungen -->
        <?php $counter = 0; ?>
        <?php foreach ($strafen as $strafe) : ?>
            <?php if ($strafe['verwarnung'] == 'Ja') : ?>
                <?php $row_class = $counter % 2 == 1 ? "w3-light-grey" : ""; ?>
                <div class="w3-row <?=$row_class?>">
                    <div class="w3-col w3-padding-8 l3 m3 s3 w3-left-align"><?=$strafe['teamname']?></div>
                    <div class="w3-col w3-padding-8 l7 m7 s7 w3-left-align"><?=$strafe['grund']?></div>
                    <?php if (!empty($strafe['datum'])) : ?>
                        <div class="w3-col w3-padding-8 l2 m2 s2 w3-left-align"><?=date("d.m.Y", strtotime($strafe['datum']))?> (<?=($strafe['ort'])?>)</div>
                    <?php else : ?>
                        <div class="w3-col w3-padding-8 l2 m2 s2 w3-left-align">-</div>
                    <?php endif; ?>
                </div>
                <?php $counter++; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($strafe_not_empty) : ?>
    <?php if ($verwarnung_not_empty) : ?>
        <h2 class="w3-text-secondary">Strafen</h2>
    <?php else : ?>
        <h1 id="strafen" class="w3-text-primary">Strafen</h1>
    <?php endif; ?>
    <div class="w3-responsive w3-card">
        <!-- Header der Strafen -->
        <div class="w3-row w3-primary"> 
            <div class="w3-col w3-padding-8 l3 m3 s3 w3-left-align"><b>Team</b></div>
            <div class="w3-col w3-padding-8 l6 m6 s6 w3-left-align"><b>Grund</b></div>
            <div class="w3-col w3-padding-8 l1 m1 s1 w3-right-align"><b>Strafe</b></div>
            <div class="w3-col w3-padding-8 l2 m2 s2 w3-left-align"><b>Datum (Ort)</b></div>
        </div>
        <!-- Zeilen der Strafen -->
        <?php $counter = 0; ?>
        <?php foreach ($strafen as $strafe) : ?>
            <?php if ($strafe['verwarnung'] == 'Nein') : ?>
                <?php $row_class = $counter % 2 == 1 ? "w3-light-grey" : ""; ?>
                <div class="w3-row <?=$row_class?>">
                    <div class="w3-col w3-padding-8 l3 m3 s3 w3-left-align"><?=$strafe['teamname']?></div>
                    <div class="w3-col w3-padding-8 l6 m6 s6 w3-left-align"><?=$strafe['grund']?></div>
                    <div class="w3-col w3-padding-8 l1 m1 s1 w3-right-align"><?=$strafe['prozentsatz']?>%</div>
                    <?php if (!empty($strafe['datum'])) : ?>
                        <div class="w3-col w3-padding-8 l2 m2 s2 w3-left-align"><?=date("d.m.Y", strtotime($strafe['datum']))?> (<?=($strafe['ort'])?>)</div>
                    <?php else : ?>
                        <div class="w3-col w3-padding-8 l2 m2 s2 w3-left-align">-</div>
                    <?php endif; ?>
                </div>
                <?php $counter++; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../../templates/footer.tmp.php'; ?>