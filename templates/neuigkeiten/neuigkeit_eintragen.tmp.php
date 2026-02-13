<?php 
    use App\Service\Neuigkeit\PermissionService; 
    use App\Service\Neuigkeit\FormatService; 
    use App\Enum\NeuigkeitArt;
?>

<!-- Neuigkeit eintragen -->
<h1 class="w3-text-primary">Neuigkeit eintragen</h1>

<form action="" method="post" enctype="multipart/form-data">
    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="titel">Titel (Optional)</label>
            <input class="w3-input w3-border w3-border-grey" type="text" id="titel" name="titel" placeholder="Titel der Neuigkeit" value="<?=$_POST['titel'] ?? ''?>" >
        </div>
    </div>

    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="text">Art der Neuigkeit</label>
            <select id="art" name="art" class="w3-input">
                <?php foreach (NeuigkeitArt::cases() as $case): ?>
                    <?php if (PermissionService::canSetArt($case)): ?>
                        <option value="<?=$case->value?>"><?=FormatService::getArtString($case)?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="text">Text</label>
            <textarea required class="w3-input w3-border w3-border-grey" rows="10" id="text" name="text" placeholder="Text der Neuigkeit" onkeyup="woerter_zaehlen(750)" maxlength="750"><?=stripcslashes($_POST['text'] ?? '')?></textarea>
            <p id="counter"><i>Es dürfen 750 Zeichen verwendet werden. Für mehr Infos kannst du ein Bild und/oder ein PDF hochladen.</i><p>
        </div>
    </div>
    
    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="jpgupload">Bild (Optional)</label><br>
            <input class="w3-primary w3-padding" type="file" name="jpgupload" id="jpgupload">
            <p><i>Es können nur Bilder im <b>.jpg, .jpeg, .gif, .png</b> Format mit bis zu 11,9 Megabyte hochgeladen werden. Bilder werden webtauglich verarbeitet - exif-Daten der Bilder werden gelöscht.</i></p>
            
            <?php if(PermissionService::canEmbedLink()):?>
                <label for="bild_verlinken">Bild verlinken (Optional)</label><br>
                <input class="w3-input w3-border w3-border-grey" placeholder="Link angeben" type="url" id="bild_verlinken" name="bild_verlinken" value="<?=$_POST['bild_verlinken'] ?? ''?>" >
            <?php endif; ?>

        </div>
    </div>
    
    <div class="w3-section">
        <div class="w3-border w3-border-primary" style="padding: 16px;">
            <label for="pdfupload">Dokument (Optional)</label><br>
            <input class="w3-primary w3-padding" type="file" name="pdfupload" id="pdfupload">
            <p><i>Es können nur Dateien im <b>.pdf / .xlsx</b> Format mit bis zu <b>3 MB</b> hochgeladen werden.</i></p>
        </div>
    </div>

    <?php if(PermissionService::canSetTime()): ?>
        <div class="w3-section">
            <div class="w3-border w3-border-primary" style="padding: 16px;">
                <label for="zeitpunkt">Angezeigter Veröffentlichungszeitpunkt</label><br>
                <input type="datetime-local" class="w3-input w3-border w3-border-grey" id="zeitpunkt" name="zeitpunkt" value="<?=date('Y-m-d\TH:i')?>">
            </div>
        </div>
    <?php endif; ?>

    <div class="w3-section">
        <input type="submit" class="w3-button w3-primary" id="submit" name="create_neuigkeit" value="Neuigkeit eintragen">
    </div>
</form>
