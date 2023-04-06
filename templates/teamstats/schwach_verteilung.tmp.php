<div class="w3-row-padding">
    <!-- Spiele -->
    <div class="w3-quarter">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xlarge"><?=$schwach['games']?></p>
            <p class="w3-center">Spiele</p>
        </div>
    </div>
    <!-- Siege -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-green w3-card-4">
            <p class="w3-center w3-xlarge"><?=$schwach['win']?></p>
            <p class="w3-center">Siege</p>
        </div>
    </div>
    <!-- Unentschieden -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-yellow w3-card-4">
            <p class="w3-center w3-xlarge"><?=$schwach['draw']?></p>
            <p class="w3-center">Unentschieden</p>
        </div>
    </div>
    <!-- Niederlagen -->
    <div class="w3-quarter">
        <div class="w3-panel ehl-red w3-card-4">
            <p class="w3-center w3-xlarge"><?=$schwach['loss']?></p>
            <p class="w3-center">Niederlagen</p>
        </div>
    </div>
    <!-- Tore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xlarge"><?=$schwach_tore['goals']?></p>
            <p class="w3-center">Tore</p>
        </div>
    </div>
    <!-- Gegentore -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xlarge"><?=$schwach_tore['goals_against']?></p>
            <p class="w3-center">Gegentore</p>
        </div>
    </div>
    <!-- Differenz -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xlarge"><?=$schwach_tore['diff']?></p>
            <p class="w3-center">Differenz</p>
        </div>
    </div>
</div>