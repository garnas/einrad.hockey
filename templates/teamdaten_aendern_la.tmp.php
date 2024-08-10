<div class="w3-card-4 w3-responsive w3-panel">
    <form method='post'>
        <h3>Ligaausschuss</h3>
        <div class="w3-row-padding">
            <div class="w3-half">
                <p>
                    <label for="teamname" class="w3-text-primary">Teamname</label>
                    <input required
                           class='w3-input w3-border w3-border-primary'
                           type='text'
                           id='teamname'
                           name='teamname'
                           value='<?= $team->getName() ?>'
                    >
                </p>
            </div>
            <div class="w3-half">
                <p>
                    <label for="paswort" class="w3-text-primary">Passwort</label>
                    <input class='w3-input w3-border w3-border-primary'
                           type='text'
                           id='passwort'
                           name='passwort'
                           placeholder='Neues Passwort vergeben'>
                </p>
            </div>
            <div class="w3-half">
                <p>
                    <label for="freilos_grund" class="w3-text-primary">Neues Freilos hinzufügen</label>
                    <input class='w3-input w3-border w3-border-primary'
                           type='text'
                           id='freilos_grund'
                           name='freilos_grund'
                           placeholder='Grund für Erhalt'
                    >
                </p>
            </div>
        </div>
        <p>
            <button type='submit'
                    class='w3-button w3-secondary w3-block'
                    name="change_la"
            >
                <?= Html::icon('create') ?> Teamdaten ändern (nur LA)
            </button>
        </p>
    </form>
</div>
