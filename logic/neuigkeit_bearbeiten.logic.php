<?php
$neuigkeiten_id = $_GET['neuigkeiten_id'];
$neuigkeiten = Neuigkeit::get_neuigkeiten($neuigkeiten_id);
$error = false;
if (!isset($neuigkeiten[$neuigkeiten_id])) {
    Form::error("Der Neuigkeiteneintrag wurde nicht gefunden");
    header("Location: ../liga/neues.php");
    die();
}

$neuigkeit = $neuigkeiten[$neuigkeiten_id];

// Neuigkeit löschen
if (isset($_POST['delete_neuigkeit'])) {
    Neuigkeit::delete($neuigkeiten_id);
    Form::info("Neuigkeit wurde gelöscht");
    header('Location: ../liga/neues.php');
    die();
}

// Neuigkeiten verändern
if (isset($_POST['change_neuigkeit'])) {

    if (!empty($_POST['titel']) && !empty($_POST['text'])) {
        Form::error("Bitte Titel- und Textfeld ausfüllen.");
        $error = true;
    }

    // Fileupload
    // Bild
    if (!empty($_FILES["jpgupload"]["tmp_name"])) {

        // Bild wird hochgeladen, target_file_jpg = false, falls fehlgeschlagen.
        $target_file_jpg = Neuigkeit::upload_bild($_FILES["jpgupload"]);

        if ($target_file_jpg === false) {
            $error = true;
        }

    } else {
        // Wert aus der Datenbank wird übernommen
        $target_file_jpg = $neuigkeit['link_jpg'];
    }
    // Dokument
    if (!empty($_FILES["pdfupload"]["tmp_name"])) {
        // Bild wird hochgeladen, target_file_jpg = false, falls fehlgeschlagen.
        if ($target_file_pdf = Neuigkeit::upload_dokument($_FILES["pdfupload"])) {
            $error = true;
        }
    } else {
        $target_file_pdf = $neuigkeit['link_pdf'];
    }

    //////Titel, Text und Verlinkungen werden in die Datenbank eingetragen//////
    $titel = $_POST['titel'];
    $text = $_POST['text'];
    $bild_verlinken = $_POST['bild_verlinken'] ?? '';

    if (!$error) {

        // Bisheriges Bild bzw Dokument löschen, wenn dies ausgewählt wurde.
        // Bild
        if ($_POST['delete_jpg'] === 'Ja' && !empty($neuigkeit['link_jpg'])) {
            unlink($neuigkeit['link_jpg']);
            Form::info("Bild wurde gelöscht.");
            if ($neuigkeit['link_jpg'] === $target_file_jpg) {
                $target_file_jpg = '';
            }
        }
        // Dokument
        if ($_POST['delete_pdf'] === 'Ja' && !empty($neuigkeit['link_pdf'])) {
            unlink($neuigkeit['link_pdf']);
            Form::info("PDF wurde gelöscht.");
            if ($neuigkeit['link_pdf'] === $target_file_pdf) {
                $target_file_pdf = '';
            }
        }

        Neuigkeit::update_neuigkeit($neuigkeiten_id, $titel, $text, $target_file_jpg, $target_file_pdf, $bild_verlinken);
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
        Form::info("Die Neuigkeit wurde bearbeitet.");
        header('Location: ../liga/neues.php');
        die();
    }
    Form::error("Es wurden keine Änderungen vorgenommen."); // Fehler bei JPG oder PDF Upload
}