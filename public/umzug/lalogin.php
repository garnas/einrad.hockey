<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

// PHP-Mailer hinzufügen //QUELLE: https://www.html-seminar.de/forum/thread/6852-kontaktformular-tutorial/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../frameworks/phpmailer/src/Exception.php';
require_once '../../frameworks/phpmailer/src/PHPMailer.php';

$las =   array(
            array("larissa", "Larissa Barten", "larissa@einrad.hockey", "2"),
            array("ansgar", "Ansgar Pölking", "ansgar.p@einrad.hockey", "16"),
            array("philipp", "Philipp Gross", "larissa@einrad.hockey", "12"),
            array("max", "Max Oles", "max@einrad.hockey", "16"),
            array("ole", "Ole Jaekel", "ole@einrad.hockey", "27"),
            array("malte", "Malte Voelkel", "malte@einrad.hockey","689")
        );

$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

foreach ($las as $la){
    $password = substr(str_shuffle($permitted_chars), 0, 7);
    
    LigaLeitung::create_new_la($la[0],$la[1],$password,$la[2], $la[3]);
    
    $wort = explode(" ", $la[1]);
    $la[1] = $wort[0];
    //Mail verschicken:
    $mailer = new PHPMailer();
    $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
    $mailer->isHTML(true);
    $mailer->setFrom(Config::TECHNIKMAIL,"Deutsche Einradhockeyliga"); // Absenderemail und -name setzen
    $mailer->addAddress("merkmich@gmail.com");
     //Empfängeradresse
    $mailer->Subject = "Passwort Ligacenter"; //Betreff der Email
    $mailer->AltBody = "Benutzername: " . $la[0] . "\r\nPasswort: " . $password
    . "\r\n\r\n" . "Bitte ändere dein Passwort nach deinem ersten Login.";
    $mailer->Body = "<html>Benutzername: " . $la[0] . "\r\nLogin: " . $password
    . "\r\n\r\n" . "<a href='https://einrad.hockey/ligacenter/pw_aendern'>Passwort ändern</a></html>";
    db::debug($mailer);
    /*if ($mailer->send()) {
        Form::affirm("Email wurde versendet");
    } else {
        Form::error("Es ist ein Fehler aufgetreten: Email wurde nicht versendet!");
    }*/
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/footer.tmp.php';