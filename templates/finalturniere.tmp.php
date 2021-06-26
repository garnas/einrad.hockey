<!-- Liste der Finalturniere -->

<h1 class="w3-text-primary">Finalturniere der Saison <?= Html::get_saison_string() ?></h1>

    <div class="w3-container">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <?php if (isset($finale)) { ?>
                <li>Das <a href='turnier_details.php?turnier_id=<?=$finale['turnier_id']?>'>Finale der Deutschen Einradhockeyliga</a> findet am <?=$finale['datum']?> in <?=$finale['ort']?> statt.</li>
            <?php } else { ?>
                <li>Für das Finale der Deutschen Einradhockeyliga am 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_EINS))?> / 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_ZWEI))?> / 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_DREI))?> / 
                    <?=strftime("%d.%m.%y", strtotime(Config::FINALE_VIER))?>
                    wird noch ein Ausrichter gesucht.</li>
            <?php } ?>

            <?php if (isset($bfinale)) { ?>
                <li>Das <a href='turnier_details.php?turnier_id=<?=$bfinale['turnier_id']?>'>B-Finale der Deutschen Einradhockeyliga</a> findet am <?=$bfinale['datum']?> in <?=$bfinale['ort']?> statt.</li>
            <?php } else { ?>
                <li>Für das B-Finale der Deutschen Einradhockeyliga am 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_EINS))?> / 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_ZWEI))?> / 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_DREI))?> / 
                    <?=strftime("%d.%m.%y", strtotime(Config::FINALE_VIER))?>
                    wird noch ein Ausrichter gesucht.</li>
            <?php } ?>

            <?php if (isset($dfinale)) { ?>
                <li>Das <a href='turnier_details.php?turnier_id=<?=$cfinale['turnier_id']?>'>C-Finale der Deutschen Einradhockeyliga</a> findet am <?=$dfinale['datum']?> in <?=$cfinale['ort']?> statt.</li>
            <?php } else { ?>
                <li>Für das C-Finale der Deutschen Einradhockeyliga am 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_EINS))?> / 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_ZWEI))?> / 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_DREI))?> / 
                    <?=strftime("%d.%m.%y", strtotime(Config::FINALE_VIER))?>
                    wird noch ein Ausrichter gesucht.</li>
            <?php } ?>

            <?php if (isset($dfinale)) { ?>
                <li>Das <a href='turnier_details.php?turnier_id=<?=$dfinale['turnier_id']?>'>Saisonschlussturnier</a> findet am <?=$dfinale['datum']?> in <?=$dfinale['ort']?> statt.</li>
            <?php } else { ?>
                <li>Für das Saisonschlussturnier am 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_EINS))?> / 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_ZWEI))?> / 
                    <?=strftime("%d.%m", strtotime(Config::FINALE_DREI))?> / 
                    <?=strftime("%d.%m.%y", strtotime(Config::FINALE_VIER))?>
                    wird noch ein Ausrichter gesucht.</li>
            <?php } ?>
        </ul>
    </div>