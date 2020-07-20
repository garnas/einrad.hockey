<?php
if(isset($_POST['change'])) {
    $array = array('plz','ort','verein','homepage');

    if (empty($_POST['ligavertreter'])) {
        Form::error('Es muss ein Ligavertreter angegeben werden');
    }else{
        array_push($array, 'ligavertreter'); //Ligavertreter wird zum Array für set_team_detail hinzugefügt
    }
    
    //teamdetails
    foreach ($array as $entry) {
        if ($daten[$entry] != $_POST[$entry]) {
            $akt_team->set_team_detail($entry, $_POST[$entry]);
            $change = true;
            $aff = true;
        }
    }
    if(($aff ?? false) == true){Form::affirm("Teamdaten wurden verändert");}

    //emails
    foreach($akt_team_kontakte->get_all_emails() as $email){
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
}

//Verarbeitung des Formulars zum Eintragen einer Email
if(isset($_POST['neue_email'])) {
    $email = $_POST['email'];
    $infomail = $_POST['get_info_mail'];
    $public = $_POST['public'];

    if(filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $akt_team_kontakte->create_new_team_kontakt ($email,$public,$infomail);
        Form::affirm("Email wurde hinzugefügt");
        $change = true;
    }else{
        Form::error("E-Mail-Adresse wurde nicht akzeptiert");
    }
}

//Weiterleitung, falls etwas geändert wurde
if (isset($_POST['neue_email']) or isset($_POST['change'])){
    if ($change ?? false){
        if ($ligacenter){
            header('Location: lc_teamdaten.php?team_id=' . $team_id);
            die();
        }elseif($teamcenter){
            header('Location: tc_teamdaten.php');
            die();
        }
    }else{
        Form::error('Es wurden keine Daten verändert');
    }
}

//Teamfoto hochladen
if (isset($_POST['teamfoto'])){
    if (!empty($_FILES["jpgupload"]["tmp_name"])){  
        //Bild wird hochgeladen, target_file_jpg = false, falls fehlgeschlagen.
        $target_file_jpg = Neuigkeit::upload_image($_FILES["jpgupload"]);
        if($target_file_jpg === false){
            Form::error("Fehler beim Fotoupload");
            header('Location: ' . db::escape($_SERVER['PHP_SELF']));
            die();
        }else{
            $akt_team->teamfoto($target_file_jpg);
            Form::affirm("Teamfoto wurde hochgeladen");
            header('Location: ' . db::escape($_SERVER['PHP_SELF']));
            die();
        }
    }
}

//Teamfoto löschen
if (isset($_POST['delete_teamfoto'])){
    $akt_team->delete_teamfoto($daten['teamfoto']);
    Form::affirm("Teamfoto wurde gelöscht");
    header('Location: ' . db::escape($_SERVER['PHP_SELF']));
    die();
}