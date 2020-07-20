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

$team_ids = Team::get_all_teamids();
$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

foreach ($team_ids as $team_id){
    $akt_team = new Team ($team_id);
    $kontakt = new Kontakt ($team_id);
    $emails = $kontakt->get_emails();
    $teamname = Team::teamid_to_teamname($team_id);
    $password = substr(str_shuffle($permitted_chars), 0, 7);

    $akt_team->set_passwort($password, 'Nein');

    //Mail verschicken:
    $mailer = new PHPMailer();
    $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
    $mailer->setFrom(Config::TECHNIKMAIL,"Deutsche Einradhockeyliga"); // Absenderemail und -name setzen
    foreach ($emails as $email){
        $mailer->addAddress($email);
    }
     //Empfängeradresse
    $mailer->isHTML(true);
    $mailer->Subject = "Teamcenter: Neues Passwort"; //Betreff der Email
    $mailer->AltBody = nl2br("<html>Hallo " . $teamname . ",\r\n" . "euer neues Passwort für das Teamcenter der Einradhockeyliga lautet:\r\n\r\n" . $password
    . "\r\n" . "Bitte ändert dieses nach Eurem ersten <a href=\"https://einrad.hockey/teamcenter/tc_passwort_aendern\">Login</a>" . "\r\n" . "Euer Technikausschuss\r\n" . Form::mailto(Config::TECHNIKMAIL)) . "</html>";
    $mailer->Body = "Hallo " . $teamname . ",\r\n" . "euer neues Passwort für das Teamcenter der Einradhockeyliga lautet:\r\n" . $password
    . "\r\n" . "Bitte ändert dieses nach Eurem ersten Login auf www.einradhockeyliga.de." . "\r\n" . "Euer Technikausschuss\r\n" . Config::TECHNIKMAIL;
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
