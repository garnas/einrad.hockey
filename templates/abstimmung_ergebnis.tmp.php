<h1 class="w3-text-primary">Abstimmungsergebnis</h1>

<p class="w3-text-grey">
    Start: <?= date("d.m.Y H:i", $beginn)?>&nbsp;Uhr
    <br>
    Ende: <?=date("d.m.Y H:i", $abschluss)?>&nbsp;Uhr
</p>
<?php Form::countdown(Abstimmung::ENDE);?>
<?php foreach($tabelle as $key => $zeile) { ?>
    <div class="w3-section">
        <span><b><?=$key?></b></span>
        <div class="w3-light-grey w3-round w3-center">
            <div class="<?=$zeile['farbe']?> w3-round" style="width:<?=$zeile['prozent']?>; height:24px;"></div>
        </div>
        <span class=""><i><?=$zeile['stimmen']?> Stimmen (<?=$zeile['prozent']?>)</i></span>
    </div>
<?php } ?>
<div class="w3-section">
    <span><b>Wahlbeteiligung</b></span>
    <div class="w3-light-grey w3-round">
        <div class="w3-black w3-round" style="width:<?=$wahlbeteiligung?>; height:24px;"></div>
    </div>
    <span class=""><i><?=$abgegebene_stimmen?> von <?=$anzahl_teams?> Teams (<?=$wahlbeteiligung?>)</i></span>
</div>