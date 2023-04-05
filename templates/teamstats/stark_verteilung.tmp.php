<h2>Gegen schwächere Teams</h2>
<div class="w3-row-padding w3-stretch">
    <!-- Spiele -->
    <div class="w3-quarter">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xlarge"><?=$stark['games']?></p>
            <p class="w3-center">Spiele</p>
        </div>
    </div>
    <!-- Siege -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-green w3-card-4">
            <p class="w3-center w3-xlarge"><?=$stark['win']?></p>
            <p class="w3-center">Siege</p>
        </div>
    </div>
    <!-- Unentschieden -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-yellow w3-card-4">
            <p class="w3-center w3-xlarge"><?=$stark['draw']?></p>
            <p class="w3-center">Unentschieden</p>
        </div>
    </div>
    <!-- Niederlagen -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-red w3-card-4">
            <p class="w3-center w3-xlarge"><?=$stark['loss']?></p>
            <p class="w3-center">Niederlagen</p>
        </div>
    </div>
</div>
<div class="w3-row-padding w3-stretch">
    <!-- Tore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xlarge"><?=$stark_tore['goals']?></p>
            <p class="w3-center">Tore</p>
        </div>
    </div>
    <!-- Gegentore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xlarge"><?=$stark_tore['goals_against']?></p>
            <p class="w3-center">Gegentore</p>
        </div>
    </div>
    <!-- Differenz -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xlarge"><?=$stark_tore['diff']?></p>
            <p class="w3-center">Differenz</p>
        </div>
    </div>
</div>
