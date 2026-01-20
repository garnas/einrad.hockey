<!-- Header der Meisterschaftstabelle -->
<div class="w3-row w3-primary">
    <div class="w3-col w3-right w3-padding-8" style="width: 42px;"><!-- Platz fuer das Icon --></div>
    <div class="w3-rest">
        <div class="w3-row">
            <div class="w3-col l1 m1 w3-padding-8 w3-right-align"><!-- Platzierung --></div>
            <div class="w3-col l10 m9 w3-padding-8 w3-left-align"><b>Team</b></div>
            <div class="w3-col l1 m2 w3-padding-8 w3-right-align"><b>Punkte</b></div>
        </div>
    </div>
</div>

<!-- Zeilen der Meisterschaftstabelle -->
<div>
    <?php $counter = 0; ?>
    <?php foreach ($meisterschafts_tabelle as $key => $zeile): ?>        
        <?php if ($counter % 2 == 0) { $color = "w3-light-grey"; } else { $color = "w3-white"; } ?>
        <!-- Kopfzeile fuer das Team -->
        <div  onclick="show_large_results('meister', <?=$key?>)" style="cursor: pointer;" id="large-meister-head-<?=$key?>" class="w3-row <?=$color?> w3-border-bottom w3-border-grey w3-hover-text-secondary">
            <div class="w3-col w3-right w3-padding-8 w3-center" style="width: 42px;">
                <? if (!empty($zeile['details'])): ?>
                    <span>
                        <span id="large-meister-icon-show-<?=$key?>" class="material-icons w3-text-primary" style="display:block">arrow_drop_down</span>
                        <span id="large-meister-icon-hide-<?=$key?>" class="material-icons w3-text-primary" style="display:none">arrow_drop_up</span>
                    </span>
                <? endif; ?>
            </div>
            <div class="w3-rest">
                <div class="w3-row">
                    <div class="w3-col l1 m1 w3-padding-8 w3-right-align?>"><!-- Platzierung --></div>
                    <div class="w3-col l8 m8 w3-padding-8"><?=$zeile['teamname'] . (!empty($zeile['hat_strafe']) ? '<a class="no w3-text-primary w3-hover-text-secondary" href="#strafen">*</a>' : '')?></div>
                    <div class="w3-col l3 m3 w3-padding-8 w3-right-align"><?=number_format($zeile['summe'] ?: 0, 0, ",", ".")?></div>
                </div>
            </div>
        </div>

        <!-- Details zu den Turnieren des Teams -->
        <?php if (!empty($zeile['details'])): ?>
            <?php foreach ($zeile['details'] as $dey => $ergebnis): ?>
                <a href="ergebnisse.php?saison=<?= $saison ?>#<?= $ergebnis['turnier_id'] ?>" class="no">
                    <div class="large-meister-result-<?= $key ?> w3-row w3-text-primary <?= $color ?> w3-hover-text-secondary"
                         style="display: none;">
                        <div class="w3-col l2 m2 w3-padding-8 w3-right-align"><?= date_format(date_create($ergebnis['datum']), "d.m.y") ?></div>
                        <div class="w3-col l2 m2 w3-padding-8"><?= $ergebnis['tblock'] ?></div>
                        <div class="w3-col l3 m3 w3-padding-8"><?= $ergebnis['ort'] ?></div>
                        <div class="w3-col l3 m3 w3-padding-8 w3-right-align">Platz <?= $ergebnis['platz'] ?>
                            / <?= $ergebnis['teilnehmer'] ?></div>
                        <div class="w3-col l2 m2 w3-padding-8 w3-right-align">
                            <?= number_format($ergebnis['ergebnis'] ?: 0, 0, ",", ".") ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>    
        <?php endif; ?>
        <?php $counter++; ?>
    <?php endforeach; ?>
</div>