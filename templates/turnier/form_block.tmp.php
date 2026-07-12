<?php use App\Service\Turnier\TurnierFormService;

?>

<div class="w3-panel w3-card-4">
    <h3 id="result" class="w3-text-secondary">Turnierblock</h3>
    <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
        <p>
            Ein Turnier kann automatisch um einen Block nach oben oder unten erweitert werden.
            Beim Übergang in die Setzphase wird zuerst die passende Blockzugehörigkeit getestet und im Anschluss das Turnier erweitert. 
        </p>
        <p>
            Danach erfolgt eine weitere Prüfung. Eine automatische Erweiterung auf ein ABCDEF Turnier ist nicht möglich. 
            Diese muss manuell in den eigenen Turnieren unter "Turnier bearbeiten" erfolgen.
        </p>
        <p>
            Es gibt Fälle, in denen die automatische Erweiterung eingerichtet ist, aber nur durchgeführt wird. 
            Beispiel: Handelt es sich beim Übergang in die Setzphase um ein A- oder AB-Turnier, dann ist eine Erweiterung um den nächsten höheren Block nicht möglich.
        </p>
    </div>

    <div>
        <p>
            <label class="w3-text-primary" for="art">Turnierblock<span class="w3-text-secondary">*</span></label>
            <select required <?php if (!TurnierFormService::isEditable('art_block', $turnier)): ?> disabled <?php endif; ?> class="w3-select w3-border w3-border-primary w3-padding" id="art_block" name="art_block">
                <option value="" disabled selected>Wähle einen Turnierblock</option>
                <!-- Blockhöhere Turniere -->
                <?php foreach ($block_higher as $block): ?>
                    <option <?php if (($turnier_block ?? '') === $block): ?> selected <?php endif; ?> value='II_<?=$block?>'><?=$block?>: Blockhöheres Turnier (II)</option>
                <?php endforeach; ?>
                <!-- Blockeigenes Turnier -->
                <option <?php if (($turnier_block ?? '') === $ausrichter_block) {?> selected <?php } ?> value=I_<?= $ausrichter_block ?>><?= $ausrichter_block?>: Blockeigenes Turnier (I)</option>
                <?php if (Helper::$ligacenter): ?>
                    <!-- LA: Turnier mit fixiertem Block -->
                    <?php foreach (Config::BLOCK as $block): ?>
                        <option <?php if (($turnier_art_block ?? '') === $block): ?> selected <?php endif; ?> value='fixed_<?=$block?>'><?=$block?>: Fixierter Turnierblock</option>
                    <?php endforeach; ?>
                    <!-- LA: Abschlussturnier -->
                    <option value='final'>Abschlussturnier</option>
                <?php endif; ?>
                <!-- Spaßturnier -->
                <option <?php if (($turnier_block ?? '') == 'spass') {?> selected <?php } ?> value="spass">Spaßturnier</option>
            </select>
        </p>
    </div>

    <div id="immediately_open_div">
        <p>
            <label class="w3-text-primary" for="sofort_oeffnen">Automatische Erweiterung des Turniers<span class="w3-text-secondary">*</span></label>
            <select required <?php if (!TurnierFormService::isEditable('sofort_oeffnen', $turnier)): ?> disabled <?php endif; ?> class="w3-select w3-border w3-border-primary w3-padding" id="sofort_oeffnen" name="sofort_oeffnen">
                <option value="" disabled selected>Wähle eine Option</option>
                <option <?php if (($sofort_oeffnen ?? '') == 'higher') {?> selected <?php } ?> value="higher">Automatisch einen Block nach oben erweitern</option>
                <option <?php if (($sofort_oeffnen ?? '') == 'lower') {?> selected <?php } ?> value="lower">Automatisch einen Block nach unten erweitern</option>
                <option <?php if (($sofort_oeffnen ?? '') == 'none') {?> selected <?php } ?> value="none">Keine automatische Erweiterung vornehmen</option>
            </select>
        </p>
    </div>

    <p>
        <span class="w3-text-secondary">*</span>Pflichtfeld
    </p>

</div>