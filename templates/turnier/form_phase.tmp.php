<?php

use App\Turnier\Enum\TurnierPhase;

/** @var TurnierPhase|null $phase */
?>
<div class="w3-panel w3-card-4">
    <h3 class="w3-text-secondary">Turnierphase</h3>

    <div id="phase_div">
        <p>
            <label class="w3-text-primary" for="phase">Turnierphase<span class="w3-text-secondary">*</span></label>
            <select required class="w3-select w3-border w3-border-primary w3-padding" id="phase" name="phase">

                <?php foreach (TurnierPhase::cases() as $option) { ?>
                    <option <?php if (($phase ?? null) === $option) {?> selected <?php } ?> value="<?= $option->value ?>"><?= $option->label() ?></option>
                <?php } //end foreach?>
            </select>
        </p>
    </div>

    <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
</div>
