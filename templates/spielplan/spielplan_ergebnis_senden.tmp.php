<form method="post">
    <p>
        <input type="submit"
               name="turnierergebnis_speichern"
               class="w3-block w3-button w3-tertiary <?= $spielplan->check_turnier_beendet() ?: 'w3-opacity' ?>"
               value="Turnierergebnis übermitteln"
        >
    </p>
    <?php if ($spielplan->turnier->details['phase'] == 'ergebnis') { ?>
        <p class="w3-text-green">
            <span class="material-icons">check_circle</span>
            Dem Ligaausschuss liegt ein Turnierergebnis vor.
        </p>
        <p class="w3-text-green">
            <span class="material-icons">info</span>
            Durch erneutes Übermitteln kann das Turnierergebnis korrigiert werden.
        </p>
    <?php } //end if?>

    <?php if ($spielplan->turnier->details['phase'] != 'ergebnis') { ?>
        <p class="w3-text-grey">
            <span class="material-icons">info</span>
            Dem Ligaausschuss liegt noch kein Turnierergebnis vor.
        </p>
    <?php } // endif?>
</form>