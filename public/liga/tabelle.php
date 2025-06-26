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

// Daten der Meisterschaftstabelle, um sie an das Layout zu uebergeben
if ($saison >= Config::SAISON) {
    $meisterschafts_tabelle = Tabelle::get_meisterschafts_tabelle($gew_spieltag, $saison);
    $meisterschafts_tabelle_templates = Tabelle::get_meisterschafts_tabelle_templates($saison);
} else {
    $meisterschafts_tabelle = ArchivTabelle::get_meisterschafts_tabelle($gew_spieltag, $saison);
    $meisterschafts_tabelle_templates = ArchivTabelle::get_meisterschafts_tabelle_templates($saison);
}

// Daten der Rangtabelle, um sie an das Layout zu uebergeben
$rang_tabelle = Tabelle::get_rang_tabelle($gew_spieltag, $saison);
$rang_tabelle_templates = Tabelle::get_rang_tabelle_templates($saison);

// Daten der Strafen, um sie an das Layout zu uebergeben
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

$block_color = array(
    'A' => 'ehl-text-pink', 
    'B' => 'ehl-text-orange', 
    'C' => 'ehl-text-blue', 
    'D' => 'ehl-text-yellow', 
    'E' => 'ehl-text-green', 
    'F' => 'ehl-text-green'
);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Aktuelle Tabellen der Deutschen Einradhockeyliga";
Html::$content = "Die Rang- und Meisterschaftstabelle welche aus den Turnieren der Deutschen Einradhockeyliga entstehen.";
include '../../templates/header.tmp.php';?>

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
<div class="w3-responsive w3-card w3-hide-small">
    <?php include '../../' . $meisterschafts_tabelle_templates['desktop']; ?>
</div>

<!-- Meisterschaftstabelle für mobile Geräte -->
<div class="w3-responsive w3-card w3-hide-large w3-hide-medium">
    <?php include '../../' . $meisterschafts_tabelle_templates['mobil']; ?>
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

<!-- Legende für die Farbgebung -->
<div>
    <p><b>Weiß hinterlegt</b>: Das Team hat die notwendige Anzahl an Turnieren gespielt und ist zu einer Teilnahme an einer Meisterschaft berechtigt.</p>
    <p><b>Grau hinterlegt</b>: Das Team hat noch nicht die notwendige Anzahl an Turnieren gespielt und ist noch nicht zu einer Teilnahme an einer Meisterschaft berechtigt.</p>
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
<div class="w3-responsive w3-card w3-hide-small">
    <?php include '../../' . $rang_tabelle_templates['desktop']; ?>
</div>

<!-- Rangtabelle für mobile Geräte -->
<div class="w3-responsive w3-card w3-hide-large w3-hide-medium">
    <?php include '../../' . $rang_tabelle_templates['mobil']; ?>
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
    <div class="w3-responsive w3-card w3-hide-small">
        <?php include '../../templates/tabellen/desktop_verwarnungen.tmp.php'; ?>
    </div>
    <div class="w3-hide-large w3-hide-medium">
        <?php include '../../templates/tabellen/mobil_verwarnungen.tmp.php'; ?>
    </div>
<?php endif; ?>

<?php if ($strafe_not_empty) : ?>
    <?php if ($verwarnung_not_empty) : ?>
        <h2 class="w3-text-secondary">Strafen</h2>
    <?php else : ?>
        <h1 id="strafen" class="w3-text-primary">Strafen</h1>
    <?php endif; ?>
    <div class="w3-responsive w3-card w3-hide-small">
        <?php include '../../templates/tabellen/desktop_strafen.tmp.php'; ?>
    </div>
    <div class="w3-hide-large w3-hide-medium">
        <?php include '../../templates/tabellen/mobil_strafen.tmp.php'; ?>
    </div>
<?php endif; ?>

<?php include '../../templates/footer.tmp.php'; ?>