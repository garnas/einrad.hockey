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
                <td class="w3-right-align">11. - 12.06.2022</td>
                <td>Finale der Deutschen Einradhockeyliga</td>
                <td class="w3-right-align">Thedinghausen / Lilienthal</td>
            </tr>
            <!-- B-Finale der Deutschen Einradhockeyliga -->
            <tr>
                <td class="w3-right-align">18. - 19.06.2022</td>
                <td>B-Finale der Deutschen Einradhockeyliga</td>
                <td class="w3-right-align">Meißen</td>
            </tr>
            <!-- Saisonschlussturnier -->
            <tr>
                <?php if (isset($dfinale)) { ?>
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