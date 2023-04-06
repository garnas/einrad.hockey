<style>
    .ehl-green {background-color: hsl(154deg, 38%, 58%)}
    .ehl-yellow {background-color: hsl(38deg, 38%, 58%)}
    .ehl-red {background-color: hsl(7deg, 38%, 58%)}
</style>

<!-- Tabelle mit Ergebnissen gegen alle anderen Teams -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Gesamtübersicht über alle Teams</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>Dargestellt werden alle bisherigen Spiele gruppiert nach dem jeweiligen Gegner.</span>
        </p>
    </div>
    <?php include "gegner.tmp.php"; ?>
</div>

<!-- Panels und Tabelle mit Lieblingsgegner -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Lieblingsgegner</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>Ausschlaggebend ist die Anzahl der Siege gegen ein anderes Team. Bei Gleichheit entscheidet die Summe der Anzahl aus Unentschieden und Niederlagen.</span>
        </p>
    </div>
    <?php if (isset($first_liebling)) include "lieblingsgegner.tmp.php"; ?>
    <?php if (!empty($liebling)) include "lieblingsgegner_table.tmp.php"; ?>
</div>

<!-- Panels und Tabelle mit Angstgegner -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Angstgegner</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span> </span>
        </p>
    </div>
    <?php if (isset($first_angst)) include "angstgegner.tmp.php"; ?>
    <?php if (!empty($angst)) include "angstgegner_table.tmp.php"; ?>
</div>

<!-- Panels mit Turnierergebnissen -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Turnierergebnisse</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span> </span>
        </p>
    </div>
    <?php include "turnierergebnisse.tmp.php"; ?>
</div>

<!-- Panels mit Spielergebnissen -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Spielergebnisse</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span> </span>
        </p>
    </div>
    <?php include "spielergebnisse.tmp.php"; ?>
</div>


<div class="w3-row-padding">
    <h3 class="w3-text-secondary">Verteilung gegen alle Teams</h3>
    <p class="w3-border-top w3-border-grey w3-text-grey">
        <span> </span>
    </p>
    <?php include "gesamt_verteilung.tmp.php"; ?>
</div>

<div class="w3-row-padding">
    <h3 class="w3-text-secondary">Verteilung gegen stärkere Teams</h3>
    <p class="w3-border-top w3-border-grey w3-text-grey">
        <span> </span>
    </p>
    <?php include "schwach_verteilung.tmp.php"; ?>
</div>

<div class="w3-row-padding">
    <h3 class="w3-text-secondary">Verteilung gegen schwächere Teams</h3>
    <p class="w3-border-top w3-border-grey w3-text-grey">
        <span> </span>
    </p>
    <?php include "stark_verteilung.tmp.php"; ?>
</div>