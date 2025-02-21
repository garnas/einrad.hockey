<!-- Zeilen der Verwarnungen -->
<?php foreach ($strafen as $strafe) : ?>
    <?php if ($strafe['verwarnung'] == 'Nein') : ?>
        <div class="w3-card w3-leftbar w3-border-primary" style="margin-bottom: 15px;">
            <ul class="w3-ul">
                <li class="w3-text-primary"><b><?=$strafe['teamname']?></b></li>
                <li><?=$strafe['grund']?></li>
                <li><?=$strafe['prozentsatz']?>%</li>
                <?php if (!empty($strafe['datum'])) : ?>
                    <li><?=date("d.m.Y", strtotime($strafe['datum'])) . " (" . $strafe['ort'] . ")"?></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
<?php endforeach; ?>