<!-- Zeilen der Verwarnungen -->
<?php

use App\Repository\Team\TeamRepository;

?>

<?php foreach ($strafen as $strafe) : ?>
    <?php if ($strafe->isVerwarnung()) : ?>
        <div class="w3-card w3-leftbar w3-border-primary" style="margin-bottom: 15px;">
            <ul class="w3-ul">
                <li class="w3-text-primary"><b><?=TeamRepository::get()->getTeamName($strafe->getTeam(), $strafe->getTurnier()->getSaison())?></b></li>
                <li><?=$strafe->getGrund()?></li>
                <?php if ($strafe->getTurnier()) : ?>
                    <li>
                        <?= $strafe->getTurnier()->getDatum()->format("d.m.Y") . " (" . $strafe->getTurnier()->getDetails()->getOrt() . ")" ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
<?php endforeach; ?>