<div class="w3-section">

    <h1 class="w3-text-primary">Abstimmungsergebnis</h1>

</div>
<?php foreach(Abstimmung::OPTIONS as $option => $text): ?>
    <div class="w3-section">
        <span><strong><?=$text?></strong></span>
        <div class="w3-light-grey w3-round w3-center">
            <div class="<?=Abstimmung::OPTIONS_COLOR[$option]?> w3-round"
                 style="width:<?=$ergebnisse[$option]['%']?>%; height:24px;"></div>
        </div>
        <span><i><?=$ergebnisse[$option]['anzahl']?> Stimmen (<?=$ergebnisse[$option]['%']?> %)</i></span>
    </div>
<?php endforeach; ?>
<div class="w3-section">
        <br>
        <span><b>Wahlbeteiligung</b></span>
        <div class="w3-light-grey w3-round">
            <div class="w3-black w3-round"
                 style="width:<?=$ergebnisse["%"]?>%; height:24px;"></div>
        </div>
        <span class=""><i><?=$ergebnisse["gesamt"]?> von <?=Abstimmung::ANZAHL_TEAMS?> Teams (<?=$ergebnisse["%"]?> %)</i></span>
</div>