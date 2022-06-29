<?php

use App\Service\Turnier\BlockService;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierValidatorService;

?>
<!-- Formular -->
<form method="post">
    <div class="w3-panel w3-card-4">
        <h3>Turnier erweitern</h3>
        <?php if (TurnierValidatorService::isErweiterbarBlockhoch($turnier)): ?>
            <p>
                <button type="submit" class="w3-button w3-block w3-tertiary" name="block_erweitern">
                    Turnierblock erweitern auf <?= BlockService::nextTurnierBlock($turnier)?>
                </button>
            </p>
        <?php endif; ?>
        <?php if (TurnierValidatorService::isErweiterbarBlockfrei($turnier)): ?>
            <p>
                <button type="submit" class="w3-button w3-block w3-tertiary" name="block_frei">
                    Turnierblock erweitern auf ABCDEF
                </button>
            </p>
        <?php endif; ?>
        <?php if (!TurnierValidatorService::isErweiterbarBlockfrei($turnier)
            && !TurnierValidatorService::isErweiterbarBlockhoch($turnier)): ?>
            <p>
                Turnierblock kann nicht auf den nächsten höheren BLock oder auf ABCDEF erweitert werden.
            </p>
            <?php if ($turnier->isWartePhase()):?>
                <p>
                    <i class="w3-text-grey">Turniere können ab der Setzphase (ab <?= TurnierService::getLosDatum($turnier) ?>) erweitert werden.</i>
                </p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</form>
<form method="post">
    <div class="w3-panel w3-card-4">
        <h3>Startzeit</h3>
        <p>
            <label class="w3-text-primary" for="startzeit">Startzeit</label>
            <input required type="time" class="w3-input w3-border w3-border-primary" value="<?=$turnier->getDetails()->getStartzeit()->format("H:i") ?>" style="max-width: 320px" id="startzeit" name="startzeit">
            <i class="w3-text-grey">Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen um 20:00&nbsp;Uhr beendet sein</i>
        </p>
        <p>
            <input
                    class="w3-check"
                    type="checkbox"
                    id="besprechung"
                    name="besprechung"
                    <?= ($turnier->getDetails()->getBesprechung() === "Ja") ? "checked" : "" ?>
            >
            <label style="cursor: pointer;" class="w3-hover-text-secondary w3-text-primary" for="besprechung"> Gemeinsame Besprechung aller Teams 15 min vor Turnierbeginn</label>
        </p>
        <h3>Plätze</h3>
        <p>
            <label class="w3-text-primary" for="plaetze">Plätze</label>
            <select required class="w3-select w3-border w3-border-primary" id="plaetze" name="plaetze">
                <option <?php if($turnier->getDetails()->getPlaetze() == '4'){?>selected<?php }elseif(Helper::$teamcenter){?> disabled <?php }?> value="4">4 Teams (nur in Absprache mit dem Ligaausschuss)</option>
                <option <?php if($turnier->getDetails()->getPlaetze() == '5'){?>selected<?php }?> value="5">5 Teams</option>
                <option <?php if($turnier->getDetails()->getPlaetze() == '6'){?>selected<?php }?> value="6">6 Teams</option>
                <option <?php if($turnier->getDetails()->getPlaetze() == '7'){?>selected<?php }?> value="7">7 Teams</option>
                <option <?php if($turnier->getDetails()->getPlaetze() == '8'){?>selected<?php }?> value="8">8 Teams</option>
                <?php if (Helper::$ligacenter): ?>
                    <option <?php if($turnier->getDetails()->getPlaetze() == '9'){?>selected<?php }?> value="9">9 Teams</option>
                    <option <?php if($turnier->getDetails()->getPlaetze() == '10'){?>selected<?php }?> value="10">10 Teams</option>
                    <option <?php if($turnier->getDetails()->getPlaetze() == '11'){?>selected<?php }?> value="11">11 Teams</option>
                    <option <?php if($turnier->getDetails()->getPlaetze() == '12'){?>selected<?php }?> value="12">12 Teams</option>
                <?php endif; ?>
            </select>
        </p>
    </div>
    <div class="w3-panel w3-card-4">
            <h3>Adresse</h3>
            <div class="w3-section">
                <label class="w3-text-primary" for="hallenname">Hallenname</label>
                <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$turnier->getDetails()->getHallenname()?>" id="hallenname" name="hallenname">
            </div>
            <div class="w3-section">
                <label class="w3-text-primary" for="strasse">Straße und Hausnummer</label>
                <input required type="text" class="w3-input w3-border w3-border-primary" value="<?=$turnier->getDetails()->getStrasse() ?>" id="strasse" name="strasse">
            </div>
            <div class="w3-section">
                <label class="w3-text-primary" for="plz">PLZ</label>
                <input required type="text" class="w3-input w3-border w3-border-primary" maxlength="5" value="<?= $turnier->getDetails()->getPlz() ?>" id="plz" name="plz">
            </div>
            <div class="w3-section">
                <label class="w3-text-primary" for="ort">Ort</label>
                <input required type="text" class="w3-input w3-border w3-border-primary" value="<?= $turnier->getDetails()->getOrt() ?>" id="ort" name="ort">
            </div>
            <div class="w3-section">
                <label class="w3-text-primary" for="haltestellen">Haltestellen</label>
                <input type="text" class="w3-input w3-border w3-border-primary" value="<?= $turnier->getDetails()->getHaltestellen() ?>" id="haltestellen" name="haltestellen">
                <i class="w3-text-grey">Optional - Für die Anfahrt mit öffentlichen Verkehrsmitteln</i>
            </div>
    </div>
    <div class="w3-panel w3-card-4">
        <h3>Turnierdetails</h3>
        <p>
            <label class="w3-text-primary" for="tname">Turniername <i class="w3-small">(optional)</i></label>
            <input type="text" maxlength="60" value="<?= e($turnier->getName()) ?>" class="w3-input w3-border w3-border-primary" id="tname" name="tname">
        </p>
        <p>
            <label class="w3-text-primary" for="text">Hinweistext</label>
            <i class="w3-text-grey">(optional)</i>
            <textarea class="w3-input w3-border w3-border-primary" onkeyup="woerter_zaehlen(1500);" maxlength="1500" rows="4" id="text" name="hinweis"><?=$turnier->getDetails()->getHinweis()?></textarea>
            <p id="counter"><p>
        </p>
        <p>
            <label class="w3-text-primary" for="startgebuehr">Startgebühr</label>
            <?php if(Helper::$ligacenter) { ?>
                <input type="text" class="w3-input w3-border w3-border-primary" placeholder="z. B. 5 Euro" value="<?=$turnier->getDetails()->getStartgebuehr()?>" id="startgebuehr" name="startgebuehr">
            <?php } else { ?>
                <select class="w3-input w3-border w3-border-primary" id="startgebuehr" name="startgebuehr">
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == 'keine'){?>selected<?php }?> value="keine">keine</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '5 Euro'){?>selected<?php }?> value="5 Euro">5 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '6 Euro'){?>selected<?php }?> value="6 Euro">6 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '7 Euro'){?>selected<?php }?> value="7 Euro">7 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '8 Euro'){?>selected<?php }?> value="8 Euro">8 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '9 Euro'){?>selected<?php }?> value="9 Euro">9 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '10 Euro'){?>selected<?php }?> value="10 Euro">10 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '11 Euro'){?>selected<?php }?> value="11 Euro">11 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '12 Euro'){?>selected<?php }?> value="12 Euro">12 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '13 Euro'){?>selected<?php }?> value="13 Euro">13 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '14 Euro'){?>selected<?php }?> value="14 Euro">14 Euro</option>
                    <option <?php if($turnier->getDetails()->getStartgebuehr() == '15 Euro'){?>selected<?php }?> value="15 Euro">15 Euro</option>
                </select>
            <?php } //end if?>
        </p>
    </div>
    <div class="w3-panel w3-card-4">
        <h3>Organisator</h3>
        <p>
            <label class="w3-text-primary" for="organisator">Name</label>
            <input required value="<?=$turnier->getDetails()->getOrganisator()?>" type="text" class="w3-input w3-border w3-border-primary" id="organisator" name="organisator">
        </p>
        <p>
            <label class="w3-text-primary" for="handy">Handy</label>
            <input required value="<?=$turnier->getDetails()->getHandy()?>" type="text" class="w3-input w3-border w3-border-primary" id="handy" name="handy">
            <i class="w3-text-grey">Das Handy muss am Turniertag erreichbar sein</i>
        </p>
    </div>
    <div class="w3-panel w3-card-4">
        <p>
            <input type="submit" value="Turnierdaten ändern" name="change_turnier" class="w3-tertiary w3-button w3-block">
        </p>
    </div>
</form>