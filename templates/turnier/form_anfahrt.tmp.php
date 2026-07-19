<div class="w3-card-4 w3-panel">
    <h3 class="w3-text-secondary">Adresse</h3>
    <div class="w3-section">
        <label class="w3-text-primary" for="hallenname">Hallenname<span class="w3-text-secondary">*</span></label>
        <input required
                type="text" class="w3-input w3-border w3-border-primary"
                value="<?=e($adresse_hallenname ?? '')?>"
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
                value="<?=e($adresse_strasse ?? '')?>" id="strasse"
                name="strasse" list="list_strasse">
        <?=Html::datalist_turnier("strasse")?>
    </div>
    <div class="w3-section">
        <label class="w3-text-primary" for="plz">PLZ<span class="w3-text-secondary">*</span></label>
        <input required type="text" maxlength="5" class="w3-input w3-border w3-border-primary"
                value="<?=e($adresse_plz ?? '')?>" id="plz" name="plz" list="list_plz">
        <?=Html::datalist_turnier("plz")?>
    </div>
    <div class="w3-section">
        <label class="w3-text-primary" for="ort">Ort<span class="w3-text-secondary">*</span></label>
        <input required type="text" class="w3-input w3-border w3-border-primary"
                value="<?=e($adresse_ort ?? '')?>" id="ort" name="ort" list="list_ort">
        <?=Html::datalist_turnier("ort")?>
    </div>
    <div class="w3-section">
        <label class="w3-text-primary" for="haltestellen">Haltestellen</label>
        <input type="text" class="w3-input w3-border w3-border-primary" value="<?=e($adresse_haltestellen ?? '')?>"
                id="haltestellen" name="haltestellen" list="list_haltestellen">
        <?=Html::datalist_turnier("haltestellen")?>
    </div>
    <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
</div>

<script>

    const turnier_array = <?= Html::turnier_adressen_javascript_array() ?>;
    function onchange_fill_address(selectObject) {
        if (selectObject.value && turnier_array[selectObject.value] !== undefined) {
            document.getElementById("strasse").value = turnier_array[selectObject.value]["strasse"];
            document.getElementById("plz").value = turnier_array[selectObject.value]["plz"];
            document.getElementById("ort").value = turnier_array[selectObject.value]["ort"];
            document.getElementById("haltestellen").value = turnier_array[selectObject.value]["haltestellen"];
        }
    }

</script>