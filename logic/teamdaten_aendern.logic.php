<?php
if ($ligacenter){
    $get_var = '?team_id=' . $daten['team_id'];
}else{
    $get_var = '';
}

if(isset($_POST['change'])) {
    $array = array('plz','ort','verein','homepage', 'ligavertreter');
    $error = false;
    if (empty($_POST['ligavertreter'])) {
        Form::error('Es muss ein Ligavertreter angegeben werden');
        $error = true;
    }
    if (empty($_POST['dsgvo'])){
        Form::error('Der Ligavertreter muss den Datenschutz-Hinweisen zustimmen.');
        $error = true;
    }

    // Teamdetails
    if (!$error){
        foreach ($array as $entry) {
            if ($daten[$entry] != $_POST[$entry]) {
                $akt_team->set_team_detail($entry, $_POST[$entry]);
                $change = true;
            }
        }
    }

    // Emails
    foreach($akt_team_kontakte->get_emails_with_details() as $email){
        if ($email['public'] != ($_POST['public'.$email['teams_kontakt_id']]  ?? '')) {
            $akt_team_kontakte->set_public($email['teams_kontakt_id'],$_POST['public'.$email['teams_kontakt_id']]);
            $change = true;
        }
        if ($email['get_info_mail'] != ($_POST['info'.$email['teams_kontakt_id']] ?? '')) {
            $akt_team_kontakte->set_info($email['teams_kontakt_id'],$_POST['info'.$email['teams_kontakt_id']]);
            $change = true;
        }
        if ("Ja" == ($_POST['delete'.$email['teams_kontakt_id']]) ?? '') {
            if ($akt_team_kontakte->delete_email($email['teams_kontakt_id'])){
                Form::affirm($email['email'] . " wurde gelöscht");
                $change = true;
            }else{
                Form::error("Es muss mindestens eine E-Mail-Adresse hinterlegt sein");
            }
        }
    }
    if ($change ?? false){
        Form::affirm("Teamdaten wurden geändert.");
        header('Location: ' . db::escape($_SERVER['PHP_SELF']) . $get_var);
        die();
    }
}

//Verarbeitung des Formulars zum Eintragen einer Email
if(isset($_POST['neue_email'])) {
    $email = $_POST['email'];
    $infomail = $_POST['get_info_mail'];
    $public = $_POST['public'];

    if(filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $akt_team_kontakte->create_new_team_kontakt ($email,$public,$infomail);
        Form::affirm("E-Mail-Adresse wurde hinzugefügt");
        header('Location: ' . db::escape($_SERVER['PHP_SELF']) . $get_var);
        die();
    }else{
        Form::error("E-Mail-Adresse wurde nicht akzeptiert");
    }
}

//Teamfoto hochladen
if (isset($_POST['teamfoto'])){
    if (!empty($_FILES["jpgupload"]["tmp_name"])){
        //Bild wird hochgeladen, target_file_jpg = false, falls fehlgeschlagen.
        $target_file_jpg = Neuigkeit::upload_image($_FILES["jpgupload"]);
        if($target_file_jpg === false){
            Form::error("Fehler beim Fotoupload");
        }else{
            $akt_team->set_teamfoto($target_file_jpg);
            Form::affirm("Teamfoto wurde hochgeladen");
            header('Location: ' . db::escape($_SERVER['PHP_SELF']) . $get_var);
            die();
        }
    }
}

//Teamfoto löschen
if (isset($_POST['delete_teamfoto'])){
    $akt_team->delete_teamfoto($daten['teamfoto']);
    Form::affirm("Teamfoto wurde gelöscht");
    header('Location: ' . db::escape($_SERVER['PHP_SELF']) . $get_var);
    die();
}