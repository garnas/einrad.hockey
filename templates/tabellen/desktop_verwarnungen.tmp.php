<!-- Header der Verwarnungen -->
<div class="w3-row w3-primary"> 
    <div class="w3-col w3-padding-8 l3 m3 w3-left-align"><b>Team</b></div>
    <div class="w3-col w3-padding-8 l6 m6 w3-left-align"><b>Grund</b></div>
    <div class="w3-col w3-padding-8 l3 m3 w3-left-align"><b>Datum (Ort)</b></div>
</div>
<!-- Zeilen der Verwarnungen -->
<?php use App\Entity\Team\Strafe;

$counter = 0;

/** @var Strafe[] $strafen */
foreach ($strafen as $strafe) : ?>
    <?php if ($strafe->isVerwarnung()) : ?>
        <?php $row_class = $counter % 2 == 1 ? "w3-light-grey" : ""; ?>
        <div class="w3-row <?=$row_class?>">
            <div class="w3-col w3-padding-8 l3 m3 w3-left-align"><?=$strafe->getTeam()->getName()?></div>
            <div class="w3-col w3-padding-8 l6 m6 w3-left-align"><?=$strafe->getGrund()?></div>
            <div class="w3-col w3-padding-8 l3 m3 w3-left-align">
                <?= $strafe->getTurnier() ? $strafe->getTurnier()->getDatum()->format("d.m.Y") . " (" . $strafe->getTurnier()->getDetails()->getOrt() . ")" : "-" ?>
            </div>
        </div>
        <?php $counter++; ?>
    <?php endif; ?>
<?php endforeach; ?>