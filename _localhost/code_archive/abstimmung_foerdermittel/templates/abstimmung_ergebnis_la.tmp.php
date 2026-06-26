<?php if (!empty($ergebnisse)):?>
    <h2 class="w3-large w3-text-secondary">Weitere Anmerkungen / Vorschläge</h2>
    <?php if (empty($weiteres)):?>
        <p class="w3-panel w3-leftbar w3-border-bottom w3-border-primary w3-text-primary">Es wurden keine weiteren Vorschläge gemacht.</p>
    <?php else:?>
        <table class="w3-table w3-striped">
            <?php foreach ($weiteres['Weiteres'] as $key => $weitere):?>
                <tr class="w3-border-primary w3-border"><td><?=$key?></td></tr>
            <?php endforeach;?>
        </table>
    <?php endif;?>
<?php endif;?>