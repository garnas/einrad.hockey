<?php

// Inititalisierung

$team_id = (Helper::$teamcenter) ? $_SESSION['logins']['team']['id'] : (int) @$_GET['team_id'];
if (Team::is_ligateam($team_id)){
    $team = new Team ($team_id);
    $kontakte = new Kontakt ($team->id);
    $emails = $kontakte->get_emails_with_details();
}

$path = (Helper::$ligacenter) ? '../ligacenter/lc_teamdaten_aendern.php?team_id=' . $team_id : '../teamcenter/tc_teamdaten_aendern.php';



// Trikotfarben verwalten
if (
    isset($_POST['color_1'])
    || isset($_POST['color_2'])
    || isset($_POST['no_color_2'])
    || isset($_POST['no_color_1'])
) {
    if (isset($_POST['color_1'])) {
        if (preg_match('/^#[a-f0-9]{6}$/i', $_POST['color_1'])) {
            $team->set_detail('trikot_farbe_1', $_POST['color_1']);
        } else {
            Html::error("Es konnte keine Farbe fürs erste Trikot ermittelt werden.");
        }
    }
    if (isset($_POST['color_2'])) {
        if (preg_match('/^#[a-f0-9]{6}$/i', $_POST['color_2'])) {
            $team->set_detail('trikot_farbe_2', $_POST['color_2']);
        } else {
            Html::error("Es konnte keine Farbe fürs zweite Trikot ermittelt werden.");
        }
    }
    if (isset($_POST['no_color_1']))
        $team->set_detail('trikot_farbe_1', '');
    if (isset($_POST['no_color_2']))
        $team->set_detail('trikot_farbe_2', '');
    Html::info("Trikotfarbe geändert.");
    header("Location:" . $path);
    die();
}

// Teamdaten ändern
if (isset($_POST['teamdaten_aendern'])) {

    $error = false;
    if (empty($_POST['ligavertreter'])) {
        Html::error('Bitte gebt einen Ligavertreter an.');
        $error = true;
    }
    if (empty($_POST['dsgvo'])) {
        Html::error('Der Ligavertreter hat noch nicht den Datenschutz-Hinweisen zugestimmt.');
        $error = true;
    }

    if (!$error) {
        $array = ['plz', 'ort', 'verein', 'homepage', 'ligavertreter'];
        foreach ($array as $entry) {
            if ($team->details[$entry] != $_POST[$entry]) {
                $team->set_detail($entry, $_POST[$entry]);
            }
        }
    }

    // Emails
    foreach ($emails as $email) {
        if ($email['public'] != ($_POST['public' . $email['teams_kontakt_id']] ?? '')) { //TODO in Array Umwandeln
            $kontakte->set_public($email['teams_kontakt_id'], $_POST['public' . $email['teams_kontakt_id']]);
        }
        if ($email['get_info_mail'] != ($_POST['info' . $email['teams_kontakt_id']] ?? '')) {
            $kontakte->set_info($email['teams_kontakt_id'], $_POST['info' . $email['teams_kontakt_id']]);
        }
        if ("Ja" == ($_POST['delete' . $email['teams_kontakt_id']]) ?? '') {
            if ($kontakte->delete_email($email['teams_kontakt_id'])) {
                Html::info($email['email'] . " wurde gelöscht");
            } else {
                Html::error("Es muss mindestens eine E-Mail-Adresse hinterlegt sein");
            }
        }
    }
    Html::info("Teamdaten wurden gespeichert.");
    header('Location: ' . $path);
    die();
}

// Verarbeitung des Formulars zum Eintragen einer Email
if (isset($_POST['neue_email'])) {
    $email = $_POST['email'];
    $infomail = $_POST['get_info_mail'];
    $public = $_POST['public'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $kontakte->set_email($email, $public, $infomail);
        Html::info("E-Mail-Adresse wurde hinzugefügt");
        header('Location: ' . $path);
        die();
    }

    Html::error("E-Mail-Adresse wurde nicht akzeptiert");
}

// Teamfoto hochladen
if (isset($_POST['teamfoto']) && !empty($_FILES["jpgupload"]["tmp_name"])) {
    // Bild wird hochgeladen, target_file_jpg = false, falls fehlgeschlagen.
    $target_file_jpg = Neuigkeit::upload_bild($_FILES["jpgupload"]);

    if ($target_file_jpg !== false) {
        $team->set_detail('teamfoto', $target_file_jpg);
        Html::info("Teamfoto wurde hochgeladen.");
        header('Location: ' . $path);
        die();
    }

    Html::error("Es ist ein Fehler beim Fotoupload aufgetreten.");
}

// Teamfoto löschen
if (isset($_POST['delete_teamfoto'])) {
    $team->delete_foto();
    Html::info("Teamfoto wurde gelöscht.");
    header('Location: ' . $path);
    die();
}