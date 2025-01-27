<!-- Header der Rangtabelle -->
<div class="w3-row w3-primary">
    <div class="w3-col w3-right" style="width: 42px;">&nbsp;</div>
    <div class="w3-rest">
        <div class="w3-row">
            <div class="w3-col l1 m1 w3-padding-8 w3-right-align"><b>#</b></div>
            <div class="w3-col l1 m1 w3-padding-8"><b>Block</b></div>
            <div class="w3-col l1 m1 w3-padding-8 w3-right-align"><b>Wert</b></div>
            <div class="w3-col l6 m6 w3-padding-8"><b>Team</b></div>
            <div class="w3-col l3 m3 w3-padding-8 w3-right-align"><b>Mittelwert</b></div>
        </div>
    </div>
</div>

<!-- Zeilen der Rangtabelle -->
<div>
    <?php $counter = 0; ?>
    <?php foreach ($rang_tabelle as $key => $zeile): ?>
        <?php $block = Tabelle::rang_to_block($zeile['rang']); ?>
        <?php $nthcolor = $counter % 2 == 0 ? '' : 'w3-light-grey'; ?>
        
        <!-- Kopfzeile fuer das Team -->
        <div id="large-rang-head-<?=$key?>" class="w3-row <?=$nthcolor?> w3-border-bottom w3-border-grey">
            <div class="w3-col w3-right w3-padding-8 w3-center" style="width: 42px;">
                <? if (!empty($zeile['details'])): ?>
                    <span onclick="show_large_results('rang', <?=$key?>)" style="cursor: pointer;">
                        <span id="large-rang-icon-show-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:block">arrow_drop_up</span>
                        <span id="large-rang-icon-hide-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:none">arrow_drop_down</span>
                    </span>
                <? endif; ?>
            </div>
            <div class="w3-rest">
                <div class="w3-row">
                    <div class="w3-col l1 m1 w3-padding-8 w3-right-align"><?=$zeile['rang']?></div>
                    <div class="w3-col l1 m1 w3-padding-8 <?=$block_color[substr($block, 0, 1)]?>"><?=$block?></div>
                    <div class="w3-col l1 m1 w3-padding-8 w3-right-align"><?=Tabelle::rang_to_wertigkeit($zeile['rang'])?></div>
                    <div class="w3-col l7 m7 w3-padding-8"><?=$zeile['teamname']?></div>
                    <div class="w3-col l2 m2 w3-padding-8 w3-right-align"><?=number_format($zeile['avg'] ?: 0, 1, ",", ".")?></div>
                </div>
            </div>
        </div>
        <!-- Details zu den Turnieren des Teams -->
        <?php if (!empty($zeile['details'])): ?>
            <?php foreach ($zeile['details'] as $dey => $ergebnis): ?>
                <div class="large-rang-result-<?=$key?> w3-row <?=$saison != $ergebnis['saison'] ? 'w3-text-grey' : 'w3-text-primary'?>" style="display: none; <?=$details_style?>">
                    <div class="w3-col l2 m2 w3-padding-8 w3-right-align"><?=date_format(date_create($ergebnis['datum']), "d.m.y")?></div>
                    <div class="w3-col l2 m2 w3-padding-8"><?=$ergebnis['tblock']?></div>
                    <div class="w3-col l3 m3 w3-padding-8"><?=$ergebnis['ort']?></div>
                    <div class="w3-col l3 m3 w3-padding-8 w3-right-align">Platz <?=$ergebnis['platz']?> / <?=$ergebnis['teilnehmer']?></div>
                    <div class="w3-col l2 m2 w3-padding-8 w3-right-align">
                        <a href="ergebnisse.php?saison=<?=$ergebnis['saison']?>#<?=$ergebnis['turnier_id']?>" class="no w3-hover-text-secondary"> 
                            <?=number_format($ergebnis['ergebnis'] ?: 0, 0, ",", ".")?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>    
        <?php endif; ?>
        <?php $counter++; ?>
    <?php endforeach; ?>
</div>