<form method="post">
    <p>
    <input type="submit" 
        name="gesendet_turnierergebnisse" 
        class="w3-block w3-button w3-tertiary <?php if(!$spielplan->check_alles_gespielt($tabelle) && empty($spielplan->penalty_warning)){?>w3-opacity<?php }//endif?>"
        value="Turnierergebnis übermitteln">
    </p>
    <?php if($spielplan->akt_turnier->details['phase'] == 'ergebnis'){?>
        <p class="w3-text-green"><i class="material-icons">check_circle</i> Dem Ligaausschuss liegt ein Turnierergebnis vor.<br><br><i class="material-icons">info</i> Durch erneutes Übermitteln kann das Turnierergebnis korrigiert werden.</p>
    <?php }//endif?>
    <?php if($spielplan->akt_turnier->details['phase'] != 'ergebnis'){?>
        <p class="w3-text-grey"><i class="material-icons">info</i> Dem Ligaausschuss liegt noch kein Turnierergebnis vor.</p>
    <?php }//endif?>
</form>