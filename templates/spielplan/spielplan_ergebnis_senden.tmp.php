<?php /** @var \App\Model\Spielplan\Spielplan $spielplan */ ?>
<form method="post">
    <p>
        <button type="submit"
               <?= $spielplan->getTurnier()->isFinalTurnier() ? "disabled" : "" ?>
               name="turnierergebnis_speichern"
               class="w3-block w3-button w3-tertiary <?= $spielplan->istTurnierBeendet() ?: 'w3-opacity' ?>"
        >
            <?= Html::icon('send') ?> In die Ligatabellen eintragen
        </button>
    </p>
    <?php if ($spielplan->getTurnier()->getPhase() == 'ergebnis') { ?>
        <p class="w3-text-green">
            <?= Html::icon('check_circle') ?>
            Dem Ligaausschuss liegt ein Turnierergebnis vor.
        </p>
        <p class="w3-text-green">
            <?= Html::icon('info') ?>
            Durch erneutes Übermitteln kann das Turnierergebnis korrigiert werden.
        </p>
    <?php } //end if?>

    <?php if ($spielplan->getTurnier()->getPhase() != 'ergebnis') { ?>
        <p class="w3-text-grey">
            <?= Html::icon('info') ?>
            Dem Ligaausschuss liegt noch kein Turnierergebnis vor.
        </p>
    <?php } // endif?>
</form>
