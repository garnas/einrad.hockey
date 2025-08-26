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
if ($saison >= 30) {
    $meisterschafts_tabelle = Tabelle::get_meisterschafts_tabelle($gew_spieltag, $saison);
    $meisterschafts_tabelle_templates = Tabelle::get_meisterschafts_tabelle_templates($saison);
    $show_filter = true;
    $filter = (isset($_GET['filter'])) ? $_GET['filter'] : 'tabelle';
} else {
    $meisterschafts_tabelle = Archiv_Tabelle::get_meisterschafts_tabelle($gew_spieltag, $saison);
    $meisterschafts_tabelle_templates = Archiv_Tabelle::get_meisterschafts_tabelle_templates($saison);
    $show_filter = false;
    $filter = 'tabelle';
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
foreach (range(1, 10) as $i) {
    $platz_color[$i] = "ehl-text-pink";
}
foreach (range(11, 16) as $i) {
    $platz_color[$i] = "ehl-text-orange";
}
foreach (range(17, 22) as $i) {
    $platz_color[$i] = "ehl-text-blue";
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

<h2 class="w3-text-secondary w3-xlarge">Spieltag <?=$gew_spieltag?></h2>

<!-- Filter fuer die unterschiedlichen Darstellungen -->
<?php if ($show_filter): ?>
    <?php include '../../templates/tabellen/mt_filterauswahl.tmp.php'; ?>
<?php endif; ?>

<!-- Auswahl des Spieltages ueber der Meisterschaftstabelle -->
<?php include '../../templates/tabellen/spieltagsauswahl.tmp.php'; ?>

<!-- Meisterschaftstabelle fuer large + medium -->
<div class="w3-responsive w3-card w3-hide-small">
    <?php include '../../' . $meisterschafts_tabelle_templates['desktop'][$filter]; ?>
</div>

<!-- Meisterschaftstabelle fuer small -->
<div class="w3-responsive w3-card w3-hide-large w3-hide-medium">
    <?php include '../../' . $meisterschafts_tabelle_templates['mobil'][$filter]; ?>
</div>

<!-- Auswahl des Spieltages unter der Meisterschaftstabelle -->    
<?php include '../../templates/tabellen/spieltagsauswahl.tmp.php'; ?>

<!-- ------------------------- RANGTABELLE ------------------------- -->
<h1 id="rang" class="w3-text-primary w3-border-primary">Rangtabelle</h1>
<p class="w3-border-top w3-border-grey w3-text-grey"><a href="#meister" class="no w3-hover-text-secondary">Zur Meisterschaftstabelle</a><span class="w3-right">Saison <?=Html::get_saison_string($saison)?></span></p>

<h2 class="w3-text-secondary w3-xlarge">Spieltag <?=$gew_spieltag?></h2>

<!-- Auswahl des Spieltages ueber der Rangtabelle -->  
<?php include '../../templates/tabellen/spieltagsauswahl.tmp.php'; ?>

<!-- Beginn der eigentlichen Rangtabelle -->
<div class="w3-responsive w3-card w3-hide-small">
    <?php include '../../' . $rang_tabelle_templates['desktop']; ?>
</div>

<!-- Rangtabelle für mobile Geräte -->
<div class="w3-responsive w3-card w3-hide-large w3-hide-medium">
    <?php include '../../' . $rang_tabelle_templates['mobil']; ?>
</div>

<!-- Auswahl des Spieltages unter der Rangtabelle -->
<?php include '../../templates/tabellen/spieltagsauswahl.tmp.php'; ?>


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