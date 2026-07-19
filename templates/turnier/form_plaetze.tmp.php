<?php use App\Service\Turnier\TurnierFormService;

?>

<div class="w3-panel w3-card-4">
    <h3 class="w3-text-secondary">Plätze</h3>

    <div id="min_number_of_teams_div">
        <p>
            <label class="w3-text-primary" for="min_teams"><b>Minimale Anzahl</b> an Teams<span class="w3-text-secondary">*</span></label>
            <select required <?php if (!TurnierFormService::isEditable('min_teams', $turnier)): ?> disabled <?php endif; ?> class="w3-select w3-border w3-border-primary w3-padding" id="min_teams" name="min_teams">
                <option value="" disabled selected>Wähle eine Minimalanzahl</option>
                <option <?php if (($min_teams ?? '') == '4') {?> selected <?php } ?> value="4">4 Teams</option>
                <option <?php if (($min_teams ?? '') == '5') {?> selected <?php } ?> value="5">5 Teams</option>
            </select>
        </p>
    </div>

    <div id="number_of_teams_div">
        <p>
            <label class="w3-text-primary" for="plaetze"><b>Maximale Anzahl</b> an Teams<span class="w3-text-secondary">*</span></label>
            <select required <?php if (!TurnierFormService::isEditable('plaetze', $turnier)): ?> disabled <?php endif; ?> class="w3-select w3-border w3-border-primary w3-padding" id="plaetze" name="plaetze">
                <option value="" disabled selected>Wähle eine Maximalanzahl</option>
                <option <?php if (($plaetze ?? '') == '4') {?> selected <?php } ?> value="4">4 Teams</option>
                <option <?php if (($plaetze ?? '') == '5') {?> selected <?php } ?> value="5">5 Teams</option>
                <option <?php if (($plaetze ?? '') == '6') {?> selected <?php } ?> value="6">6 Teams</option>
                <option <?php if (($plaetze ?? '') == '7') {?> selected <?php } ?> value="7">7 Teams</option>
                <option <?php if (($plaetze ?? '') == '8') {?> selected <?php } ?> value="8">8 Teams</option>
                <?php if (Helper::$ligacenter): ?>
                    <option <?php if (($plaetze ?? '') == '9') {?> selected <?php } ?> value="9">9 Teams</option>
                    <option <?php if (($plaetze ?? '') == '10') {?> selected <?php } ?> value="10">10 Teams</option>
                    <option <?php if (($plaetze ?? '') == '11') {?> selected <?php } ?> value="11">11 Teams</option>
                    <option <?php if (($plaetze ?? '') == '12') {?> selected <?php } ?> value="12">12 Teams</option>
                <?php endif; ?>
            </select>
        </p>
    </div>

    <p><span class="w3-text-secondary">*</span>&nbsp;Pflichtfeld</p>
</div>