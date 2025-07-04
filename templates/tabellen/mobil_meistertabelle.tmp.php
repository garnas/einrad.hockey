<!-- Header der Meisterschaftstabelle -->
<div class="w3-row w3-primary">
    <div class="w3-row">
        <div class="w3-col s1 w3-padding-8 w3-right-align"><b>#</b></div>
        <div class="w3-col s8 w3-padding-8 w3-left-align"><b>Team</b></div>
        <div class="w3-col s3 w3-padding-8 w3-right-align"><b>Summe</b></div>
    </div>
</div>

<!-- Zeilen der Meisterschaftstabelle -->
<div>
    <?php $counter = 0; ?>
    <?php foreach ($meisterschafts_tabelle as $key => $zeile): ?>
        <?php $color = (count($zeile['details']) >= 4) ? '' : 'w3-light-grey'; ?>
        
        <!-- Kopfzeile fuer das Team -->
        <div id="small-meister-head-<?=$key?>" class="w3-row <?=$color?> w3-border-bottom w3-border-grey">
            <div class="w3-col w3-left w3-padding-8 w3-right-align <?=$platz_color[$zeile['platz']] ?? ''?>" style="width: 36px;"><?=$zeile['platz']?></div>
            <div class="w3-col w3-right w3-padding-8 w3-center" style="width: 42px;">
                <span onclick="show_small_results('meister', <?=$key?>)">
                    <span id="small-meister-icon-show-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:block">arrow_drop_down</span>
                    <span id="small-meister-icon-hide-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:none">arrow_drop_up</span>
                </span>
            </div>
            <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 4em"><?=number_format($zeile['summe'] ?: 0, 0, ",", ".") . (!empty($zeile['hat_strafe']) ? '<a class="no w3-text-primary w3-hover-text-secondary" href="#strafen">*</a>' : '')?></div>
            <div class="w3-rest w3-padding-8"><?=$zeile['teamname']?></div>
        </div>

        <!-- Details zu den Turnieren des Teams -->
        <?php if (!empty($zeile['details'])): ?>
            <?php foreach ($zeile['details'] as $ergebnis): ?>
                <div class="small-meister-result-<?=$key?> w3-row w3-text-primary <?=$color?>" style="display: none;">
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