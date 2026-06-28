<?php

use App\Service\Turnier\TurnierValidatorService;
use App\Service\Turnier\BlockService;

?>

<?php if ($turnier->isSetzPhase()): ?>

    <form method="post">
        <div class="w3-panel w3-card-4">

            <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
                <p>
                    Es ist möglich, den Turnierblock auf unterschiedliche Arten anzupassen.
                </p>
                <p>
                    Bei der Erweiterung um einen einzigen Block kann ein niedrigerer oder ein höherer Block gewählt werden. Beides ist nicht möglich. Im Anschluss kann das Turnier auch auf ein ABCDEF-Turnier erweitert werden. Dies ist auch unabhängig von einer vorherigen Erweiterung um einen Block höher oder niedriger möglich.
                </p>
            </div>

            <h3>Turniererweiterung um einen Block</h3>

            <?php if ($turnier->isBlockErweitertRunter() || $turnier->isBlockErweitertHoch()): ?>
                
                <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
                    <p>
                        Das Turnier ist bereits um einen Block erweitert worden.
                    </p>
                </div>
            
            <?php else: ?>

                <?php if (TurnierValidatorService::isErweiterbarBlockhoch($turnier)): ?>
                    <p>
                        <button type="submit" class="w3-button w3-block w3-tertiary" name="block_erweitern_hoch">
                            Turnierblock erweitern auf <?= BlockService::hoehererTurnierBlock($turnier)?>
                        </button>
                    </p>
                <?php endif; ?>
                
                <?php if (TurnierValidatorService::isErweiterbarBlockrunter($turnier)): ?>
                    <p>
                        <button type="submit" class="w3-button w3-block w3-tertiary" name="block_erweitern_runter">
                            Turnierblock erweitern auf <?= BlockService::niedrigererTurnierBlock($turnier)?>
                        </button>
                    </p>
                <?php endif; ?>
            
            <?php endif; ?>

            <h3>Turniererweiterung um alle Blöcke</h3>

            <?php if (TurnierValidatorService::isErweiterbarBlockfrei($turnier)): ?>
                <p>
                    <button type="submit" class="w3-button w3-block w3-tertiary" name="block_frei">
                        Turnierblock erweitern auf ABCDEF
                    </button>
                </p>
            <?php else: ?>
                <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
                    <p>
                        Das Turnier kann nicht auf alle Blöcke erweitert werden.
                    </p>
                </div>
            <?php endif; ?>
        
        </div>
    
    </form>

<?php endif; ?>


<?php if ($turnier->isWartePhase()): ?>
    <form method="post">
        <div class="w3-panel w3-card-4">
            <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
                <p>
                    In der aktuellen Phase des Turniers ist es nicht möglich, eine Blockänderung vorzunehmen. Stattdessen kann die automatische Erweiterung angepasst werden. Diese findet sofort nach Übergang in die Wartephase statt.
                </p>
            </div>
            <div>
                <p>
                    <select required class="w3-select w3-border w3-border-primary w3-padding w3-white" id="sofort_oeffnen" name="sofort_oeffnen">
                        <option <?php if ($turnier->isBlockErweitertHoch()) {?> selected <?php } ?> value="higher">Automatisch einen Block nach oben erweitern</option>
                        <option <?php if ($turnier->isBlockErweitertRunter()) {?> selected <?php } ?> value="lower">Automatisch einen Block nach unten erweitern</option>
                        <option <?php if (!$turnier->isBlockErweitertRunter() && !$turnier->isBlockErweitertHoch()) {?> selected <?php } ?> value="none">Keine automatische Erweiterung vornehmen</option>
                    </select>
                </p>
            </div>
            
            <div>
                <p>
                    <button type="submit" class="w3-button w3-block w3-tertiary" name="automatisch">
                        Änderungen
                    </button>
                </p>
            </div>
        </div>
    </form>
<?php endif; ?>