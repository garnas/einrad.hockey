<?php

use App\Service\Turnier\TurnierSnippets;

?>

<!-- Formular Turnier löschen -->
<form method="post" onsubmit="return confirm('Das Turnier in <?= TurnierSnippets::ortDatumBlock($turnier) ?> mit der ID <?= $turnier->id() ?> wird gelöscht werden.');">
    <div class="w3-panel w3-card-4">
        <h3>Turnier löschen <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
        <p>
            <b>Hinweis:</b> Nur bei fehlerhafter oder doppelter Eintragung!
        </p>
        <p>
            <input type="submit" value="Turnier löschen" name="delete_turnier" class="w3-secondary w3-button w3-block">
        </p>
    </div>
</form>

<!-- Formular Turnier absagen -->
<?php if ($turnier->isCanceled()):
    Html::message('info', 'Turnier wurde abgesagt - wende dich an den Technikausschuss um es wieder herzustellen.');
else: ?>
    <form method="post">
        <div class="w3-panel w3-card-4">
            <h3>Turnier absagen <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
            <p>
                <b>Hinweis:</b> Zur bevorzugen, das Turnier bleibt in der Datenbank
                <br>
            </p>
            <p>
                <input class="w3-check" type="checkbox" id="send_mail" name="send_mail" checked value="send_mail">
                <label for="send_mail">
                    <b>Allen Teams auf der Warte- und Setzliste wird eine automatische E-Mail geschickt
                        (mit dem Grund)</b>
                </label>
            </p>
            <p>
                <label for='grund' class="w3-text-primary">Grund der Turnierabsage</label>
                <input required list="browsers" id="grund" name="grund" placeholder="Bitte eingeben.." class="w3-input w3-border w3-border-primary">
                <datalist id="browsers">
                    <option value="Zu wenig spielberechtigte Ligateams">
                    <option value="Vom Ausrichter im Vorfeld abgesagt">
                    <option value="Spaßturnier">
                    <option value="Corona-Pandemie">
                </datalist>
            </p>
            <p>
                <input type="submit" value="Turnier absagen" name="absagen_turnier" class="w3-secondary w3-button w3-block">
            </p>
            <p>
        </div>
    </form>
<?php endif; ?>
