<div class="w3-panel w3-card-4">
    <h3 class="w3-text-secondary">Turnierphase</h3>

    <div id="phase_div">
        <p>
            <label class="w3-text-primary" for="phase">Turnierphase<span class="w3-text-secondary">*</span></label>
            <select required class="w3-select w3-border w3-border-primary w3-padding" id="phase" name="phase">

                <option <?php if (($phase ?? '') == 'warte') {?> selected <?php } ?> value="warte">Wartephase</option>
                <option <?php if (($phase ?? '') == 'setz') {?> selected <?php } ?> value="setz">Setzphase</option>
                <option <?php if (($phase ?? '') == 'spielplan') {?> selected <?php } ?> value="spielplan">Spielplanphase</option>
                <option <?php if (($phase ?? '') == 'ergebnis') {?> selected <?php } ?> value="ergebnis">Ergebnisphase</option>
            </select>
        </p>
    </div>

    <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
</div>