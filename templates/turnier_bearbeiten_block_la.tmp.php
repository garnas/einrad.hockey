<!-- Formular Ligaausschuss -->
<form method="post">
    <div class="w3-panel w3-card-4">
        <h3>Ligalogik <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
        <p>
            <label class="w3-text-primary" for="phase">Phase</label>
            <select required type="date" class="w3-input w3-border w3-border-primary" id="phase" name="phase">
                <option <?php if ($turnier->getPhase() == 'warte') {?> selected <?php }?> value="warte">Wartephase</option>
                <option <?php if ($turnier->getPhase() == 'setz') {?> selected <?php }?> value="setz">Setzphase</option>
                <option <?php if ($turnier->getPhase() == 'spielplan') {?> selected <?php }?> value="spielplan">Spielplan</option>
                <option <?php if ($turnier->getPhase() == 'ergebnis') {?> selected <?php }?> value="ergebnis">Ergebnis</option>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="art">Turnierart</label>
            <select required class="w3-select w3-border w3-border-primary" id="art" name="art">
                <option <?php if ($turnier->getArt() == 'I') {?> selected <?php }?> value="I">I: Blockeigenes Turnier</option>
                <option <?php if ($turnier->getArt() == 'II') {?> selected <?php }?> value="II">II: Blockhöheres Turnier</option>
                <option <?php if ($turnier->getArt() == 'spass') {?> selected <?php }?> value="spass">Spaßturnier</option>
                <option <?php if ($turnier->getArt() == 'final') {?> selected <?php }?> value='final'>Abschlussturnier</option>
                <option <?php if ($turnier->getArt() == 'fixed') {?> selected <?php }?> value='fixed'>Manuelles (fixed) Turnier</option>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="block">Turnierblock</label>
            <select required class="w3-select w3-border w3-border-primary" id="block" name="block">
                <?php foreach (Config::BLOCK_ALL as $block): ?>
                    <option <?= ($turnier->getBlock() === $block) ? 'selected' : '' ?>> <?=$block?></option>
                <?php endforeach; ?>
            </select>
            <i class="w3-text-primary">Nach ändern des Blockes sollten die Anmeldelisten kontrolliert werden.</i>
        </p>
        <p>
            <input type="submit" value="Turnierdaten ändern" name="turnier_bearbeiten_la" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>