<?php
    $block_color = array('A' => 'ehl-text-pink', 'B' => 'ehl-text-orange', 'C' => 'ehl-text-blue', 'D' => 'ehl-text-yellow', 'E' => 'ehl-text-green', 'F' => 'ehl-text-red');
?>

<!-- Header der Rangtabelle -->
<div class="w3-row w3-primary"> 
    <div class="w3-col w3-left w3-padding-8" style="width: 2em;"><b>#</b></div>
    <div class="w3-col w3-left w3-padding-8" style="width: 2em;"><b>Bl.</b></div>
    <div class="w3-col w3-right w3-padding-8" style="width: 6em;"><b>Mittelw.</b></div>
    <div class="w3-rest w3-padding-8"><b>Team</b></div>
</div>

<!-- Zeilen der Rangtabelle -->
<div>
    <?php $counter = 0; ?>
    <?php foreach ($rang_tabelle as $key => $zeile): ?>
        <?php $header_style = $counter % 2 == 1 ? "background-color: #ebebeb;" : ""; ?>
        <?php $details_style = $counter % 2 == 1 ? "background-color: #f5f5f5;" : ""; ?>
        <?php $block = Tabelle::rang_to_block($zeile['rang']); ?>
        
        <!-- Kopfzeile fuer das Team -->
        <div id="small-rang-head-<?=$key?>" class="w3-row" style="<?=$header_style?>" >
            <div class="w3-col w3-left w3-padding-8 w3-right-align" style="width: 36px;"><?=$zeile['rang']?></div>
            <div class="w3-col w3-left w3-padding-8 <?=$block_color[substr($block, 0, 1)]?>" style="width: 40px;"><?=$block?></div>
            <div class="w3-col w3-right w3-padding-8 w3-center" style="width: 42px;">
                <span onclick="show_small_results('rang', <?=$key?>)">
                    <span id="small-rang-icon-show-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:block">arrow_drop_up</span>
                    <span id="small-rang-icon-hide-<?=$key?>" class="material-icons w3-text-primary w3-hover-text-secondary" style="display:none">arrow_drop_down</span>
                </span>
            </div>
            <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 4em"><?=number_format($zeile['avg'] ?: 0, 1, ",", ".")?></div>
            <div class="w3-rest w3-padding-8"><?=$zeile['teamname']?></div>
        </div>

        <div id="small-rang-value-<?=$key?>" class="w3-row" style="display: none; <?=$header_style?>">
            <div class="w3-col w3-left" style="width: 84px;">&nbsp;</div>
            <div class="w3-rest w3-small w3-text-primary">Wertigkeit: <?=Tabelle::rang_to_wertigkeit($zeile['rang'])?></div>
        </div>
        
        <!-- Lade Turnierdetails nur, wenn es auch gespielte Turniere gibt -->
        <?php if (!empty($zeile['details'])): ?>
            <?php foreach ($zeile['details'] as $dey => $ergebnis): ?>
                <div class="small-rang-result-<?=$key?> w3-row <?=$saison != $ergebnis['saison'] ? 'w3-text-grey' : 'w3-text-primary'?>" style="display: none; <?=$details_style?>">
                    <div class="w3-col w3-left w3-padding-8" style="width: 36px;">&nbsp;</div>
                    <div class="w3-col w3-left w3-padding-8" style="width: 90px;"><?=date_format(date_create($ergebnis['datum']), "d.m.y")?></div>
                    <div class="w3-col w3-right w3-padding-8 w3-right-align" style="width: 100px;">
                        <a href="ergebnisse.php?saison=<?=$ergebnis['saison']?>#<?=$ergebnis['turnier_id']?>" class="no w3-hover-text-secondary"> 
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