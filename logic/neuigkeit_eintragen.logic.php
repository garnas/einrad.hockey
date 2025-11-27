<?php
// Autor festlegen - Config::$ligacenter und Html::$teamcenter werden als boolean in session_la.logic.php bzw session_team.logic.php festgelegt
// Dadurch weiß man, ob vom Teamcenter oder vom Ligacenter auf dieses Skript zugegriffen wird
// Dies ermöglicht den gleichzeitgen Login von Ligaausschuss und Ligateams in einem Browser

use App\Entity\Sonstiges\Neuigkeit;
use App\Repository\Neuigkeit\NeuigkeitRepository;
use App\Enum\NeuigkeitArt;
use App\Service\Neuigkeit\PermissionService;
use App\Service\Neuigkeit\FileService;

// Autor
if (Helper::$ligacenter) {
    $name = "Ligaausschuss";
} elseif (Helper::$oeffentlichkeitsausschuss) {
    $name = "Öffentlichkeitsausschuss";
} else {
    $name = $_SESSION['logins']['team']['name'];
}

$error = false;

// Formularauswertung
if (isset($_POST['create_neuigkeit'])) {

    // Titel
    $titel = !empty($_POST['titel']) ? $_POST['titel'] : '';

    // Art
    $art = !empty($_POST['art']) ? NeuigkeitArt::from($_POST['art']) : NeuigkeitArt::NEUIGKEIT;
    
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
        $target_file_jpg = '';
    }
    
    // Dokument
    if (!empty($_FILES["pdfupload"]["tmp_name"])) {
        $target_file_pdf = FileService::uploadPDF($_FILES["pdfupload"]);
        if (!$target_file_pdf) {
            $error = true;
        }
    } else {
        $target_file_pdf = '';
    }

    // Zeitpunkt
    $zeitpunkt = PermissionService::canSetTime() ? new Datetime($_POST['zeitpunkt']) : new DateTime('now');

    if ($error) {
        // evtl hochgeladene Dateien löschen.
        if (($target_file_pdf ?? false) !== false) unlink($target_file_pdf);
        if (($target_file_jpg ?? false) !== false) unlink($target_file_jpg);
        Html::error("Neuigkeit wurde nicht erstellt, hochgeladene Dateien müssen erneut hochgeladen werden.");
    } else {
        
        $neuigkeit = new Neuigkeit();
        $neuigkeit->setTitel($titel);
        $neuigkeit->setArt($art);
        $neuigkeit->setInhalt($text);
        $neuigkeit->setLinkPdf($target_file_pdf);
        $neuigkeit->setLinkJpg($target_file_jpg);
        $neuigkeit->setBildVerlinken($bild_verlinken);
        $neuigkeit->setEingetragenVon($name);
        $neuigkeit->setZeit($zeitpunkt);
        NeuigkeitRepository::get()->create($neuigkeit);

        Html::info("Deine Neuigkeit wurde erfolgreich eingetragen");
        header('Location: ../liga/neues.php');
        die(); // Damit das Skript nicht zu auf dem Server zu ende ausgeführt wird.
    }
}