<!-- Spielerauswahlfeld -->
<div class="w3-panel w3-card-4">
    <form method="post">
        <h3 class="w3-text-primary">
            <label for="spieler">Spieler wählen</label>
        </h3>
        <p>
            <input onchange="this.form.submit();"
                   type="text"
                   placeholder="Spieler eingeben"
                   style="max-width:400px"
                   class="w3-input w3-border w3-border-primary"
                   list="spielerliste"
                   id="spieler"
                   name="spieler_auswahl"
            >
            <datalist id="spielerliste">
                <?php foreach ($spielerAlle as $s){ ?>
                <option value='<?= $s->getSpielerId() . ' | ' . $s->getName() ?>'>
                    <?php } //end foreach ?>
            </datalist>
        </p>
        <p>
            <input type="submit" class="w3-button w3-tertiary" value="Spieler wählen">
        </p>
    </form>
</div>