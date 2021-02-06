<!-- Formular Turnier löschen -->
<form method="post" onsubmit="return confirm('Das Turnier in <?=$turnier->details['ort']?> am <?=$turnier->details['datum']?> (<?=$turnier->details['tblock']?>) mit der ID <?=$turnier->details['turnier_id']?> wird gelöscht werden.');">
    <div class="w3-panel w3-card-4">
        <h3>Turnier löschen <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
        <p>
        <p><b>Hinweis:</b> Nach dem Löschen ist eine Benachrichtung der Teams über das Kommunikationscenter nicht mehr möglich. Gelöschte Turniere können nicht wieder hergestellt werden - der Turnierlog mit allen notwendigen Daten bleibt jedoch unter "Turniere verwalten" einsehbar.</p>
            <input required type="checkbox" value="checked" class="w3-check" id="delete_turnier_check" name="delete_turnier_check">
            <label class="w3-text-primary w3-hover-text-secondary" style="cursor: pointer;" for="delete_turnier_check">Hinweis gelesen</label>
        </p>
        <p>
            <label for='delete_turnier_grund' class="w3-text-primary">Grund der Turnierabsage</label>
            <input required list="browsers" id="delete_turnier_grund" name="delete_turnier_grund" placeholder="Bitte eingeben.." class="w3-input w3-border w3-border-primary">
                <datalist id="browsers">
                    <option value="Zu wenig spielberechtigte Ligateams">
                    <option value="Vom Ausrichter im Vorfeld abgesagt">
                    <option value="Spaßturnier">
                    <option value="Corona-Pandemie">
                    <option value="Fehler beim Eintragen">
                    <option value="Dritter Weltkrieg">
                </datalist>
        </p>
        <p>
            <input type="submit" value="Turnier löschen" name="delete_turnier" class="w3-secondary w3-button w3-block">
        </p>
    </div>
</form>

<!-- Formular Ligaausschuss -->
<form method="post">
    <div class="w3-panel w3-card-4">
        <h3>Turnierdaten <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
        <p>
            <label for="ausrichter" class='w3-text-primary'>Ausrichter ändern</label><br>
            <input type="text" class="w3-input w3-border w3-border-primary" value="<?=$turnier->details['teamname']?>" list="teams" id="ausrichter" name="ausrichter">
                <?=Form::datalist_teams()?>
        </p>
        <p>
            <label class="w3-text-primary" for="tname">Turniername <i class="w3-small">(optional)</i></label>
            <input type="text" maxlength="25" value="<?=$turnier->details['tname'];?>" class="w3-input w3-border w3-border-primary" id="tname" name="tname">
        </p>
        <p>
            <label class="w3-text-primary" for="datum">Datum</label>
            <input required type="date" value="<?=$turnier->details['datum'];?>" class="w3-input w3-border w3-border-primary" style="max-width: 320px" id="datum" name="datum">
        </p>
        <h3>Ligalogik <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
        <p>
            <label class="w3-text-primary" for="phase">Phase</label>
            <select required type="date" value="<?=$turnier->details['phase'];?>" class="w3-input w3-border w3-border-primary" id="phase" name="phase">
                <option <?php if($turnier->details['phase'] == 'offen'){?> selected <?php }?> value="offen">Offene Phase</option>
                <option <?php if($turnier->details['phase'] == 'melde'){?> selected <?php }?>   value="melde">Meldephase</option>
                <option <?php if($turnier->details['phase'] == 'spielplan'){?> selected <?php }?> value="spielplan">Spielplan</option>
                <option <?php if($turnier->details['phase'] == 'ergebnis'){?> selected <?php }?> value="ergebnis">Ergebnis</option>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="art">Turnierart</label>
            <select required class="w3-select w3-border w3-border-primary" id="art" name="art">
                <option <?php if($turnier->details['art'] == 'I'){?> selected <?php }?> value="I">I: Blockeigenes Turnier</option>
                <option <?php if($turnier->details['art'] == 'II'){?> selected <?php }?> value="II">II: Blockhöheres Turnier</option>
                <option <?php if($turnier->details['art'] == 'III'){?> selected <?php }?> value="III">III: Blockfreies Turnier (ABCDEF)</option>
                <option <?php if($turnier->details['art'] == 'spass'){?> selected <?php }?> value="spass">Spaßturnier</option>
                <option <?php if($turnier->details['art'] == 'final'){?> selected <?php }?> value='final'>Abschlussturnier</option>
                <option <?php if($turnier->details['art'] == 'fixed'){?> selected <?php }?> value='fixed'>Manuelles (fixed) Turnier</option>
            </select>
        </p>
        <p>
        <label class="w3-text-primary" for="block">Turnierblock</label>
        <select required class="w3-select w3-border w3-border-primary" id="block" name="block">';
        <?php foreach (Config::BLOCK_ALL as $block){?>
            <option <?php if ($turnier->details['tblock'] == $block){?> selected <?php }?> value='<?=$block?>'><?=$block?></option>
        <?php } //end foreach?>
        </select>
        <i class="w3-text-primary">Nach ändern des Blockes sollten die Anmeldelisten kontrolliert werden.</i>
        </p>
        <p>
            <input type="submit" value="Turnierdaten ändern" name="turnier_bearbeiten_la" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>