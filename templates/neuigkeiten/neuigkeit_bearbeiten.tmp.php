<?php

use App\Entity\Sonstiges\Neuigkeit;
use App\Service\Neuigkeit\PermissionService;

?>

<h1 class="w3-text-primary">Neuigkeit bearbeiten</h1>

<form action="" method="post" enctype="multipart/form-data">
    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="titel">Titel (Optional)</label>
            <input class="w3-input w3-border w3-border-grey" type="text" id="titel" name="titel" placeholder="Titel der Neuigkeit" value="<?=$neuigkeit->getTitel()?>" >
        </div>
    </div>

    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="text">Text</label>
            <textarea required class="w3-input w3-border w3-border-grey" rows="10" id="text" name="text" placeholder="Text der Neuigkeit" onkeyup="woerter_zaehlen(750)" maxlength="750"><?=$neuigkeit->getInhalt()?></textarea>
            <p id="counter"><i>Es dürfen 750 Zeichen verwendet werden. Für mehr Infos kannst du ein Bild und/oder ein PDF hochladen.</i><p>
        </div>
    </div>
    
    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="jpgupload">Bild (Optional)</label><br>
            <input class="w3-primary w3-padding" type="file" name="jpgupload" id="jpgupload">
            <p><i>Es können nur Bilder im <b>.jpg, .jpeg, .gif, .png</b> Format mit bis zu 11,9 Megabyte hochgeladen werden. Bilder werden webtauglich verarbeitet - exif-Daten der Bilder werden gelöscht.</i></p>
            <hr>
            
            <?php if(PermissionService::canEmbedLink()):?>
                <label for="bild_verlinken">Bild verlinken (Optional)</label><br>
                <input class="w3-input w3-border w3-border-grey" placeholder="Link angeben" type="url" id="bild_verlinken" name="bild_verlinken" value="<?=$neuigkeit->getBildVerlinken() ?: ''?>" >
                <hr>
            <?php endif; ?>
            
            <label for="delete_jpg">Bisheriges Bild löschen:</label><br>
            <input type='radio' class='w3-radio' id='delete_jpg_ja' name='delete_jpg' value='Ja'>
            <label for="delete_jpg_ja">Ja</label><br>
            <input type='radio' class='w3-radio' id='delete_jpg_nein' name='delete_jpg' value='Nein' checked>
            <label for="delete_jpg_nein">Nein</label><br>
        </div>
    </div>

    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="pdfupload">Dokument (Optional)</label><br>
            <input class="w3-primary w3-padding" type="file" name="pdfupload" id="pdfupload">
            <p><i>Es können nur Dateien im <b>.pdf / .xlsx</b> Format mit bis zu <b>3 MB</b> hochgeladen werden.</i></p>
            <hr>
            <label for="delete_pdf">Bisheriges Dokument löschen:</label><br>
            <input type='radio' class='w3-radio' id='delete_pdf_ja' name='delete_pdf' value='Ja'>
            <label for="delete_pdf_ja">Ja</label><br>
            <input type='radio' class='w3-radio' id='delete_pdf_nein' name='delete_pdf' value='Nein' checked>
            <label for="delete_pdf_nein">Nein</label><br>
        </div>
    </div>

    <?php if(PermissionService::canSetTime()): ?>
        <div class="w3-section">
            <div class="w3-border w3-border-primary" style="padding: 16px;">
                <label for="zeitpunkt">Veröffentlichungszeitpunkt</label><br>
                <input type="datetime-local" class="w3-input w3-border w3-border-grey" id="zeitpunkt" name="zeitpunkt" value="<?=$neuigkeit->getZeit() ? $neuigkeit->getZeit()->format('Y-m-d\TH:i') : date('Y-m-d\TH:i')?>">
            </div>
        </div>
    <?php endif; ?>

    <div class="w3-section">
        <input type="submit" class="w3-button w3-primary" id="submit" name="change_neuigkeit" value="Speichern">
        <a class="w3-button w3-secondary" href="../liga/neues.php">Abbrechen</a>
    </div>
</form>
