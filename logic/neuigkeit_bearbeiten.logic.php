<?php

use App\Service\Neuigkeit\PermissionService;
use App\Service\Neuigkeit\FileService;
use App\Repository\Neuigkeit\NeuigkeitRepository;

$neuigkeiten_id = (int) @$_GET['neuigkeiten_id'];
$neuigkeit = NeuigkeitRepository::get()->findById($neuigkeiten_id);

if (empty($neuigkeit)) {
    Helper::not_found("Neuigkeiteneintrag konnte nicht gefunden werden.");
}

if (!PermissionService::canEdit($neuigkeit->getEingetragenVon())) {
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
    $bild_verlinken = PermissionService::canEmbedLink() ? $_POST['bild_verlinken'] : '';
    if (!empty($_FILES["jpgupload"]["tmp_name"])) {
        $target_file_jpg = FileService::uploadImage($_FILES["jpgupload"]);
        if (!$target_file_jpg) {
            $error = true;
        }
    } else {
        $target_file_jpg = $neuigkeit->getLinkJpg();
    }
    
    // Dokument
    if (!empty($_FILES["pdfupload"]["tmp_name"])) {
        $target_file_pdf = FileService::uploadPDF($_FILES["pdfupload"]);
        if (!$target_file_pdf) {
            $error = true;
        }
    } else {
        $target_file_pdf = $neuigkeit->getLinkPdf();
    }

    $zeitpunkt = PermissionService::canSetTime() ? new Datetime($_POST['zeitpunkt']) : new DateTime('now');

    if ($error) {
        Html::error("Es wurden keine Änderungen vorgenommen. Fehler beim Hochladen der Dateien.");
        header('Location: ../liga/neues.php');
        die();
    }
    
    // Bild
    if ($_POST['delete_jpg'] === 'Ja' && !empty($neuigkeit->getLinkJpg())) {
        unlink($neuigkeit->getLinkJpg());
        Html::info("Bild wurde gelöscht.");
        if ($neuigkeit->getLinkJpg() === $target_file_jpg) {
            $target_file_jpg = '';
        }
    }

    // Dokument
    if ($_POST['delete_pdf'] === 'Ja' && !empty($neuigkeit->getLinkPdf())) {
        unlink($neuigkeit->getLinkPdf());
        Html::info("PDF wurde gelöscht.");
        if ($neuigkeit->getLinkPdf() === $target_file_pdf) {
            $target_file_pdf = '';
        }
    }
    
    // Altes Bild löschen
    if (
        $neuigkeit->getLinkJpg() !== $target_file_jpg
        && !empty($neuigkeit->getLinkJpg())
        && file_exists($neuigkeit->getLinkJpg())
    ) {
        unlink($neuigkeit->getLinkJpg());
    }
    
    // Altes Dokument löschen
    if (
        $neuigkeit->getLinkPdf() !== $target_file_pdf
        && !empty($neuigkeit->getLinkPdf())
        && file_exists($neuigkeit->getLinkPdf())
    ) {
        unlink($neuigkeit->getLinkPdf());
    }

    $neuigkeit->setTitel($titel);
    $neuigkeit->setInhalt($text);
    $neuigkeit->setLinkPdf($target_file_pdf);
    $neuigkeit->setLinkJpg($target_file_jpg);
    $neuigkeit->setBildVerlinken($bild_verlinken);
    $neuigkeit->setZeit($zeitpunkt);
    NeuigkeitRepository::get()->update($neuigkeit);
        
    Html::info("Die Neuigkeit wurde bearbeitet.");
    header('Location: ../liga/neues.php');
    die();
}
