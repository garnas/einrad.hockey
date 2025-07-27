<form method="post">
    <div class="flex-container w3-section" style="justify-content: space-around;">
        <div class="flex-item" style="flex-flow: column; width: 30%;">
            <img src="<?= Env::BASE_URL ?><?= Abstimmung::LOGO1 ?>" class="w3-image w3-border-bottom">
            <div class="w3-padding">
                <input type="radio" name="logo" id="logo1" value="logo1" <?= Abstimmung::selected(name: "logo", value: "logo1", value_chosen: $einsicht["logo"] ?? null) ?> required>
                <label for="logo1">Logo 1</label>
            </div>
        </div>
        <div class="flex-item"  style="flex-flow: column; width: 30%;">
            <img src="<?= Env::BASE_URL ?><?= Abstimmung::LOGO2 ?>" class="w3-image w3-border-bottom">
            <div class="w3-padding">
                <input type="radio" name="logo" id="logo2" value="logo2" <?= Abstimmung::selected(name: "logo", value: "logo2", value_chosen: $einsicht["logo"] ?? null) ?> required>
                <label for="logo2">Logo 2</label>
            </div>
        </div>
    </div>

    <div class="w3-section">
        <label for="comment">Kommentar (optional):</label>
        <textarea id="comment" name="comment" rows="4" cols="50" maxlength="500" class="w3-input w3-border"><?=$einsicht['comment'] ?? ""?></textarea>
    </div>

    <div class="w3-section">
        <label for="passwort">Teamcenter-Passwort:</label>
        <input class="w3-input w3-border" type="password" name="passwort" id="passwort" placeholder="Passwort eingeben" required>
    </div>
    
    <button class="w3-button w3-primary" name="abgestimmt" type="submit">Abstimmen</button>
</form>