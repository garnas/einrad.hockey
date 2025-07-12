<?php

use App\Service\Turnier\TurnierSnippets;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierLinks;

?>

<section id="<?= $turnier_id ?>" style="padding-top: 16px; padding-bottom: 32px;">
    <div class="w3-row">
        <div class="w3-col">
            <h3><?= TurnierSnippets::datumOrtBlock($turnier) ?></h3>
        </div>
    </div>
    
    <?php if (!empty($turnier->getName())): ?>
        <div class="w3-row w3-padding-8 <?= $turnier->isFinalTurnier() ? "w3-tertiary" : "w3-primary" ?>">
            <div class="w3-col w3-center">
                <b><?= $turnier->getName() ?></b>
            </div>
        </div>
    <?php endif; ?>

    <!-- Header fuer die Ergebnis-Tabelle -->
    <div class="w3-row w3-primary">
        <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width: 36px;">#</div>
        <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 80px;">Punkte</div>
        <div class="w3-rest w3-padding-8">Team</div>
    </div>

    <!-- Team-Ergebnisse in der Ergebnis-Tabelle -->
    <?php foreach ($turnier->getErgebnis() as $key => $ergebnis): ?>
        <div class="w3-row w3-border-bottom w3-border-grey <?= $key % 2 == 0 ? '' : 'w3-light-grey' ?>">
            <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width: 36px"><?= $ergebnis->getPlatz() ?></div>
            <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 80px;"><?= $ergebnis->getErgebnis() ? number_format($ergebnis->getErgebnis() ?: 0, 0, ",", ".") : '-' ?></div>
            <div class="w3-rest w3-padding-8"><?= $ergebnis->getTeam()->getName() ?></div>
        </div>                   
    <?php endforeach; ?>

    <div class="w3-row">
        <div class="w3-col w3-padding-8 w3-left-align w3-text-grey" style="width: 150px;">
            <?php if (TurnierService::hasNlTeamErgebnis($turnier)): ?>
                * Nichtligateam
            <?php endif; ?>
        </div>
        <div class="w3-rest w3-padding-8 w3-right-align">
            <?= Html::link(TurnierLinks::spielplan($turnier), 'Spielergebnisse', icon:'open_in_new') ?>
        </div>
    </div>
</section>