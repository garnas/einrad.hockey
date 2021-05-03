<!-- Neuigkeit eintragen -->
<h3>Neuigkeit eintragen</h3>
<p id="counter"><i>Es dürfen 750 Zeichen verwendet werden. Für mehr Infos kannst du ein Bild und/oder ein PDF hochladen.</i><p>
<form action="" method="post" enctype="multipart/form-data">
    <p>
        <label class="" for="titel">Titel</label>
        <input required class="w3-input w3-border w3-border-primary" type="text" id="titel" name="titel" value="<?=$_POST['titel'] ?? ''?>" >
    </p><p>
        <label class="" for="text">Text</label>
        <textarea required class="w3-input w3-border w3-border-primary" rows="10" id="text" name="text" onkeyup="woerter_zaehlen(750)" maxlength="750"><?=stripcslashes($_POST['text'] ?? '')?></textarea>
    </p>
    <div class="w3-border w3-border-primary">
        <div class="w3-panel">
            <h3>Bild einfügen</h3>
            <p><i>Es können nur Bilder im <b>.jpg, .jpeg, .gif, .png</b> Format mit bis zu 11,9 Megabyte hochgeladen werden. Bilder werden webtauglich verarbeitet - exif-Daten der Bilder werden gelöscht.</i></p>
            <p>
                <input class="w3-button w3-block w3-primary" type="file" name="jpgupload" id="jpgupload">
            </p>
            <?php if(Helper::$ligacenter){?>
                <p>
                    <label class="" for="bild_verlinken">Bild verlinken (optional) | nur Ligaausschuss</label>
                    <input class="w3-input w3-border w3-border-primary" placeholder="Link angeben" type="url" id="bild_verlinken" name="bild_verlinken" value="<?=$_POST['bild_verlinken'] ?? ''?>" >
                </p>
            <?php } //endif?>
        </div>
        <div class="w3-panel">
            <h3>Dokument anhängen</h3>
            <p><i>Es können nur Dateien im <b>.pdf / .xlsx</b> Format mit bis zu <b>3 MB</b> hochgeladen werden.</i></p>
            <p>
                <input class="w3-button w3-block w3-primary" type="file" name="pdfupload" id="pdfupload">
            </p>
        </div>
    </div>
    <p>
        <input type="submit" class="w3-secondary w3-block w3-button" id="submit" name="create_neuigkeit" value="Neuigkeit eintragen">
    </p>
</form>
