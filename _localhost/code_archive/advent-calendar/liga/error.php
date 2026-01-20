<?php
// Logik
require_once '../../../init.php';
$saison = (isset($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;

// Layout
Html::$titel = "Adventskalender | Deutsche Einradhockeyliga";
Html::$content = "Adventskalender der Deutschen Einradhockeyliga für das Jahr 2025.";

include '../../../templates/header.tmp.php'; ?>

<link type="text/css" rel="stylesheet" href="style.css?20251127">
<link type="text/css" rel="stylesheet" href="colors.css?20251127">

<h1 class="w3-text-primary">Adventskalender</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<div class="w3-display-container w3-round-xlarge w3-padding-16 advent-color">
    <div class="slide-container">
        <img class="slide advent-color" src="../../../bilder/advent/error.jpg" />
    </div>
</div>

<?php include '../../../templates/footer.tmp.php'; ?>