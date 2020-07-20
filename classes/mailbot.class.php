<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../frameworks/phpmailer/src/Exception.php';
require_once '../../frameworks/phpmailer/src/PHPMailer.php';

class MailBot {
    public static function add_mail($betreff, $inhalt ,$adressat, $absender = Config::LAMAIL, $cc = '', $bcc = '')
    {
        if (is_array($adressat)){
            $adressat=implode(",",$adressat); // in String umwandeln
        }
        $sql = "INSERT INTO mailbot (betreff, inhalt, adressat, absender, cc, bcc, mail_status)
                VALUES ('$betreff', '$inhalt', '$adressat', '$absender', '$cc', '$bcc', 'warte')";
        db::writedb($sql);
    }

    public static function mail_bot()
    {
        $sql = "SELECT * FROM mailbot WHERE mail_status = 'warte' ORDER BY zeit ASC";
        $result = db::readdb($sql);
        while ($mail = mysqli_fetch_assoc($result)){
            $mailer = new PHPMailer();
            $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
            $mailer->setFrom($mail['absender']); // Absenderemail und -name setzen
            $mailer->addAddress($mail['adressat']); // Empfängeradresse
            $mailer->addCC($mail['cc']);
            $mailer->addBCC($mail['bcc']);
            $mailer->Subject = $mail['betreff']; // Betreff der Email
            $mailer->Body = $mail['inhalt']; // Betreff der Email
            db::debug($mailer);
            if ($mailer->send()){
                self::set_status($mail['mail_id'], 'versendet');
            }else{
                self::set_status($mail['mail_id'], 'Fehler', $mailer->ErrorInfo);
            }
        }
    }
    public static function set_status($mail_id, $mail_status, $fehler = '')
    {
        if (empty($fehler)){
            $sql = "UPDATE mailbot SET mail_status = '$mail_status', zeit = zeit WHERE mail_id = '$mail_id'";
        }else{
            $sql = "UPDATE mailbot SET mail_status = '$mail_status', zeit = zeit, fehler = '$fehler' WHERE mail_id = '$mail_id'";
        }
    }

    public static function send_fehler()
    {
        $sql = "SELECT * FROM mailbot WHERE mail_status = 'fehler' ORDER BY zeit ASC";
        $result = db::readdb($sql);
        $inhalt = '';
        if($result->num_rows != 0){
            $mail_ids = array();
            while ($mail = mysqli_fetch_assoc($result)){
                array_push($mail_ids, $mail['mail_id']);
                $inhalt .= "\r\nBetreff:" . $mail['betreff'] . '\r\n' . $mail['adressat'] . '\r\n' .$mail['fehler'];
            }
            $inhalt = stripcslashes($inhalt);
            $mailer = new PHPMailer();
            $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
            $mailer->setFrom('noreply@einrad.hockey'); // Absenderemail und -name setzen
            $mailer->addAddress(Config::LAMAIL); // Empfängeradresse
            $mailer->Subject = "Mailbot: Mails konnten nicht gesendet werden"; // Betreff der Email
            $mailer->Body = $inhalt; // Betreff der Email
            db::debug($mailer);

            /*if ($mailer->send()){
                foreach ($mail_ids as $mail_id){
                    self::set_status($mail_id, 'nicht versendet');
                }
            }*/
        }
    }

    public static function warning_mail()
    {
        $sql = "SELECT count(*) FROM mailbot WHERE mail_status = 'fehler'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        if ($result['count(*)'] > 0){
            Form::attention("Der Mailbot kann manche Mails nicht versenden - siehe Datenbank.");
        }
    }
}