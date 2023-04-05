<?php use App\Repository\Team\TeamRepository; ?>

<h3 class="w3-text-secondary">Angstgegner</h3>
<div class="w3-row-padding">
    <div class="w3-twothird">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?= TeamRepository::get()->team($first_angst['team_id'])->getName(); ?></p>
            <p class="w3-center">Gegner</p>
        </div>
    </div>
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$first_angst['win']?> / <?=$first_angst['draw']?> / <?=$first_angst['loss']?></p>
            <p class="w3-center">Sieg / Unentschieden / Niederlage</p>
        </div>
    </div>
    <div class="w3-twothird">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$first_angst['goals']?>:<?=$first_angst['goals_against']?></p>
            <p class="w3-center">Tore:Gegentore</p>
        </div>
    </div>
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?=$first_angst['goals']-$first_angst['goals_against']?></p>
            <p class="w3-center">Differenz</p>
        </div>
    </div>
</div>