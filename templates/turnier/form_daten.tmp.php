<?php use App\Service\Turnier\TurnierFormService; ?>

<div class="w3-panel w3-card-4">
    <h3 id="result" class="w3-text-secondary">Turnierdaten</h3>
    <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
        <p>
            Ligaturniere müssen spätestens vier Wochen vor dem Spieltag eingetragen werden und dürfen nur an einem Samstag, Sonntag oder bundeseinheitlichen Feiertag stattfinden.
            Sie können frühstens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr (nach Spielplan) enden.
        </p>
    </div>

    <p>
        <label class="w3-text-primary" for="datum">Datum<span class="w3-text-secondary">*</span></label>
        <input required <?php if (!TurnierFormService::isEditable('datum', $turnier)): ?> disabled <?php endif; ?> type="date" value="<?= $turnier_datum ?? date("Y-m-d", (time() + 4 * 7 * 24 * 60 * 60))?>" class="w3-input w3-border w3-border-primary" style="max-width: 320px" id="datum" name="datum">
    </p>
    
    <?php if (Helper::$ligacenter): ?>
        <p>
            <label class="w3-text-primary" for="datum_bis">Bis-Datum</label>
            <input type="date"
                    value="<?= $turnier_datum_bis ?? "" ?>"
                    class="w3-input w3-border w3-border-primary" style="max-width: 320px" id="datum_bis" name="datum_bis">
        </p>
    <?php endif; ?>
    
    <p>
        <label class="w3-text-primary" for="startzeit">Startzeit<span class="w3-text-secondary">*</span></label>
        <input required type="time" <?php if (!TurnierFormService::isEditable('startzeit', $turnier)): ?> disabled <?php endif; ?> class="w3-input w3-border w3-border-primary" value="<?= $turnier_startzeit ?? '10:00' ?>" style="max-width: 320px" id="startzeit" name="startzeit">
    </p>
    <p>
        <input <?php if (!TurnierFormService::isEditable('besprechung', $turnier)): ?> disabled <?php endif; ?> class="w3-check" type="checkbox" id="besprechung" name="besprechung" <?php if (($besprechung ?? '') == "Ja") {?>checked<?php } ?> value="Ja">
        <label for="besprechung" class="w3-hover-text-secondary w3-text-primary" style="cursor: pointer">Gemeinsame Besprechung aller Teams 15 min vor Turnierbeginn</label>
    </p>
    <p><span class="w3-text-secondary">*</span>Pflichtfeld</p>
</div>