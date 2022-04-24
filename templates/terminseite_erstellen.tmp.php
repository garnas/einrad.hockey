<form action="<?= Nav::LINK_TERMINPLANER ?>/forms/gruppe-add_record.php" method="post">
    <div class="w3-panel w3-tertiary w3-card-4">
        <h3 class="w3-center">Neue Terminseite für Deinen Verein anlegen</h3>
    </div>

    <div class="w3-panel w3-card-4">
        <p>Hier kannst Du eine neue Terminseite für Deinen Verein anlegen.
            Unter <?= Html::link(Nav::LINK_TERMINPLANER, extern: true) ?>
            können sich die Mitspieler dann anmelden und für Termine eintragen.
            So könnt Ihr leicht den Überblick behalten wer zu welchem Termin kommt oder bereits abgesagt hat.
        </p>
        <p>
            Trainingszeiten werden nur einmalig angelegt und wiederholen sich wöchentlich.<br>
            Daten der Ligaturniere können einfach übernommen werden und es ist den Spielern ersichtlich, ob Ihr zum
            Turnier gemeldet seid und ob Ihr spielt oder auf der Warteliste steht.<br>
            Auch ist es möglich Eure Termine über CalDAV in verschiedene Clients (z.B. Thunderbird, Outlook) oder
            Android und iOS einzubinden.
        </p>
        <p>
            Es ist möglich und sinnvoll mehrere Teams für einen Verein anzulegen (Nur 1 Account hier anlegen). So könnt
            Ihr auch Spielern ermöglichen bei Turnieren auszuhelfen oder ein gemeinsames Training für mehrere Teams
            organsiseren.
        </p>
        <p>
            Die Erstellung der Spieleraccounts, Teams etc. ist unabhängig von der Teamkaderverwaltung auf der Ligaseite.
            Bei Fragen kontaktiere <?= Html::mailto('technik@einrad.hockey') ?>.
        </p>
    </div>

    <?php if ($team->check_terminplaner()) { ?>
        <div class="w3-card-4 w3-panel">
            Für Dein Team wurde mit der Emailadresse bereits eine Gruppe angelegt.
            <br>
            Klicke <?= Html::link(Nav::LINK_TERMINPLANER) ?>, um auf deine Seite zu gelangen.
            <br><br>
            Bei Fragen kannst du dich bei <?= Html::mailto(Env::TECHNIKMAIL) ?> melden.
        </div>
    <?php } else { ?>
        <div class="w3-card-4 w3-panel">
            <h3>Gruppe</h3>
            <div class="w3-section">
                <label class="w3-text-primary" for="gruppenname">Gruppen- / Vereinsname</label>
                <input required
                       type="text"
                       class="w3-input w3-border w3-border-primary"
                       value="<?= $team->details['verein'] ?>"
                       id="gruppenname"
                       name="gruppenname">
            </div>
        </div>

        <div class="w3-card-4 w3-panel">
            <h3>Administrator</h3>
            <div class="w3-section">
                <label class="w3-text-primary" for="alias">Alias</label>
                <input required type="text"
                       class="w3-input w3-border w3-border-primary"
                       value=""
                       id="alias"
                       name="alias">
                <i class="w3-text-grey">Der Alias ist später für andere Gruppenmitglieder sichtbar.</i>
            </div>
            <div class="w3-section">
                <label class="w3-text-primary" for="vorname">Vorname</label>
                <input required
                       type="text"
                       class="w3-input w3-border w3-border-primary"
                       value="<?= explode(' ', $team->details['ligavertreter'])[0] ?>"
                       id="vorname"
                       name="vorname">
            </div>
            <div class="w3-section">
                <label class="w3-text-primary" for="nachname">Nachname</label>
                <input required
                       type="text"
                       class="w3-input w3-border w3-border-primary"
                       value="<?= explode(' ', $team->details['ligavertreter'])[1] ?>"
                       id="nachname"
                       name="nachname">
            </div>
            <div class="w3-section">
                <label class="w3-text-primary" for="email">E-Mail-Adresse des Admins</label>
                <input required
                       type="email"
                       list="emails"
                       class="w3-input w3-border w3-border-primary"
                       id="email"
                       name="email">
                <datalist id="emails">
                    <?php foreach ($emails as $email) { ?>
                        <option value="<?= $email ?>">
                    <?php } // end foreach?>
                    </datalist>
                </datalist>
            </div>
        </div>

        <!-- Submit -->
        <div class="w3-panel w3-card-4">
            <input type=hidden name='passphrase' value='<?= Env::TERMINPLANER_KEY ?>'>
            <input type="hidden" name='ligaUID' value=" <?=$_SESSION['logins']['team']['id'] ?>">
            <p>
                <button type="submit" name="create_team" class="w3-secondary w3-button w3-block">
                    <?= Html::icon("input") ?> Gruppe anlegen
                </button>
            </p>
        </div>

    <?php } ?>
</form>
