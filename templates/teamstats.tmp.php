<style>
    .ehl-green {background-color: hsl(154deg, 38%, 58%)}
    .ehl-yellow {background-color: hsl(38deg, 38%, 58%)}
    .ehl-red {background-color: hsl(7deg, 38%, 58%)}
</style>
<h2>Turniere</h2>
<div class="w3-row-padding w3-stretch">
    <!-- Bestes Turnier -->
    <div class="w3-half">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$bestes_turnier_string?></p>
            <p class="w3-center">Bestes Turnier</p>
        </div>
    </div>
    <!-- Schlechtestes Turnier -->
    <div class="w3-half">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$schlechtestes_turnier_string?></p>
            <p class="w3-center">Schlechtestes Turnier</p>
        </div>
    </div>
</div>
<h2>Spiele</h2>
<div class="w3-row-padding w3-stretch">
    <!-- Hoechster Sieg -->
    <div class="w3-half">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$hoechster_sieg_string?></p>
            <p class="w3-center">Höchster Sieg</p>
        </div>
    </div>
    <!-- Hoechste Niederlage -->
    <div class="w3-half">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$hoechste_niederlage_string?></p>
            <p class="w3-center">Höchste Niederlage</p>
        </div>
    </div>
</div>
<h2>Gegen alle Teams</h2>
<div class="w3-row-padding w3-stretch">
    <!-- Spiele -->
    <div class="w3-quarter">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$alle['games']?></p>
            <p class="w3-center">Spiele</p>
        </div>
    </div>
    <!-- Siege -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-green w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$alle['win']?></p>
            <p class="w3-center">Siege</p>
        </div>
    </div>
    <!-- Unentschieden -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-yellow w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$alle['draw']?></p>
            <p class="w3-center">Unentschieden</p>
        </div>
    </div>
    <!-- Niederlagen -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-red w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$alle['loss']?></p>
            <p class="w3-center">Niederlagen</p>
        </div>
    </div>
</div>
<div class="w3-row-padding w3-stretch">
    <!-- Tore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$alle_tore['goals']?></p>
            <p class="w3-center">Tore</p>
        </div>
    </div>
    <!-- Gegentore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$alle_tore['goals_against']?></p>
            <p class="w3-center">Gegentore</p>
        </div>
    </div>
    <!-- Differenz -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$alle_tore['diff']?></p>
            <p class="w3-center">Differenz</p>
        </div>
    </div>
</div>
<h2>Gegen stärkere Teams</h2>
<div class="w3-row-padding w3-stretch">
    <!-- Spiele -->
    <div class="w3-quarter">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$schwach['games']?></p>
            <p class="w3-center">Spiele</p>
        </div>
    </div>
    <!-- Siege -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-green w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$schwach['win']?></p>
            <p class="w3-center">Siege</p>
        </div>
    </div>
    <!-- Unentschieden -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-yellow w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$schwach['draw']?></p>
            <p class="w3-center">Unentschieden</p>
        </div>
    </div>
    <!-- Niederlagen -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-red w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$schwach['loss']?></p>
            <p class="w3-center">Niederlagen</p>
        </div>
    </div>
</div>
<div class="w3-row-padding w3-stretch">
    <!-- Tore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$schwach_tore['goals']?></p>
            <p class="w3-center">Tore</p>
        </div>
    </div>
    <!-- Gegentore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$schwach_tore['goals_against']?></p>
            <p class="w3-center">Gegentore</p>
        </div>
    </div>
    <!-- Differenz -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$schwach_tore['diff']?></p>
            <p class="w3-center">Differenz</p>
        </div>
    </div>
</div>
<h2>Gegen schwächere Teams</h2>
<div class="w3-row-padding w3-stretch">
    <!-- Spiele -->
    <div class="w3-quarter">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$stark['games']?></p>
            <p class="w3-center">Spiele</p>
        </div>
    </div>
    <!-- Siege -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-green w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$stark['win']?></p>
            <p class="w3-center">Siege</p>
        </div>
    </div>
    <!-- Unentschieden -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-yellow w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$stark['draw']?></p>
            <p class="w3-center">Unentschieden</p>
        </div>
    </div>
    <!-- Niederlagen -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-red w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$stark['loss']?></p>
            <p class="w3-center">Niederlagen</p>
        </div>
    </div>
</div>
<div class="w3-row-padding w3-stretch">
    <!-- Tore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$stark_tore['goals']?></p>
            <p class="w3-center">Tore</p>
        </div>
    </div>
    <!-- Gegentore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$stark_tore['goals_against']?></p>
            <p class="w3-center">Gegentore</p>
        </div>
    </div>
    <!-- Differenz -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$stark_tore['diff']?></p>
            <p class="w3-center">Differenz</p>
        </div>
    </div>
</div>
