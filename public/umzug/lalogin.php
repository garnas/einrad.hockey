<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/la_session.logic.php'; //Auth

// PHP-Mailer hinzufügen //QUELLE: https://www.html-seminar.de/forum/thread/6852-kontaktformular-tutorial/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../frameworks/phpmailer/src/Exception.php';
require_once '../../frameworks/phpmailer/src/PHPMailer.php';

$las =   array(
            array("larissa", "Larissa Barten", "larissa@einradhockeyliga.de", "2"),
            array("ansgar", "Ansgar Pölking", "ansgar.p@einradhockeyliga.de", "16"),
            array("philipp", "Philipp Gross", "larissa@einradhockeyliga.de", "12"),
            array("max", "Max Oles", "olesmaximilian@gmail.com", "16"),
            array("ole", "Ole Jaekel", "ole@einradhockeyliga.de", "27"),
            array("malte", "Malte Voelkel", "malte@einradhockeyliga.de","689")
        );

$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

foreach ($las as $la){
    $password = substr(str_shuffle($permitted_chars), 0, 7);
    
    
    //LigaLeitung::create_new_la($la[0],$la[1],$password,$la[2], $la[3]);
    
    
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
    . "\r\n\r\n" . "Passwort ändern:". Form::link("https://einrad.hockey/ligacenter/pw_aendern", "Link") . "</html>";
    /*if ($mailer->send()) {
        Form::affirm("Email wurde versendet");
    } else {
        Form::error("Es ist ein Fehler aufgetreten: Email wurde nicht versendet!");
    }*/
    db::debug($mailer);
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/footer.tmp.php';
