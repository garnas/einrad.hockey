<h2 class="w3-text-secondary w3-large" style="margin: 0">Status der Abstimmung</h2>

<?php if (empty($abstimmung->get_team())): ?>
    <p class="w3-section" style="margin: 0">Es ist keine Stimme für euer Team hinterlegt.</p>
<?php else: ?>
    <p class="w3-section" style="margin: 0">Es ist eine Stimme für euer Team hinterlegt.</p>

    <?php if (empty($einsicht)): ?>
        <form method="post">
            <div class="w3-section">
                <label for="passwort_einsicht">Teamcenter-Passwort:</label>
                <input required type="password" name="passwort" id="passwort_einsicht" placeholder="Passwort eingeben" class="w3-input w3-border"> 
            </div>
            <button type="submit" name="stimme_einsehen" class="w3-primary w3-button">Status einsehen</button>
        </form>
    <?php else: ?>
        <p style="margin: 0">Die Stimme wird im Formular angezeigt.</p>
    <?php endif; ?>

<?php endif; ?>