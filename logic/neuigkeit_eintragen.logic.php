<?php
// Autor festlegen - $ligacenter und $teamcenter werden als boolean in session_la.logic.php bzw session_team.logic.php festgelegt
// Dadurch weiß man, ob vom Teamcenter oder vom Ligacenter auf dieses Skript zugegriffen wird
// Dies ermöglicht den gleichzeitgen Login von Ligaausschuss und Ligateams in einem Browser

// Autor
$name = ($ligacenter) ? "Ligaausschuss" : $_SESSION['teamname'];


// Formularauswertung
if (isset($_POST['create_neuigkeit'])) {

    if (empty($_POST['titel']) or empty($_POST['text'])) {
        Form::error("Bitte Titel und Text eingeben.");
    } else {

        // Fileupload

        // Bild
        if (!empty($_FILES["jpgupload"]["tmp_name"])) {

            // Bild wird hochgeladen, target_file_jpg = false, falls fehlgeschlagen.
            $target_file_jpg = Neuigkeit::upload_bild($_FILES["jpgupload"]);

            if ($target_file_jpg === false) {
                $error = true;
            }

        } else {
            $target_file_jpg = '';
        }
        // Dokument
        if (!empty($_FILES["pdfupload"]["tmp_name"])) {

            // Bild wird hochgeladen, target_file_jpg = false, falls fehlgeschlagen.
            $target_file_pdf = Neuigkeit::upload_dokument($_FILES["pdfupload"]);
            if ($target_file_pdf === false) {
                $error = true;
            }

        } else {
            $target_file_pdf = '';
        }

        // Titel, Text und Verlinkungen werden in die Datenbank eingetragen//////
        $titel = $_POST['titel'];
        $text = $_POST['text'];
        $bild_verlinken = $_POST['bild_verlinken'] ?? '';

        if (!$error) {
            Neuigkeit::create_neuigkeit($titel, $text, $name, $target_file_jpg, $target_file_pdf, $bild_verlinken);
            Form::affirm("Deine Neuigkeit wurde erfolgreich eingetragen");
            header('Location: ../liga/neues.php');
            die(); // Damit das Skript nicht zu auf dem Server zu ende ausgeführt wird.
        } else {
            // evtl hochgeladene Dateien löschen.
            if (($target_file_pdf ?? false) !== false) unlink($target_file_pdf);
            if (($target_file_jpg ?? false) !== false) unlink($target_file_jpg);
            Form::error("Neuigkeit wurde nicht erstellt, eventuell hochgeladene Dateien müssen erneut hochgeladen werden.");
        }
    }
}