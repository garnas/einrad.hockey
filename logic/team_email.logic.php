<?php
//db::sanitize verhindert, dass \r\n funktioniert.
//Hiermit werden die \r\n für newline-chars wieder escaped.
$_POST['text'] = stripcslashes($_POST['text'] ?? '');

if (isset($_POST['send_mail'])){
    $emails = $_POST['chosen_mails'];
    $betreff = $_POST['betreff'];
    $text = $_POST['text'];
    if (empty($emails) or empty($betreff) or empty($text)){
        Form::error("Kontaktformular unvollständig");
    }else{
        //Mail an die Liga
        $mailer = new PHPMailer();
        $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
        $mailer->setFrom(Config::LAMAIL,'Ligaausschuss'); // Absenderemail und -name setzen
        //All_bcc ist gesetzt, wenn es sich um eine Rundmail handelt, und alle Mails ins BCC sollen.
        if (isset($_POST['all_bcc'])){
            foreach ($emails as $email){
                $mailer->addBCC($email);
            }
            $mailer->addAddress(Config::LAMAIL);
        }else{
            foreach ($emails as $email){
                $mailer->addAddress($email);
            }
            $mailer->addCC(Config::LAMAIL, 'Ligaausschuss');
        }
        $mailer->addBCC(Config::LAMAIL_ANTWORT);
        $mailer->Subject = $betreff; // Betreff der Email
        $mailer->Body = $text . "\r\nVersendet mit dem Kontaktformular";
        db::debug($mailer);
        /*if ($mailer->send()){
            Form::affirm("Email wurde versendet");
        }else{
            Form::error("Es ist ein Fehler aufgetreten: Email wurde nicht versendet!");
        }*/
    }
}
Form::affirm("Die Mails werden noch nicht tatsächlich versendet.");