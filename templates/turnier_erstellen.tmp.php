<?php

use App\Service\Turnier\BlockService;

?>
<form method="post">
    <div class="w3-panel w3-tertiary w3-card-4">
        <h3 class="w3-center">Ausrichter: <?= $ausrichter_name ?></h3>
    </div>

    <!-- Allgemein -->
    <div class="w3-panel w3-card-4">
        <h3 id="result">Turnierdaten</h3>
        <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
            <p>
                Ligaturniere müssen spätestens vier Wochen vor dem Spieltag eingetragen werden und dürfen nur an einem Samstag, Sonntag oder bundeseinheitlichen Feiertag stattfinden.
                Sie können frühstens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr (nach Spielplan) enden.
            </p>
        </div>

        <p>
            <label class="w3-text-primary" for="datum">Datum<span class="w3-text-secondary">*</span></label>
            <input required type="date" value="<?= $_POST['datum'] ?? date("Y-m-d", (time() + 4 * 7 * 24 * 60 * 60))?>" class="w3-input w3-border w3-border-primary" style="max-width: 320px" id="datum" name="datum">
        </p>
        
        <?php if (Helper::$ligacenter): ?>
            <p>
                <label class="w3-text-primary" for="datum_bis">Bis-Datum</label>
                <input type="date"
                       value="<?= $_POST['datum_bis'] ?? "" ?>"
                       class="w3-input w3-border w3-border-primary" style="max-width: 320px" id="datum_bis" name="datum_bis">
            </p>
        <?php endif; ?>
        <p>
            <label class="w3-text-primary" for="startzeit">Startzeit<span class="w3-text-secondary">*</span></label>
            <input required type="time" class="w3-input w3-border w3-border-primary" value="<?=$_POST['startzeit'] ?? '10:00'?>" style="max-width: 320px" id="startzeit" name="startzeit">
        </p>
        <p>
            <input class="w3-check" type="checkbox" id="besprechung" name="besprechung" <?php if (($_POST['besprechung'] ?? '') == "Ja") {?>checked<?php } ?> value="Ja">
            <label for="besprechung" class="w3-hover-text-secondary w3-text-primary" style="cursor: pointer">Gemeinsame Besprechung aller Teams 15 min vor Turnierbeginn</label>
        </p>
        <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
    </div>

    <!-- Modusspezifisch -->
    <div class="w3-panel w3-card-4">
        <h3 id="result">Liga</h3>
        <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
            <p>
                Ein Turnier kann automatisch um einen Block nach oben oder unten erweitert werden. Beim Übergang in die Setzphase wird zuerst die passende Blockzugehörigkeit 
                getestet und im Anschluss das Turnier erweitert. Danach erfolgt eine weitere Prüfung. Eine automatische Erweiterung auf ein 
                ABCDEF Turnier ist nicht möglich. Diese muss manuell in den eigenen Turnieren unter "Turnier bearbeiten" erfolgen.
            </p>
        </div>

        <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
            <p>
                Die Anzahl der Teams kann nur in der Wartephase bearbeitet werden.
            </p>
        </div>

        <div>
            <p>
                <label class="w3-text-primary" for="art">Turnierblock<span class="w3-text-secondary">*</span></label>
                <select required class="w3-select w3-border w3-border-primary w3-padding w3-white" id="block" name="block">
                    <option value="" disabled selected>Wähle einen Turnierblock</option>
                    <!-- Blockhöhere Turniere -->
                    <?php foreach ($block_higher as $block): ?>
                        <option <?php if (($_POST['block'] ?? '') === $block): ?> selected <?php endif; ?> value='II_<?=$block?>'><?=$block?>: Blockhöheres Turnier (II)</option>
                    <?php endforeach; ?>
                    <!-- Blockeigenes Turnier -->
                    <option <?php if (($_POST['block'] ?? '') === $ausrichter_block ) {?> selected <?php } ?> value=I_<?= $ausrichter_block ?>><?= $ausrichter_block?>: Blockeigenes Turnier (I)</option>
                    <?php if (Helper::$ligacenter): ?>
                        <!-- LA: Turnier mit fixiertem Block -->
                        <?php foreach (Config::BLOCK as $block): ?>
                            <option <?php if (($_POST['block'] ?? '') === $block): ?> selected <?php endif; ?> value='fixed_<?=$block?>'><?=$block?>: Fixierter Turnierblock</option>
                        <?php endforeach; ?>
                        <!-- LA: Abschlussturnier -->
                        <option value='final'>Abschlussturnier</option>
                    <?php endif; ?>
                    <!-- Spaßturnier -->
                    <option <?php if (($_POST['block'] ?? '') == 'spass') {?> selected <?php } ?> value="spass">Spaßturnier</option>
                </select>
            </p>
        </div>

        <div id="immediately_open_div">
            <p>
                <label class="w3-text-primary" for="sofort_oeffnen">Automatische Erweiterung des Turniers<span class="w3-text-secondary">*</span></label>
                <select required class="w3-select w3-border w3-border-primary w3-padding w3-white" id="sofort_oeffnen" name="sofort_oeffnen">
                    <option value="" disabled selected>Wähle eine Option</option>
                    <option <?php if (($_POST['sofort_oeffnen'] ?? '') == 'higher') {?> selected <?php } ?> value="higher">Automatisch einen Block nach oben erweitern</option>
                    <option <?php if (($_POST['sofort_oeffnen'] ?? '') == 'lower') {?> selected <?php } ?> value="lower">Automatisch einen Block nach unten erweitern</option>
                    <option <?php if (($_POST['sofort_oeffnen'] ?? '') == 'none') {?> selected <?php } ?> value="none">Keine automatische Erweiterung vornehmen</option>
                </select>
            </p>
        </div>

        <div id="min_number_of_teams_div">
            <p>
                <label class="w3-text-primary" for="min_teams"><b>Minimale Anzahl</b> an Teams<span class="w3-text-secondary">*</span></label>
                <select required class="w3-select w3-border w3-border-primary w3-padding w3-white" id="min_teams" name="min_teams">
                    <option value="" disabled selected>Wähle eine Minimalanzahl</option>
                    <option <?php if (($_POST['min_teams'] ?? '') == '4') {?> selected <?php } ?> value="4">4 Teams</option>
                    <option <?php if (($_POST['min_teams'] ?? '') == '5') {?> selected <?php } ?> value="5">5 Teams</option>
                </select>
            </p>
        </div>

        <div id="number_of_teams_div">
            <p>
                <label class="w3-text-primary" for="plaetze"><b>Maximale Anzahl</b> an Teams<span class="w3-text-secondary">*</span></label>
                <select required class="w3-select w3-border w3-border-primary w3-padding w3-white" id="plaetze" name="plaetze">
                    <option value="" disabled selected>Wähle eine Maximalanzahl</option>
                    <option <?php if (($_POST['plaetze'] ?? '') == '4') {?> selected <?php } ?> value="4">4 Teams</option>
                    <option <?php if (($_POST['plaetze'] ?? '') == '5') {?> selected <?php } ?> value="5">5 Teams</option>
                    <option <?php if (($_POST['plaetze'] ?? '') == '6') {?> selected <?php } ?> value="6">6 Teams</option>
                    <option <?php if (($_POST['plaetze'] ?? '') == '7') {?> selected <?php } ?> value="7">7 Teams</option>
                    <option <?php if (($_POST['plaetze'] ?? '') == '8') {?> selected <?php } ?> value="8">8 Teams</option>
                    <?php if (Helper::$ligacenter): ?>
                        <option <?php if (($_POST['plaetze'] ?? '') == '9') {?> selected <?php } ?> value="9">9 Teams</option>
                        <option <?php if (($_POST['plaetze'] ?? '') == '10') {?> selected <?php } ?> value="10">10 Teams</option>
                        <option <?php if (($_POST['plaetze'] ?? '') == '11') {?> selected <?php } ?> value="11">11 Teams</option>
                        <option <?php if (($_POST['plaetze'] ?? '') == '12') {?> selected <?php } ?> value="12">12 Teams</option>
                    <?php endif; ?>
                </select>
            </p>
        </div>

        <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
    </div>

    <!-- Anfahrt -->
    <div class="w3-card-4 w3-panel">
        <h3>Adresse</h3>
        <div class="w3-section">
            <label class="w3-text-primary" for="hallenname">Hallenname<span class="w3-text-secondary">*</span></label>
            <input required
                   type="text" class="w3-input w3-border w3-border-primary"
                   value="<?=$_POST['hallenname'] ?? ''?>"
                   id="hallenname"
                   name="hallenname"
                   list="list_hallenname"
                   onchange="onchange_fill_address(this)"
            >
            <?=Html::datalist_turnier("hallenname")?>
            <i class="w3-text-grey">Die Adresse wird automatisch ausgefüllt, falls der Hallenname bereits verwendet wurde.</i>
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="strasse">Straße und Hausnummer<span class="w3-text-secondary">*</span></label>
            <input required type="text"
                   class="w3-input w3-border w3-border-primary"
                   value="<?=$_POST['strasse'] ?? ''?>" id="strasse"
                   name="strasse" list="list_strasse">
            <?=Html::datalist_turnier("strasse")?>
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="plz">PLZ<span class="w3-text-secondary">*</span></label>
            <input required type="text" maxlength="5" class="w3-input w3-border w3-border-primary"
                   value="<?=$_POST['plz'] ?? ''?>" id="plz" name="plz" list="list_plz">
            <?=Html::datalist_turnier("plz")?>
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="ort">Ort<span class="w3-text-secondary">*</span></label>
            <input required type="text" class="w3-input w3-border w3-border-primary"
                   value="<?=$_POST['ort'] ?? ''?>" id="ort" name="ort" list="list_ort">
            <?=Html::datalist_turnier("ort")?>
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="haltestellen">Haltestellen</label>
            <input type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['haltestellen'] ?? ''?>"
                   id="haltestellen" name="haltestellen" list="list_haltestellen">
            <?=Html::datalist_turnier("haltestellen")?>
        </div>
        <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
    </div>

    <!-- Turnierdetails -->
    <div class="w3-panel w3-card-4">
        <h3>Turnierdetails</h3>
        <p>
            <label class="w3-text-primary" for="text">Weitere Informationen</label>
            <textarea class="w3-input w3-border w3-border-primary" onkeyup="woerter_zaehlen(1500);" maxlength="1500"
                      rows="4" id="text" name="hinweis"
                      ><?=stripcslashes($_POST['hinweis'] ?? '')?></textarea>
        <p id="counter"><p>
        </p>
        <p>
            <label class="w3-text-primary" for="tname">Turniername</label>
            <input type="text" maxlength="60" value="<?=$_POST['tname'] ?? ''?>" class="w3-input w3-border w3-border-primary" id="tname" name="tname">
        </p>
        <p>
            <label class="w3-text-primary" for="startgebuehr">Startgebühr<span class="w3-text-secondary">*</span></label>
            <?php if (Helper::$ligacenter) {?>
                <input type="text" class="w3-input w3-border w3-border-primary"
                       placeholder="z. B. 5 Euro" id="startgebuehr" name="startgebuehr">
            <?php } else { ?>
                <select class="w3-input w3-border w3-border-primary w3-padding w3-white" id="startgebuehr" name="startgebuehr">
                    <option value="" disabled selected>Wähle eine Option</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == 'keine') {?>selected<?php }?> value="keine">keine</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '5 Euro') {?>selected<?php }?> value="5 Euro">5 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '6 Euro') {?>selected<?php }?> value="6 Euro">6 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '7 Euro') {?>selected<?php }?> value="7 Euro">7 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '8 Euro') {?>selected<?php }?> value="8 Euro">8 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '9 Euro') {?>selected<?php }?> value="9 Euro">9 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '10 Euro') {?>selected<?php }?> value="10 Euro">10 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '11 Euro') {?>selected<?php }?> value="11 Euro">11 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '12 Euro') {?>selected<?php }?> value="12 Euro">12 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '13 Euro') {?>selected<?php }?> value="13 Euro">13 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '14 Euro') {?>selected<?php }?> value="14 Euro">14 Euro</option>
                    <option <?php if (($_POST['startgebuehr'] ?? '') == '15 Euro') {?>selected<?php }?> value="15 Euro">15 Euro</option>
                </select>
            <?php } //end if?>
        </p>
        <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
    </div>

    <!-- Organisator -->
    <div class="w3-panel w3-card-4">
        <h3>Organisator</h3>
        <p>
            <label class="w3-text-primary" for="organisator">Name<span class="w3-text-secondary">*</span></label>
            <input required value="<?=$_POST['organisator'] ?? ''?>" type="text"
                   class="w3-input w3-border w3-border-primary" id="organisator"
                   name="organisator" list="list_organisator" onchange="onchange_fill_handy(this)">
            <?=Html::datalist_turnier_ausrichter("organisator", $ausrichter_team_id)?>
        </p>
        <p>
            <label class="w3-text-primary" for="handy">Handynummer<span class="w3-text-secondary">*</span></label>
            <input required value="<?=$_POST['handy'] ?? ''?>" type="text"
                   class="w3-input w3-border w3-border-primary" id="handy"
                   name="handy" list="list_handy">
            <?=Html::datalist_turnier_ausrichter("handy", $ausrichter_team_id)?>
        </p>
        <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
            <p>Das Handy muss während des Turniertages erreichbar sein.</p>
        </div>
        <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
    </div>

    <!-- Submit -->
    <div class="w3-panel w3-card-4">
        <p>
            <input type="submit" value="Turnier eintragen" name="create_turnier" class="w3-secondary w3-button w3-block">
        </p>
    </div>
</form>

<script>

/* 
    Skript zum ein- und ausblenden von zusätzlichen Optionen je nach gewählter Turnierart.
    Bei übermittlung des fixierten Blockes ist eine zusätzliche Überprüfung notwendig, ob man als Ligaausschuss eingeloggt ist
*/

// Automatisches Ausfüllen der Adresse bei Auswahl der Halle
const turnier_array = <?= Html::turnier_adressen_javascript_array() ?>;
function onchange_fill_address(selectObject) {
    if (selectObject.value && turnier_array[selectObject.value] !== undefined) {
        document.getElementById("strasse").value = turnier_array[selectObject.value]["strasse"];
        document.getElementById("plz").value = turnier_array[selectObject.value]["plz"];
        document.getElementById("ort").value = turnier_array[selectObject.value]["ort"];
        document.getElementById("haltestellen").value = turnier_array[selectObject.value]["haltestellen"];
    }
}

// Automatisches Ausfüllen der Handynummer bei Auswahl des Organisators
const organisator_array = <?= Html::turnier_organisator_javascript_array($ausrichter_team_id) ?>;
function onchange_fill_handy(selectObject) {
    if (selectObject.value && organisator_array[selectObject.value] !== undefined) {
        document.getElementById("handy").value = organisator_array[selectObject.value]["handy"];
    }
}

</script>