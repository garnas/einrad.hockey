<div class="w3-panel w3-card-4">
    <h3 class="w3-text-secondary">Organisator</h3>
    <p>
        <label class="w3-text-primary" for="organisator">Name<span class="w3-text-secondary">*</span></label>
        <input required value="<?=$turnier_organisator ?? ''?>" type="text"
                class="w3-input w3-border w3-border-primary" id="organisator"
                name="organisator" list="list_organisator" onchange="onchange_fill_handy(this)">
        <?=Html::datalist_turnier_ausrichter("organisator", $ausrichter_team_id)?>
    </p>
    <p>
        <label class="w3-text-primary" for="handy">Handynummer<span class="w3-text-secondary">*</span></label>
        <input required value="<?=$turnier_handy ?? ''?>" type="text"
                class="w3-input w3-border w3-border-primary" id="handy"
                name="handy" list="list_handy">
        <?=Html::datalist_turnier_ausrichter("handy", $ausrichter_team_id)?>
    </p>
    <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
</div>

<script>

    // Automatisches Ausfüllen der Handynummer bei Auswahl des Organisators
    const organisator_array = <?= Html::turnier_organisator_javascript_array($ausrichter_team_id) ?>;
    function onchange_fill_handy(selectObject) {
        if (selectObject.value && organisator_array[selectObject.value] !== undefined) {
            document.getElementById("handy").value = organisator_array[selectObject.value]["handy"];
        }
    }

</script>