<?php
$neuigkeiten_id = (int) @$_GET['neuigkeiten_id'];
$neuigkeit = Neuigkeit::get_neuigkeit_by_id($neuigkeiten_id);

if (empty($neuigkeit)) {
    Helper::not_found("Neuigkeiteneintrag konnte nicht gefunden werden.");
}

if (!Neuigkeit::darf_bearbeiten($neuigkeit['eingetragen_von'])) {
    Html::error("Neuigkeit darf nicht bearbeitet werden.");
    Helper::reload("/liga/neues.php");
}

$error = false;

// Neuigkeiten verändern
if (isset($_POST['change_neuigkeit'])) {

    // Titel
    $titel = !empty($_POST['titel']) ? $_POST['titel'] : '';

    // Text
    $text = $_POST['text']; // ist required, daher kein isset nötig

    // Bild
    $bild_verlinken = Neuigkeit::darf_verlinken() ? $_POST['bild_verlinken'] : '';
    if (!empty($_FILES["jpgupload"]["tmp_name"])) {
        $target_file_jpg = Neuigkeit::upload_bild($_FILES["jpgupload"]);
        if (!$target_file_jpg) {
            $error = true;
        }
    } else {
        $target_file_jpg = $neuigkeit['link_jpg'];
    }
    
    // Dokument
    if (!empty($_FILES["pdfupload"]["tmp_name"])) {
        $target_file_pdf = Neuigkeit::upload_dokument($_FILES["pdfupload"]);
        if (!$target_file_pdf) {
            $error = true;
        }
    } else {
        $target_file_pdf = $neuigkeit['link_pdf'];
    }

    $zeitpunkt = Neuigkeit::darf_datum_festlegen() ? date('Y-m-d H:i', strtotime($_POST['zeitpunkt'])) : date('Y-m-d H:i');

    if ($error) {
        Html::error("Es wurden keine Änderungen vorgenommen. Fehler beim Hochladen der Dateien.");
        header('Location: ../liga/neues.php');
        die();
    }
    
    // Bild
    if ($_POST['delete_jpg'] === 'Ja' && !empty($neuigkeit['link_jpg'])) {
        unlink($neuigkeit['link_jpg']);
        Html::info("Bild wurde gelöscht.");
        if ($neuigkeit['link_jpg'] === $target_file_jpg) {
            $target_file_jpg = '';
        }
    }

    // Dokument
    if ($_POST['delete_pdf'] === 'Ja' && !empty($neuigkeit['link_pdf'])) {
        unlink($neuigkeit['link_pdf']);
        Html::info("PDF wurde gelöscht.");
        if ($neuigkeit['link_pdf'] === $target_file_pdf) {
            $target_file_pdf = '';
        }
    }
    
    // Altes Bild löschen
    if (
        $neuigkeit['link_jpg'] !== $target_file_jpg
        && !empty($neuigkeit['link_jpg'])
        && file_exists($neuigkeit['link_jpg'])
    ) {
        unlink($neuigkeit['link_jpg']);
    }
    
    // Altes Dokument löschen
    if (
        $neuigkeit['link_pdf'] !== $target_file_pdf
        && !empty($neuigkeit['link_pdf'])
        && file_exists($neuigkeit['link_pdf'])
    ) {
        unlink($neuigkeit['link_pdf']);
    }

    Neuigkeit::update_neuigkeit($neuigkeiten_id, $titel, $text, $zeitpunkt, $target_file_jpg, $target_file_pdf, $bild_verlinken);
    
    Html::info("Die Neuigkeit wurde bearbeitet.");
    header('Location: ../liga/neues.php');
    die();
}
