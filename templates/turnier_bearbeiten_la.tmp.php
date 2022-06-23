<?php

use App\Service\Turnier\TurnierSnippets;

?>
<!-- Formular Turnier löschen -->
<form method="post" onsubmit="return confirm('Das Turnier in <?= e(TurnierSnippets::ortDatumBlock($turnier)) ?> mit der ID <?= $turnier->id() ?> wird gelöscht werden.');">
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

<?php if($turnier->isCanceled()):
    Html::message('info', 'Turnier wurde abgesagt - wende dich an den Technikausschuss um es wieder herzustellen.');
else: ?>
    <form method="post">
        <div class="w3-panel w3-card-4">
            <h3>Turnier absagen <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
            <p>
                <b>Hinweis:</b> Zur bevorzugen, das Turnier bleibt in der Datenbank
                <br>
                <b>Allen Teams auf der Warte- und Setzliste wird eine automatische E-Mail geschickt (mit dem Grund)</b>
            </p>
            <p>
                <label for='grund' class="w3-text-primary">Grund der Turnierabsage</label>
                <input list="browsers" id="grund" name="grund" placeholder="Bitte eingeben.." class="w3-input w3-border w3-border-primary">
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

<!-- Formular Ligaausschuss -->
<form method="post">
    <div class="w3-panel w3-card-4">
        <h3>Turnierdaten <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
        <p>
            <label for="ausrichter" class='w3-text-primary'>Ausrichter ändern</label><br>
            <input type="text" class="w3-input w3-border w3-border-primary" value="<?=e($turnier->getAusrichter()->getName())?>" list="teams" id="ausrichter" name="ausrichter">
                <?=Html::datalist_teams()?>
        </p>
        <p>
            <label class="w3-text-primary" for="tname">Turniername <i class="w3-small">(optional)</i></label>
            <input type="text" maxlength="60" value="<?= e($turnier->getName()) ?>" class="w3-input w3-border w3-border-primary" id="tname" name="tname">
        </p>
        <p>
            <label class="w3-text-primary" for="datum">Datum</label>
            <input required type="date" value="<?= e($turnier->getDatum()->format('Y-m-d')) ?>" class="w3-input w3-border w3-border-primary" style="max-width: 320px" id="datum" name="datum">
        </p>
        <h3>Ligalogik <span class="w3-text-gray">- nur Ligaausschuss</span></h3>
        <p>
            <label class="w3-text-primary" for="phase">Phase</label>
            <select required type="date" class="w3-input w3-border w3-border-primary" id="phase" name="phase">
                <option <?php if($turnier->getPhase() == 'warte'){?> selected <?php }?> value="warte">Wartephase</option>
                <option <?php if($turnier->getPhase() == 'setz'){?> selected <?php }?> value="setz">Setzphase</option>
                <option <?php if($turnier->getPhase() == 'spielplan'){?> selected <?php }?> value="spielplan">Spielplan</option>
                <option <?php if($turnier->getPhase() == 'ergebnis'){?> selected <?php }?> value="ergebnis">Ergebnis</option>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="art">Turnierart</label>
            <select required class="w3-select w3-border w3-border-primary" id="art" name="art">
                <option <?php if($turnier->getArt() == 'I'){?> selected <?php }?> value="I">I: Blockeigenes Turnier</option>
                <option <?php if($turnier->getArt() == 'II'){?> selected <?php }?> value="II">II: Blockhöheres Turnier</option>
                <option <?php if($turnier->getArt() == 'spass'){?> selected <?php }?> value="spass">Spaßturnier</option>
                <option <?php if($turnier->getArt() == 'final'){?> selected <?php }?> value='final'>Abschlussturnier</option>
                <option <?php if($turnier->getArt() == 'fixed'){?> selected <?php }?> value='fixed'>Manuelles (fixed) Turnier</option>
            </select>
        </p>
        <?php if ($turnier->getArt() !== "final"): ?>
            <p>
                <label class="w3-text-primary" for="block">Turnierblock</label>
                <select required class="w3-select w3-border w3-border-primary" id="block" name="block">
                    <?php foreach (Config::BLOCK_ALL as $block): ?>
                        <option <?= ($turnier->getBlock() === $block) ? 'selected' : '' ?>> <?=$block?></option>
                    <?php endforeach; ?>
                </select>
                <i class="w3-text-primary">Nach ändern des Blockes sollten die Anmeldelisten kontrolliert werden.</i>
            </p>
        <?php endif; ?>
        <p>
            <input type="submit" value="Turnierdaten ändern" name="turnier_bearbeiten_la" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>