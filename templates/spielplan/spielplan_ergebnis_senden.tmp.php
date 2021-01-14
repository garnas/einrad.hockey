<form method="post">

    <p>
        <input type="submit"
               name="turnierergebnis_speichern"
               class="w3-block w3-button w3-tertiary <?= $spielplan->check_turnier_beendet() ?: 'w3-opacity' ?>"
               value="Turnierergebnis übermitteln">
    </p>

    <?php if ($spielplan->turnier->details['phase'] == 'ergebnis') { ?>
            <p class="w3-text-green">
                <i class="material-icons">check_circle</i> Dem Ligaausschuss liegt ein Turnierergebnis vor.
            </p>
            <p class="w3-text-green">
                <i class="material-icons">info</i> Durch erneutes Übermitteln kann das Turnierergebnis korrigiert werden.
            </p>
    <?php } //end if?>

    <?php if ($spielplan->turnier->details['phase'] != 'ergebnis') { ?>
        <p class="w3-text-grey">
            <i class="material-icons">info</i> Dem Ligaausschuss liegt noch kein Turnierergebnis vor.
        </p>
    <?php } // endif?>

</form>