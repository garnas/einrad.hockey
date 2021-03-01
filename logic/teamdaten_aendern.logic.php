<?php

// Inititalisierung

$team_id = (Config::$teamcenter) ? $_SESSION['logins']['team']['id'] : (int) @$_GET['team_id'];
if (Team::is_ligateam($team_id)){
    $team = new Team ($team_id);
    $kontakte = new Kontakt ($team->id);
    $emails = $kontakte->get_emails_with_details();
}

$path = (Config::$ligacenter) ? '../ligacenter/lc_teamdaten_aendern.php?team_id=' . $team_id : '../teamcenter/tc_teamdaten_aendern.php';



// Trikotfarben verwalten
if (
    isset($_POST['color_1'])
    || isset($_POST['color_2'])
    || isset($_POST['no_color_2'])
    || isset($_POST['no_color_1'])
) {
    if (isset($_POST['color_1']))
        $team->set_detail('trikot_farbe_1', $_POST['color_1']);
    if (isset($_POST['color_2']))
        $team->set_detail('trikot_farbe_2', $_POST['color_2']);
    if (isset($_POST['no_color_1']))
        $team->set_detail('trikot_farbe_1', '');
    if (isset($_POST['no_color_2']))
        $team->set_detail('trikot_farbe_2', '');
    Form::info("Trikotfarbe geändert.");
    header("Location:" . $path);
    die();
}

// Teamdaten ändern
if (isset($_POST['teamdaten_aendern'])) {

    $error = false;
    if (empty($_POST['ligavertreter'])) {
        Form::error('Es muss ein Ligavertreter angegeben werden');
        $error = true;
    }
    if (empty($_POST['dsgvo'])) {
        Form::error('Der Ligavertreter muss den Datenschutz-Hinweisen zustimmen.');
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
                Form::info($email['email'] . " wurde gelöscht");
            } else {
                Form::error("Es muss mindestens eine E-Mail-Adresse hinterlegt sein");
            }
        }
    }
    Form::info("Teamdaten wurden gespeichert.");
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
        Form::info("E-Mail-Adresse wurde hinzugefügt");
        header('Location: ' . $path);
        die();
    } else {
        Form::error("E-Mail-Adresse wurde nicht akzeptiert");
    }
}

// Teamfoto hochladen
if (isset($_POST['teamfoto'])) {
    if (!empty($_FILES["jpgupload"]["tmp_name"])) {
        //Bild wird hochgeladen, target_file_jpg = false, falls fehlgeschlagen.
        $target_file_jpg = Neuigkeit::upload_bild($_FILES["jpgupload"]);
        if ($target_file_jpg === false) {
            Form::error("Fehler beim Fotoupload");
        } else {
            $team->set_detail('teamfoto', $target_file_jpg);
            Form::info("Teamfoto wurde hochgeladen");
            header('Location: ' . $path);
            die();
        }
    }
}

// Teamfoto löschen
if (isset($_POST['delete_teamfoto'])) {
    $team->delete_foto();
    Form::info("Teamfoto wurde gelöscht");
    header('Location: ' . $path);
    die();
}