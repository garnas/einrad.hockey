
<!-- Neuigkeit bearbeiten -->
<h3>Neuigkeit bearbeiten<?php if($ligacenter){?> (als Ligaausschuss)<?php }?></h3>
<p id="counter"><i>Es dürfen 500 Zeichen verwendet werden. Für mehr Infos kannst du ein Bild und/oder ein PDF hochladen.</i><p>
<form action="" method="post" enctype="multipart/form-data">
    <p>
        <label class="" for="titel">Titel</label>
        <input class="w3-input w3-border w3-border-primary" type="text" id="titel" name="titel" value="<?=$neuigkeit['titel']?>" required>
    </p>
    <p>
        <label class="" for="text">Text</label>
        <textarea required class="w3-input w3-border w3-border-primary" rows="10" id="text" name="text" onkeyup="woerter_zaehlen()" maxlength="500"><?=$neuigkeit['inhalt']?></textarea>
    </p>
    <div class="w3-border w3-border-primary">
        <div class="w3-panel">
            <h3>Bild ändern</h3>
            <p><i>Es können nur Bilder im <b>.jpg, .jpeg, .gif, .png</b> Format mit bis zu 11,9 Megabyte hochgeladen werden. Bilder werden webtauglich verarbeitet - exif-Daten der Bilder werden gelöscht.</i></p>
            <p>
                <input class="w3-button w3-block w3-primary" type="file" name="jpgupload" id="jpgupload">
            </p>
            <p>
                <label for="delete_jpg">Bisheriges Bild löschen:</label> 
                <select class='w3-select w3-border w3-border-primary' id='delete_jpg' name='delete_jpg'>
                    <option value='Ja'>Bild löschen</option>
                    <option value='Nein' selected>Nein</option>
                </select>
            </p>
        </div>
        <div class="w3-panel">
            <h3>Dokument ändern</h3>
            <p><i>Es können nur Dateien im <b>.pdf / .xlsx</b> Format mit bis zu <b>3 MB</b> hochgeladen werden.</i></p>
            <p>
                <input class="w3-button w3-block w3-primary" type="file" name="pdfupload" id="pdfupload">
            </p>
            <p>
                <label for="delete_pdf">Bisheriges Dokument löschen:</label> 
                <select class='w3-select w3-border w3-border-primary' id='delete_pdf' name='delete_pdf'>
                    <option value='Ja'>Dokument löschen</option>
                    <option value='Nein' selected>Nein</option>
                </select>
            </p>
    </div>
    </div>
    <p>
        <input type="submit" class="w3-tertiary w3-block w3-button" id="submit" value="Neuigkeit bearbeiten">
    </p>
</form>

<!-- Neuigkeit löschen -->
<form method="post">
    <p>
        <input type="submit" class="w3-secondary w3-block w3-button" id="submit" name="delete_neuigkeit" value="Neuigkeiteintrag löschen">
    </p>
</form>
