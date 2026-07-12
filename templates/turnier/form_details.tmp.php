<?php use App\Service\Turnier\TurnierFormService;

?>

<div class="w3-panel w3-card-4">
    <h3 class="w3-text-secondary">Turnierdetails</h3>
    <p>
        <label class="w3-text-primary" for="text">Weitere Informationen</label>
        <textarea class="w3-input w3-border w3-border-primary" onkeyup="woerter_zaehlen(1500);" maxlength="1500"
                    rows="4" id="text" name="hinweis"
                    ><?=stripcslashes($turnier_hinweis ?? '')?></textarea>
    <p id="counter"><p>
    </p>
    <p>
        <label class="w3-text-primary" for="tname">Turniername</label>
        <input type="text" maxlength="60" value="<?=$turnier_name ?? ''?>" class="w3-input w3-border w3-border-primary" id="tname" name="tname">
    </p>
    <p>
        <label class="w3-text-primary" for="startgebuehr">Startgebühr<span class="w3-text-secondary">*</span></label>
        <select class="w3-input w3-border w3-border-primary w3-padding" <?php if (!TurnierFormService::isEditable('startgebuehr', $turnier)): ?> disabled <?php endif; ?> id="startgebuehr" name="startgebuehr" required>
            <option value="" disabled selected>Wähle eine Option</option>
            <option <?php if (($turnier_startgebuehr ?? '') == 'keine') {?>selected<?php }?> value="keine">keine</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '5 Euro') {?>selected<?php }?> value="5 Euro">5 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '6 Euro') {?>selected<?php }?> value="6 Euro">6 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '7 Euro') {?>selected<?php }?> value="7 Euro">7 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '8 Euro') {?>selected<?php }?> value="8 Euro">8 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '9 Euro') {?>selected<?php }?> value="9 Euro">9 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '10 Euro') {?>selected<?php }?> value="10 Euro">10 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '11 Euro') {?>selected<?php }?> value="11 Euro">11 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '12 Euro') {?>selected<?php }?> value="12 Euro">12 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '13 Euro') {?>selected<?php }?> value="13 Euro">13 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '14 Euro') {?>selected<?php }?> value="14 Euro">14 Euro</option>
            <option <?php if (($turnier_startgebuehr ?? '') == '15 Euro') {?>selected<?php }?> value="15 Euro">15 Euro</option>
        </select>
    </p>
    <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
</div>