<form action="https://team.einrad.hockey/forms/gruppe-add_record.php" method="post">
    <div class="w3-panel w3-tertiary w3-card-4">
        <h3 class="w3-center">Neue Terminseite für Deinen Verein anlegen</h3>
    </div>

    <div class="w3-card-4 w3-panel">
        <h3>Gruppe</h3>
        <div class="w3-section">
            <label class="w3-text-primary" for="gruppenname">Gruppenname</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['gruppenname'] ?? ''?>" id="gruppenname" name="gruppenname">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="nameBot">Name vom Email-Bot</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['nameBot'] ?? ''?>" id="nameBot" name="nameBot">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="emailBot">Emailadresse vom Bot</label>
            <input required type="email" class="w3-input w3-border w3-border-primary" value="<?=$_POST['emailBot'] ?? ''?>" id="emailBot" name="emailBot">
            <i class="w3-text-grey">Absendeadresse bei automatisch versendeten Nachrichten</i>
        </div>
    </div>

    <div class="w3-card-4 w3-panel">
        <h3>Administrator</h3>
        <div class="w3-section">
            <label class="w3-text-primary" for="alias">Alias</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['alias'] ?? ''?>" id="alias" name="alias">
            <i class="w3-text-grey">Der Alias ist später für andere Gruppenmitglieder sichtbar</i>
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="vorname">Vorname</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['vorname'] ?? ''?>" id="vorname" name="vorname">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="nachname">Nachname</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$_POST['nachname'] ?? ''?>" id="nameBot" name="nachname">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="email">Emailadresse</label>
            <input required type="email" class="w3-input w3-border w3-border-primary" value="<?=$_POST['email'] ?? ''?>" id="email" name="email">
        </div>
    </div>


    <!-- Submit -->
    <div class="w3-panel w3-card-4">
        <p>
            <input type="submit" value="Gruppe anlegen" name="create_turnier" class="w3-secondary w3-button w3-block">
        </p>
    </div>
</form>
