<?php use App\Repository\Team\TeamRepository; ?>

<div class="w3-row-padding">
    <h3 class="w3-text-secondary">Angstgegner</h3>
    <p class="w3-border-top w3-border-grey w3-text-grey">
        <span> </span>
    </p>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <thead class="w3-primary">
            <tr>
                <th>Team</th>
                <th class="w3-center">Sieg / Unentschieden / Niederlage</th>
                <th class="w3-center">Tore:Gegentore</th>
                <th class="w3-center">Differenz</th>
            </tr>
            </thead>
            <?php foreach ($angst as $team_id => $team): ?>
                <tr>
                    <td><?=TeamRepository::get()->team($team_id)->getName();?></td>
                    <td class="w3-center"><?=$team['win']?> / <?=$team['draw']?> / <?=$team['loss']?></td>
                    <td class="w3-center"><?=$team['goals']?>:<?=$team['goals_against']?></td>
                    <td class="w3-right"><?=$team['goals']-$team['goals_against']?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>