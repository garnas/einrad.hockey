<div class="w3-row w3-text-primary w3-margin-top w3-margin-bottom w3-small">
    <div class="w3-col l3 m1 s3 w3-left-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=0#meister" class="no w3-hover-text-secondary"><?=Html::icon('first_page')?> <span class="w3-hide-small w3-hide-medium">Erster Spieltag</span></a>
    </div>
    <div class="w3-col l3 m5 s3 w3-left-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=max($gew_spieltag-1, 0)?>#meister" class="no w3-hover-text-secondary"><?=Html::icon('keyboard_arrow_left')?> <span class="w3-hide-small">Vorheriger Spieltag </span></a>
    </div>
    <div class="w3-col l3 m5 s3 w3-right-align">
        <a href="tabelle.php?saison=<?=$saison?>&spieltag=<?=min($gew_spieltag+1, $akt_spieltag)?>#meister" class="no w3-hover-text-secondary"><span class="w3-hide-small"> Nächster Spieltag</span> <?=Html::icon('keyboard_arrow_right')?></a>
    </div>
    <div class="w3-col l3 m1 s3 w3-right-align">
        <a href="tabelle.php?saison=<?=$saison?>#meister" class="no w3-hover-text-secondary"><span class="w3-hide-small w3-hide-medium">Aktueller Spieltag</span> <?=Html::icon('last_page')?></a>
    </div>
</div>