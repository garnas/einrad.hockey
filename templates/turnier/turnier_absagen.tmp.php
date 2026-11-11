<?php
/** @var \App\Turnier\Entity\Turnier $turnier */
/** @var string[] $absage_gruende Vorschläge für den Absagegrund */
?>
<?php if ($turnier->isCanceled()): ?>
    <?php Html::message('info', 'Turnier wurde abgesagt - wende dich an den Technikausschuss um es wieder herzustellen.'); ?>
<?php else: ?>
    <form method="post" onsubmit="return confirm('Das Turnier wird abgesagt.');">
        <div class="w3-panel w3-card-4">
            <h3 class="w3-text-secondary">Turnier absagen</h3>
            <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
                <p>
                    Das Turnier wird in der Datenbank als "abgesagt" markiert und wird nicht mehr angezeigt.
                    Wiederherstellen kann es nur der Technikausschuss.
                </p>
            </div>
            <p>
                <label for='grund' class="w3-text-primary">Grund der Turnierabsage</label>
                <input required list="absage_gruende" id="grund" name="grund" placeholder="Bitte eingeben..." class="w3-input w3-border w3-border-primary">
                <datalist id="absage_gruende">
                    <?php foreach ($absage_gruende as $absage_grund): ?>
                        <option value="<?= e($absage_grund) ?>">
                    <?php endforeach; ?>
                </datalist>
            </p>
            <p>
                <input class="w3-check" type="checkbox" id="send_mail" name="send_mail" checked value="send_mail">
                <label for="send_mail">
                    Allen Teams auf der Warte- und Setzliste wird eine automatische E-Mail geschickt (mit dem Grund)
                </label>
            </p>
            <p>
                <input type="submit" value="Turnier absagen" name="absagen_turnier" class="w3-secondary w3-button w3-block">
            </p>
        </div>
    </form>
<?php endif; ?>
