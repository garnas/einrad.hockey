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
                           value='<?= $team->details['teamname'] ?>'
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
                    <label for="freilose" class="w3-text-primary">Freilose</label>
                    <input class='w3-input w3-border w3-border-primary'
                           type='number'
                           id='freilose'
                           name='freilose'
                           value='<?= $team->details['freilose'] ?>'
                    >
                </p>
            </div>
        </div>
        <p>
            <button type='submit'
                    class='w3-button w3-secondary w3-block'
                    name="change_la"
            >
                <i class="material-icons">create</i> Teamdaten aendern (nur LA)
            </button>
        </p>
    </form>
</div>
