<!-- Liste der Finalturniere -->

<h1 class="w3-text-primary">Finalturniere der Saison <?= Html::get_saison_string() ?></h1>

    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <thead>
                <tr class="w3-primary">
                    <td>Datum</td>
                    <td>Finalturnier</td>
                    <td>Ort</td>
                </tr>            
            </thead>
            <!-- Finale der Deutschen Einradhockeyliga -->
            <tr>
                <?php if (isset($finale)) { ?>
                    <td class="w3-right-align"><?=$finale['datum']?></td>
                    <td><?= Html::link('turnier_details.php?turnier_id=' . $finale['turnier_id'], "Finale der Deutschen Einradhockeyliga", false) ?></td>
                    <td class="w3-right-align"><?=$finale['ort']?></td>
                <?php } else { ?>
                    <td class="w3-right-align"><?=strftime("%d.", strtotime(Config::FINALE_EINS))?> / <?=strftime("%d.", strtotime(Config::FINALE_ZWEI))?> / <?=strftime("%d.", strtotime(Config::FINALE_DREI))?> / <?=strftime("%d.%m.%y", strtotime(Config::FINALE_VIER))?></td>
                    <td>Finale der Deutschen Einradhockeyliga</td>
                    <td class="w3-right-align">Ausrichter gesucht</td>
                <?php } ?>
            </tr>
            <!-- B-Finale der Deutschen Einradhockeyliga -->
            <tr>
                <?php if (isset($bfinale)) { ?>
                    <td class="w3-right-align"><?=$bfinale['datum']?></td>
                    <td><?= Html::link('turnier_details.php?turnier_id=' . $bfinale['turnier_id'], "B-Finale der Deutschen Einradhockeyliga", false) ?></td>
                    <td class="w3-right-align"><?=$bfinale['ort']?></td>
                <?php } else { ?>
                    <td class="w3-right-align"><?=strftime("%d.", strtotime(Config::FINALE_EINS))?> / <?=strftime("%d.", strtotime(Config::FINALE_ZWEI))?> / <?=strftime("%d.", strtotime(Config::FINALE_DREI))?> / <?=strftime("%d.%m.%y", strtotime(Config::FINALE_VIER))?></td>
                    <td>B-Finale der Deutschen Einradhockeyliga</td>
                    <td class="w3-right-align">Ausrichter gesucht</td>
                <?php } ?>
            </tr>
            <!-- C-Finale der Deutschen Einradhockeyliga -->
            <tr>
                <?php if (isset($cfinale)) { ?>
                    <td class="w3-right-align"><?=$cfinale['datum']?></td>
                    <td><?= Html::link('turnier_details.php?turnier_id=' . $cfinale['turnier_id'], "C-Finale der Deutschen Einradhockeyliga", false) ?></td>
                    <td class="w3-right-align"><?=$cfinale['ort']?></td>
                <?php } else { ?>
                    <td class="w3-right-align"><?=strftime("%d.", strtotime(Config::FINALE_EINS))?> / <?=strftime("%d.", strtotime(Config::FINALE_ZWEI))?> / <?=strftime("%d.", strtotime(Config::FINALE_DREI))?> / <?=strftime("%d.%m.%y", strtotime(Config::FINALE_VIER))?></td>
                    <td>C-Finale der Deutschen Einradhockeyliga</td>
                    <td class="w3-right-align">Ausrichter gesucht</td>
                <?php } ?>
            </tr>
            <!-- Saisonschlussturnier -->
            <tr>
                <?php if (isset($cfinale)) { ?>
                    <td class="w3-right-align"><?=$dfinale['datum']?></td>
                    <td><?= Html::link('turnier_details.php?turnier_id=' . $dfinale['turnier_id'], "Saisonschlussturnier", false) ?></td>
                    <td class="w3-right-align"><?=$dfinale['ort']?></td>
                <?php } else { ?>
                    <td class="w3-right-align"><?=strftime("%d.", strtotime(Config::FINALE_EINS))?> / <?=strftime("%d.", strtotime(Config::FINALE_ZWEI))?> / <?=strftime("%d.", strtotime(Config::FINALE_DREI))?> / <?=strftime("%d.%m.%y", strtotime(Config::FINALE_VIER))?></td>
                    <td>Saisonschlussturnier</td>
                    <td class="w3-right-align">Ausrichter gesucht</td>
                <?php } ?>
            </tr>
        </table>
    </div>