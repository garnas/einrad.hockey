<form action="https://team.einrad.hockey/forms/gruppe-add_record.php" method="post">
    <div class="w3-panel w3-tertiary w3-card-4">
        <h3 class="w3-center">Neue Terminseite für Deinen Verein anlegen</h3>
    </div>

    <div class="w3-panel w3-card-4">
        <p>Hier kannst Du eine neue Terminseite für Deinen Verein anlegen. Unter <a href="https://team.einrad.hockey">team.einrad.hockey</a> können sich die Mitspieler dann anmelden und für Termine eintragen.
          So könnt Ihr leicht den Überblick behalten wer zu welchem Termin kommt oder bereits abgesagt hat.<br><br>
          Trainingszeiten werden nur einmalig angelegt und wiederholen sich wöchentlich.<br>
          Daten der Ligaturniere können einfach übernommen werden und es ist den Spielern ersichtlich, ob Ihr zum Turnier gemeldet seid und ob Ihr spielt oder auf der Warteliste steht.<br>
          Auch ist es möglich Eure Termine über CalDAV in verschiedene Clients (z.B. Thunderbird, Outlook) oder Android und iOS einzubinden.<br><br>
          Es ist möglich und sinnvoll mehrere Teams für einen Verein anzulegen (Nur 1 Account hier anlegen). So könnt Ihr auch Spielern ermöglichen bei Turnieren auszuhelfen oder ein gemeinsames Training für mehrere Teams organsiseren.<br><br>
          Die Erstellung der Spieleraccounts, Teams etc. ist unabhängig von der Teamkaderverwaltung auf der Ligaseite.<br>
          Bei Fragen kontaktiere <a href="mailto:team@einrad.hockey">team@einrad.hockey</a>.
        </p>
    </div>

<?php if (!$akt_team->get_terminplaner()) {?>
    <div class="w3-card-4 w3-panel">
        <h3>Gruppe</h3>
        <div class="w3-section">
            <label class="w3-text-primary" for="gruppenname">Gruppen- / Vereinsname</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$daten['verein']?>" id="gruppenname" name="gruppenname">
        </div>
    </div>

    <div class="w3-card-4 w3-panel">
        <h3>Administrator</h3>
        <div class="w3-section">
            <label class="w3-text-primary" for="alias">Alias</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="" id="alias" name="alias">
            <i class="w3-text-grey">Der Alias ist später für andere Gruppenmitglieder sichtbar</i>
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="vorname">Vorname</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=explode(' ',$daten['ligavertreter'])[0]?>" id="vorname" name="vorname">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="nachname">Nachname</label>
            <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=explode(' ',$daten['ligavertreter'])[1]?>" id="nameBot" name="nachname">
        </div>
        <div class="w3-section">
            <label class="w3-text-primary" for="email">Emailadresse</label>
            <input required type="email" class="w3-input w3-border w3-border-primary" value="<?=$emails[0]['email']?>" id="email" name="email">
        </div>
    </div>


    <!-- Submit -->
    <div class="w3-panel w3-card-4">
        <input type=hidden name='passphrase' value='NurdamitnichtjederHansundFranzeineGruppeanlegenkann'>
        <p>
            <input type="submit" value="Gruppe anlegen" name="create_team" class="w3-secondary w3-button w3-block">
        </p>
    </div>

  <?php } else { ?>
    <div class="w3-card-4 w3-panel">
        Für Dein Team wurde mit der Emailadresse bereits eine Gruppe angelegt.<br>
        Klicke <?=Form::link('https://team.einrad.hockey')?>, um auf deine Seite zu gelangen.<br><br>
        Bei Fragen kannst du dich bei <?=Form::mailto(Config::TECHNIKMAIL)?> melden.
    </div>
  <?php }?>
</form>
