<!-- Header der Meisterschaftstabelle -->
<div class="w3-row w3-primary">
    <div class="w3-row">
        <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width: 36px;"><b>#</b></div>
        <div class="w3-col w3-right w3-padding-8 w3-center" style="width: 42px;"><!-- Platz fuer das Icon --></div>
        <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 6em;"><b>Punkte</b></div>
        <div class="w3-rest w3-padding-8"><b>Team</b></div>
    </div>
</div>

<!-- Zeilen der Meisterschaftstabelle -->
<div>
    <?php $counter = 0; ?>
    <?php foreach ($meisterschafts_tabelle as $key => $zeile): ?>
        <?php $nthcolor = $counter % 2 == 0 ? '' : 'w3-light-grey'; ?>
        
        <!-- Kopfzeile fuer das Team -->
        <div id="small-meister-head-<?=$key?>" class="w3-row <?=$nthcolor?> w3-border-bottom w3-border-grey">
            <div class="w3-col w3-left w3-padding-8 w3-right-align?>" style="width: 36px;"><!-- Platzierung --></div>
            <div class="w3-col w3-right w3-padding-8 w3-center" style="width: 42px;">
                <span onclick="show_small_results('meister', <?=$key?>)">
                    <span id="small-meister-icon-show-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:block">arrow_drop_down</span>
                    <span id="small-meister-icon-hide-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:none">arrow_drop_up</span>
                </span>
            </div>
            <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 4em"><?=number_format($zeile['summe'] ?: 0, 0, ",", ".")?></div>
            <div class="w3-rest w3-padding-8"><?=$zeile['teamname'] . (!empty($zeile['hat_strafe']) ? '<a class="no w3-text-primary w3-hover-text-secondary" href="#strafen">*</a>' : '')?></div>
        </div>

        <!-- Details zu den Turnieren des Teams -->
        <?php if (!empty($zeile['details'])): ?>
            <?php foreach ($zeile['details'] as $ergebnis): ?>
                <div class="small-meister-result-<?=$key?> w3-row w3-text-primary <?=$nthcolor?>" style="display: none;">
                    <div class="w3-col w3-left w3-padding-8" style="width: 36px;">&nbsp;</div>
                    <div class="w3-col w3-left w3-padding-8" style="width: 90px;"><?=date_format(date_create($ergebnis['datum']), "d.m.y")?></div>
                    <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 100px;">
                        <a href="ergebnisse.php?saison=<?=$saison?>#<?=$ergebnis['turnier_id']?>" class="no w3-hover-text-secondary"> 
                            <?=number_format($ergebnis['ergebnis'] ?: 0, 0, ",", ".")?>
                        </a>
                    </div>
                    <div class="w3-rest w3-padding-8 w3-left-align"><?=$ergebnis['ort']?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php $counter++; ?>
    <?php endforeach; ?>
</div>