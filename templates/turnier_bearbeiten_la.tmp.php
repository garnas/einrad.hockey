<!-- Formular Ligaausschuss -->
<form method="post">
    <div class="w3-panel w3-card-4">
        <h3 id="result">Turnierdaten</h3>
        <p>
            <label for="ausrichter" class='w3-text-primary'>Ausrichter ändern</label><br>
            <input type="text" class="w3-input w3-border w3-border-primary" value="<?=$daten['teamname']?>" list="teams" id="ausrichter" name="ausrichter">
                <?=Form::datalist_teams()?>
        </p>
        <p>
            <label class="w3-text-primary" for="tname">Turniername <i class="w3-small">(optional)</i></label>
            <input type="text" maxlength="25" value="<?=$daten['tname'];?>" class="w3-input w3-border w3-border-primary" id="tname" name="tname">
        </p>
        <p>
            <label class="w3-text-primary" for="datum">Datum</label>
            <input required type="date" value="<?=$daten['datum'];?>" class="w3-input w3-border w3-border-primary" style="max-width: 320px" id="datum" name="datum">
        </p>
    </div>
    <div class="w3-panel w3-card-4">
        <h3 id="result">Liga</h3>
        <p>
            <label class="w3-text-primary" for="phase">Phase</label>
            <select required type="date" value="<?=$daten['phase'];?>" class="w3-input w3-border w3-border-primary" id="phase" name="phase">
                <option <?php if($daten['phase'] == 'offen'){?> selected <?php }?> value="offen">Offene Phase</option>
                <option <?php if($daten['phase'] == 'melde'){?> selected <?php }?>   value="melde">Meldephase</option>
                <option <?php if($daten['phase'] == 'spielplan'){?> selected <?php }?> value="spielplan">Spielplan</option>
                <option <?php if($daten['phase'] == 'ergebnis'){?> selected <?php }?> value="ergebnis">Ergebnis</option>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="art">Turnierart</label>
            <select required class="w3-select w3-border w3-border-primary" id="art" name="art">
                <option <?php if($daten['art'] == 'I'){?> selected <?php }?> value="I">I: Blockeigenes Turnier</option>
                <option <?php if($daten['art'] == 'II'){?> selected <?php }?> value="II">II: Blockhöheres Turnier</option>
                <option <?php if($daten['art'] == 'III'){?> selected <?php }?> value="III">III: Blockfreies Turnier (ABCDEF)</option>
                <option <?php if($daten['art'] == 'spass'){?> selected <?php }?> value="spass">Spaßturnier</option>
                <option <?php if($daten['art'] == 'final'){?> selected <?php }?> value='final'>Abschlussturnier</option>
                <option <?php if($daten['art'] == 'fixed'){?> selected <?php }?> value='fixed'>Manuelles (fixed) Turnier</option>
            </select>
        </p>
        <p>
        <label class="w3-text-primary" for="block">Turnierblock</label>
        <select required class="w3-select w3-border w3-border-primary" id="block" name="block">';
        <?php foreach (Config::BLOCK_ALL as $block){?>
            <option <?php if ($daten['tblock'] == $block){?> selected <?php }?> value='<?=$block?>'><?=$block?></option>
        <?php } //end foreach?>
        </select>
        <i class="w3-text-primary">Nach ändern des Blockes sollten die Anmeldelisten kontrolliert werden.</i>
        </p>
    </div>
    <div class="w3-panel w3-card-4">
        <p>
            <input type="submit" value="Ligaausschuss - Turnierdaten ändern" name="turnier_bearbeiten_la" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>

<!-- Formular Turnier löschen -->
<form method="post" onsubmit="return confirm('Das Turnier in <?=$daten['ort']?> am <?=$daten['datum']?> (<?=$daten['tblock']?>) mit der ID <?=$daten['turnier_id']?> wird gelöscht werden.');">
    <div class="w3-panel w3-card-4">
        <p>
            <input type="submit" value="Turnier löschen" name="delete_turnier" class="w3-secondary w3-button w3-block">
        </p>
    </div>
</form>