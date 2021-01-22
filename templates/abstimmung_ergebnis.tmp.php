<h1 class="w3-text-primary">Abstimmungsergebnis</h1>

<p class="w3-text-grey">
    Start: <?=Abstimmung::BEGINN?>&nbsp;Uhr
    <br>
    Ende: <?=Abstimmung::ENDE?>&nbsp;Uhr
</p>
<?php foreach($display_ergebnisse as $ergebnis) { ?>
    <div class="w3-section">
        <span><?=$ergebnis['formulierung']?></span>
        <div class="w3-light-grey w3-round w3-center">
            <div class="<?=$ergebnis['farbe']?> w3-round" style="width:<?=$ergebnis['prozent']?>; height:24px;"></div>
        </div>
        <span><i><?=$ergebnis['stimmen']?> Stimmen (<?=$ergebnis['prozent']?>)</i></span>
    </div>
<?php } ?>
<div class="w3-section">
    <span><b>Wahlbeteiligung</b></span>
    <div class="w3-light-grey w3-round">
        <div class="w3-black w3-round" style="width:<?=$wahlbeteiligung?>; height:24px;"></div>
    </div>
    <span class=""><i><?=$abgegebene_stimmen?> von <?=$anzahl_teams?> Teams (<?=$wahlbeteiligung?>)</i></span>
</div>