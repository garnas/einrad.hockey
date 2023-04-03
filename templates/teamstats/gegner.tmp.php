<div class="w3-row-padding">
    <h3 class="w3-text-secondary">Gesamtübersicht über alle Teams</h3>
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
            </tr>
            </thead>
            <?php foreach ($teams as $team_id => $team):?>
                <tr>
                    <td><?=$team_id?></td>
                    <td class="w3-center"><?=$team['win']?> / <?=$team['draw']?> / <?=$team['loss']?></td>
                    <td class="w3-center"><?=$team['goals']?>:<?=$team['goals_against']?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>