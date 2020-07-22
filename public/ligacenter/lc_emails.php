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
require_once '../../logic/emails.logic.php';

//Email verschicken

//db::sanitize verhindert, dass \r\n funktioniert.
//Hiermit werden die \r\n für newline-chars wieder escaped.
$_POST['text'] = stripcslashes($_POST['text'] ?? '');

if (isset($_POST['send_mail'])){
    $emails = $_POST['chosen_mails'];
    $betreff = $_POST['betreff'];
    $text = $_POST['text'];
    db::debug ($emails);
    if (empty($emails) or empty($betreff) or empty($text)){
        Form::error("Kontaktformular unvollständig");
    }else{
        //Mail an die Liga
        $mailer = new PHPMailer();
        $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
        $mailer->setFrom(Config::LAMAIL,'Ligaausschuss'); // Absenderemail und -name setzen
        //All_bcc ist gesetzt, wenn es sich um eine Rundmail handelt, und alle Mails ins BCC sollen.
        if (isset($_POST['all_bcc']) or count($emails) > 12){
            foreach ($emails as $email){
                $mailer->addBCC($email);
            }
            $mailer->addAddress(Config::LAMAIL);
        }else{
            foreach ($emails as $email){
                $mailer->addAddress($email);
            }
        }
        $mailer->addBCC(Config::LAMAIL_ANTWORT);
        $mailer->Subject = $betreff; // Betreff der Email
        $mailer->Body = $text . "\r\n\r\nVersendet via einrad.hockey";
        db::debug($mailer);
        /*if ($mailer->send()){
            Form::affirm("Email wurde versendet");
            header('Location: lc_emails.php');
            die();
        }else{
            Form::error("Es ist ein Fehler aufgetreten: Email wurde nicht versendet!");
        }*/
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/emails.tmp.php';
?>

<!-- Anzeige des Formulars für den Emailversand -->
<?php if (!empty($emails)){?>                	    
    <div class="w3-card-4 w3-panel">
        <h3>Kontaktformular</h3>
        <form method="post" onsubmit="return confirm('Soll die Email wirklich abgeschickt werden?')">
            <p><b>Absender:</b></p>
            <p><?=Config::LAMAIL?></p>
            <p><b>
                <?php if (isset($_POST['rundmail']) or count($emails) > 12){?> 
                    BCC <input type="hidden" name="all_bcc" value="all_bcc"> 
                <?php }else{?> 
                    Empfänger 
                <?php } //end if?>
                (<?=count($emails ?? array())?>):
            </b></p>
            <p>
                <?php foreach (($emails ?? array()) as $email){?>
                    <?=$email?><br>
                    <input type="hidden" name="chosen_mails[]" value="<?=$email?>">
                <?php } //end if?>
            </p>
            <p><b>+ BCC:</b></p>
            <p><?=Config::LAMAIL_ANTWORT?></p>
            <p>
                <label class="w3-text-primary" for="betreff">Betreff</label>
                <input class="w3-input w3-border w3-border-primary" type="text" id="betreff" name="betreff" value="<?=$_POST['betreff'] ?? ''?>" required>
            </p>
            <p>
                <label class="w3-text-primary" for="text">Text</label>
                <textarea class="w3-input w3-border w3-border-primary" rows="10" type="text" id="text" name="text" required><?=stripcslashes($_POST['text'] ?? '')?></textarea>
            </p>
            <p>
                <input type="submit" class="w3-secondary w3-block w3-button" name="send_mail" value="Senden">
            </p>
        </form>
    </div>
<?php } //end if

include '../../templates/footer.tmp.php';