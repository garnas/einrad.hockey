<!-- Formular zur auswahl eines Teams -->
<form class="w3-panel w3-card-4" method="post">
    <h3 class="w3-text-primary">
        <label for="la_team_waehlen">Bitte Ligateam auswählen</label>
    </h3>
    <input onchange="this.form.submit();"
           type="text"
           style="max-width:400px"
           class="w3-input w3-border w3-border-primary"
           placeholder="Team eingeben"
           list="teams"
           id="la_team_waehlen"
           name="la_team_waehlen">

    <?= Html::datalist_teams() ?>

    <p>
        <input type="submit" class="w3-button w3-tertiary" value="Team wählen">
    </p>
</form>
