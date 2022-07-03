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
        <p>
            <label class="w3-text-primary" for="datum">Datum</label>
            <input required type="date" value="<?=$_POST['datum'] ?? date("Y-m-d", (time()+4*7*24*60*60))?>" class="w3-input w3-border w3-border-primary" style="max-width: 320px" id="datum" name="datum">
            <i class="w3-text-grey"> Ligaturniere müssen spätestens vier Wochen vor dem Spieltag eingetragen werden<br>nur Samstage, Sonntage und bundesweite Feiertage<br>Saison: <?=Config::SAISON_ANFANG;?> - <?=Config::SAISON_ENDE;?></i>
        </p>
        <p>
            <label class="w3-text-primary" for="startzeit">Startzeit</label>
            <input required type="time" class="w3-input w3-border w3-border-primary" value="<?=$_POST['startzeit'] ?? '10:00'?>" style="max-width: 320px" id="startzeit" name="startzeit">
            <i class="w3-text-grey">Ligaturniere müssen zwischen 9:00&nbsp;Uhr und 20:00&nbsp;Uhr stattfinden</i>
        </p>
        <p>
            <input class="w3-check" type="checkbox" id="besprechung" name="besprechung" <?php if(($_POST['besprechung'] ?? '') == "Ja"){?> checked <?php }//endif?> value="Ja">
            <label for="besprechung" class="w3-hover-text-secondary w3-text-primary" style="cursor: pointer"> Gemeinsame Besprechung aller Teams 15 min vor Turnierbeginn</label>
        </p>
    </div>

    <!-- Modusspezifisch -->
    <div class="w3-panel w3-card-4">
        <h3 id="result">Liga</h3>
        <p>
            <label class="w3-text-primary" for="art">Turnierart</label>
            <select required class="w3-select w3-border w3-border-primary" id="art" name="art" onchange="onchange_show_block(this)">
                <option <?php if (($_POST['art'] ?? '') == 'I'){?> selected <?php } ?> value="I">I: Blockeigenes Turnier <?= BlockService::toString($ausrichter_block)?></option>
                <option <?php if (($_POST['art'] ?? '') == 'II'){?> selected <?php } ?> value="II">II: Blockhöheres Turnier <?=$block_higher_str?></option>
                <option <?php if (($_POST['art'] ?? '') == 'spass'){?> selected <?php } ?> value="spass">Spaßturnier (außerhalb der Liga, Anmeldung beim Ausrichter, Datum und Uhrzeit beliebig)</option>
                <?php if (Helper::$ligacenter){?>
                    <option <?php if (($_POST['art'] ?? '') == 'final'){?> selected <?php } ?> value='final'>Abschlussturnier</option>
                    <option <?php if (($_POST['art'] ?? '') == 'fixed'){?> selected <?php } ?> value='fixed'>Fixierter Turnierblock (<?=implode(", ", Config::BLOCK)?>)</option>
                <?php } //endif?>
            </select>
        </p>
        <p>
            <input class="w3-check" type="checkbox" id="sofort_oeffnen" name="sofort_oeffnen" <?php if(($_POST['sofort_oeffnen'] ?? '') == "Ja"){?> checked <?php }//endif?> value="Ja">
            <label for="sofort_oeffnen" class="w3-hover-text-secondary w3-text-primary" style="cursor: pointer"> Das Turnier soll beim Übergang von Wartephase auf Setzphase sofort auf ABCDEF geöffnet werden.</label>
            <br>
            <span class="w3-text-grey">Entspricht dem früheren blockfreien Turnier und wird auf der Webseite auch so angezeigt.</span>
        </p>
        <div id="block_higher_div" style="display: none">
            <p><label class="w3-text-primary" for="block">Höheren Turnierblock wählen</label>
            <select required class="w3-select w3-border w3-border-primary" id="block" name="block">
                <?php foreach ($block_higher as $block){?>
                    <option
                        <?php if (($_POST['block'] ?? '') === $block): ?>
                            selected
                        <?php endif; ?>
                            value='<?=$block?>'> <?=$block?>
                    </option>
                <?php } //end foreach?>
            </select>
        </div>

        <?php if (Helper::$ligacenter){?>
            <div id="block_fixed_div" style="display: none">
                <p>
                <label class="w3-text-primary" for="block_fixed">Fixierter Turnierblock</label>
                <select class="w3-input w3-border w3-border-primary" id="block_fixed" name="block">
                    <?php foreach (Config::BLOCK as $block_fixed) {?>
                    <option <?php if (($_POST['block'] ?? '') == $block_fixed){?> selected <?php } //endif?> value='<?=$block_fixed?>'><?=$block_fixed?></option>
                    <?php } //end foreach?>
                </select><i class="w3-small w3-text-grey">Fixierte Turnierblöcke verändern sich nicht mehr</i>
                </p>
            </div>
        <?php } //endif?>

        <p>
            <label class="w3-text-primary" for="plaetze">Plätze</label>
            <select required class="w3-select w3-border w3-border-primary" id="plaetze" name="plaetze">
                <option <?php if (($_POST['plaetze'] ?? '') == '4'){?> selected <?php } //endif?> <?php if(Helper::$teamcenter){?> disabled <?php } //end if?> value="4">4 Teams (nur in Absprache mit dem Ligaausschuss)</option>
                <option <?php if (($_POST['plaetze'] ?? '') == '5'){?> selected <?php } //endif?> value="5">5 Teams</option>
                <option <?php if (($_POST['plaetze'] ?? '') == '6'){?> selected <?php } //endif?> value="6">6 Teams</option>
                <option <?php if (($_POST['plaetze'] ?? '') == '7'){?> selected <?php } //endif?> value="7">7 Teams</option>
                <option <?php if (($_POST['plaetze'] ?? '') == '8'){?> selected <?php } //endif?> value="8">8 Teams</option>
                <?php if(Helper::$ligacenter): ?>
                    <option <?php if (($_POST['plaetze'] ?? '') == '9'){?> selected <?php } //endif?> value="9">9 Teams</option>
                    <option <?php if (($_POST['plaetze'] ?? '') == '10'){?> selected <?php } //endif?> value="10">10 Teams</option>
                    <option <?php if (($_POST['plaetze'] ?? '') == '11'){?> selected <?php } //endif?> value="11">11 Teams</option>
                    <option <?php if (($_POST['plaetze'] ?? '') == '12'){?> selected <?php } //endif?> value="12">12 Teams</option>
                <?php endif; ?>
            </select>
        </p>
    </div>

    <!-- Anfahrt -->
    <div class="w3-card-4 w3-panel">
        <h3>Adresse</h3>
        <div class="w3-section">
            <label class="w3-text-primary" for="hallenname">Hallenname</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['hallenname'] ?? ''?>" id="hallenname" name="hallenname">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="strasse">Straße und Hausnummer</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['strasse'] ?? ''?>" id="strasse" name="strasse">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="plz">PLZ</label>
            <input required type="text" maxlength="5" class="w3-input w3-border w3-border-primary" value="<?=$_POST['plz'] ?? ''?>" id="plz" name="plz">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="ort">Ort</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['ort'] ?? ''?>" id="ort" name="ort">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="haltestellen">Haltestellen <i>(optional)</i></label>
            <input type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['haltestellen'] ?? ''?>" id="haltestellen" name="haltestellen">
            <i class="w3-text-grey">Für die Anfahrt mit öffentlichen Verkehrsmitteln</i>
        </div>
    </div>

    <!-- Turnierdetails -->
    <div class="w3-panel w3-card-4">
        <h3>Turnierdetails</h3>
        <p>
            <label class="w3-text-primary" for="text">Hinweistext</label>
            <!--<input type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['hinweis'] ?? '';?>" id="hinweis" name="hinweis">-->
            <textarea class="w3-input w3-border w3-border-primary" onkeyup="woerter_zaehlen(1500);" maxlength="1500" rows="4" id="text" name="hinweis" required><?=stripcslashes($_POST['hinweis'] ?? '')?></textarea>
            <p id="counter"><p>
        </p>
        <p>
            <label class="w3-text-primary" for="tname">Turniername <i>(optional)</i></label>
            <input type="text" maxlength="60" value="<?=$_POST['tname'] ?? '';?>" class="w3-input w3-border w3-border-primary" id="tname" name="tname">
        </p>
        <p>
            <label class="w3-text-primary" for="startgebuehr">Startgebühr</label>
            <?php if(Helper::$ligacenter){?>
                <input type="text" class="w3-input w3-border w3-border-primary" placeholder="z. B. 5 Euro" id="startgebuehr" name="startgebuehr">
            <?php }else{ ?>
                <select class="w3-input w3-border w3-border-primary" id="startgebuehr" name="startgebuehr">
                    <option <?php if(($_POST['startgebuehr'] ?? '') == 'keine'){?>selected<?php }?> value="keine">keine</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '5 Euro'){?>selected<?php }?> value="5 Euro">5 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '6 Euro'){?>selected<?php }?> value="6 Euro">6 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '7 Euro'){?>selected<?php }?> value="7 Euro">7 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '8 Euro'){?>selected<?php }?> value="8 Euro">8 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '9 Euro'){?>selected<?php }?> value="9 Euro">9 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '10 Euro'){?>selected<?php }?> value="10 Euro">10 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '11 Euro'){?>selected<?php }?> value="11 Euro">11 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '12 Euro'){?>selected<?php }?> value="12 Euro">12 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '13 Euro'){?>selected<?php }?> value="13 Euro">13 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '14 Euro'){?>selected<?php }?> value="14 Euro">14 Euro</option>
                    <option <?php if(($_POST['startgebuehr'] ?? '') == '15 Euro'){?>selected<?php }?> value="15 Euro">15 Euro</option>
                </select>
            <?php } //end if?>
        </p>
    </div>

    <!-- Organisator -->
    <div class="w3-panel w3-card-4">
        <h3>Organisator</h3>
        <p>
            <label class="w3-text-primary" for="organisator">Name</label>
            <input required value="<?=$_POST['organisator'] ?? ''?>" type="text" class="w3-input w3-border w3-border-primary" id="organisator" name="organisator">
        </p>
        <p>
            <label class="w3-text-primary" for="handy">Handynummer</label>
            <input required value="<?=$_POST['handy'] ?? ''?>" type="text" class="w3-input w3-border w3-border-primary" id="handy" name="handy">
            <i class="w3-text-grey">Das Handy muss während des Turniertages erreichbar sein</i>
        </p>
    </div>

    <!-- Submit -->
    <div class="w3-panel w3-card-4">
        <p>
            <input type="submit" value="Turnier eintragen" name="create_turnier" class="w3-secondary w3-button w3-block">
        </p>
    </div>
</form>

<script>
/* Skript zum ein- und ausblenden von zusätzlichen Optionen je nach gewählter Turnierart.
Bei übermittlung des fixierten Blockes ist eine zusätzliche Überprüfung notwendig, ob man als Ligaausschuss eingeloggt ist*/

function onstart_show_block(){
    
    var e = document.getElementById("art");
    var result = e.options[e.selectedIndex].value;

    /* Einblenden der Auswahl fixierten Turnierblocks */
    <?php if(Helper::$ligacenter){?>
    if (result === "fixed"){
        document.getElementById("block_fixed_div").style.display = "block";
    }
    <?php } //endif?>
    
    /* Einblenden der Auswahl Abschlussturniere */
    <?php if(Helper::$ligacenter){?>
    if (result === "final"){
        document.getElementById("block_final_div").style.display = "block";
    }
    <?php } //endif?>

    /* Einblenden der Auswahl des höheren Turnierblocks */
    if (result === "II"){
        document.getElementById("block_higher_div").style.display = "block";
    }
}

onstart_show_block();

function onchange_show_block(selectObject) {
    
    /* Einblenden der Auswahl des fixierten Turnierblocks */
    <?php if(Helper::$ligacenter){?>
        if (selectObject.value ===  "fixed") {
            document.getElementById("block_fixed_div").style.display = "block";
        }else{
            document.getElementById("block_fixed_div").style.display = "none";
        }

        /* Einblenden der Auswahl für das Abschlussturnier */
        if (selectObject.value ===  "final") {
            document.getElementById("block_final_div").style.display = "block";
        }else{
            document.getElementById("block_final_div").style.display = "none";
        }
    <?php } //endif?>

    /* Einblenden der Auswahl des höheren Turnierblocks */
    if (selectObject.value ===  "II") {
        document.getElementById("block_higher_div").style.display = "block";
        }else{
        document.getElementById("block_higher_div").style.display = "none";
        }
}
</script>