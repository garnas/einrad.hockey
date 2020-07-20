<!-- Formular zur auswahl eines Teams -->
<form class="w3-panel w3-card-4" method="post">
  <p>
    <label for="la_team_waehlen"><h3 class="w3-text-primary">Bitte Ligateam auswählen</h3></label>
    <input onchange="this.form.submit();" type="text" style="max-width:400px" class="w3-input w3-border w3-border-primary" list="teams" id="la_team_waehlen" name="la_team_waehlen">
      <?=Form::datalist_teams()?>
  </p>
  <p>
    <input type="submit" class="w3-button w3-tertiary" value="Team wählen">
  </p>
</form>
