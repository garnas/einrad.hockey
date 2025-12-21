<?php
// Autor festlegen - Config::$ligacenter und Html::$teamcenter werden als boolean in session_la.logic.php bzw session_team.logic.php festgelegt
// Dadurch weiß man, ob vom Teamcenter oder vom Ligacenter auf dieses Skript zugegriffen wird
// Dies ermöglicht den gleichzeitgen Login von Ligaausschuss und Ligateams in einem Browser

// Autor
if (Helper::$ligacenter) {
    $name = "Ligaausschuss";
} elseif (Helper::$team_social_media) {
    $name = "Team Social Media";
} else {
    $name = $_SESSION['logins']['team']['name'];
}

$error = false;

// Formularauswertung
if (isset($_POST['create_neuigkeit'])) {

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
        $target_file_jpg = '';
    }
    
    // Dokument
    if (!empty($_FILES["pdfupload"]["tmp_name"])) {
        $target_file_pdf = Neuigkeit::upload_dokument($_FILES["pdfupload"]);
        if (!$target_file_pdf) {
            $error = true;
        }
    } else {
        $target_file_pdf = '';
    }

    // Zeitpunkt
    $zeitpunkt = Neuigkeit::darf_datum_festlegen() ? date('Y-m-d H:i', strtotime($_POST['zeitpunkt'])) : date('Y-m-d H:i');

    if ($error) {
        // evtl hochgeladene Dateien löschen.
        if (($target_file_pdf ?? false) !== false) unlink($target_file_pdf);
        if (($target_file_jpg ?? false) !== false) unlink($target_file_jpg);
        Html::error("Neuigkeit wurde nicht erstellt, hochgeladene Dateien müssen erneut hochgeladen werden.");
    } else {
        Neuigkeit::create($titel, $text, $name, $zeitpunkt, $target_file_jpg, $target_file_pdf, $bild_verlinken);
        Html::info("Deine Neuigkeit wurde erfolgreich eingetragen");
        header('Location: ../liga/neues.php');
        die(); // Damit das Skript nicht zu auf dem Server zu ende ausgeführt wird.
    }
}