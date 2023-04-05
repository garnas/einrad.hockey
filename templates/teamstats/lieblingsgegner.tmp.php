<?php use App\Repository\Team\TeamRepository; ?>

<div class="w3-half">
    <div class="w3-panel w3-primary w3-card-4">
        <p class="w3-center w3-xlarge"><?= TeamRepository::get()->team($first_liebling['team_id'])->getName(); ?></p>
    </div>
</div>
<div class="w3-quarter">
    <div class="w3-panel w3-primary w3-card-4">
        <p class="w3-center w3-xlarge"><?=$first_liebling['win']?> / <?=$first_liebling['draw']?> / <?=$first_liebling['loss']?></p>
    </div>
</div>
<div class="w3-quarter">
    <div class="w3-panel w3-primary w3-card-4">
        <p class="w3-center w3-xlarge"><?=$first_liebling['goals']?> : <?=$first_liebling['goals_against']?></p>
    </div>
</div>