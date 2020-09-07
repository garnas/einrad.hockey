<form method="post">
    <?php if($spielplan->akt_turnier->daten['phase'] == 'ergebnis'){?>
        <p class="w3-text-green">Dem Ligaausschuss liegt ein Turnierergebniss vor. Durch erneutes Speichern kann das Turnierergebnis verändert werden.</p>
    <?php }//endif?>
    <?php if($spielplan->akt_turnier->daten['phase'] != 'ergebnis'){?>
        <p class="w3-text-grey">Dem Ligaausschuss liegt noch kein Turnierergebnis vor.</p>
    <?php }//endif?>
    <p>
        <input type="submit" name="gesendet_turnierergebnisse" class="w3-block w3-button w3-tertiary" value="Ergebnisse speichern">
    </p>
</form>