<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/team_session.logic.php'; //Auth
// PHP-Mailer hinzufügen //QUELLE: https://www.html-seminar.de/forum/thread/6852-kontaktformular-tutorial/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../frameworks/phpmailer/src/Exception.php';
require_once '../../frameworks/phpmailer/src/PHPMailer.php';
require_once '../../logic/emails.logic.php';

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
        $akt_kontakt = new Kontakt($_SESSION['team_id']);
        $absender = $akt_kontakt->get_emails();
        foreach($absender as $email){
            $mailer->AddReplyTo($email, $_SESSION['teamname']);
        }
        $mailer->setFrom("noreply@einrad.hockey", $_SESSION['teamname']); // Absenderemail und -name setzen
        //All_bcc ist gesetzt, wenn es sich um eine Rundmail handelt, und alle Mails ins BCC sollen.
        if (isset($_POST['all_bcc'])){
            foreach ($emails as $email){
                $mailer->addBCC($email);
            }
            $mailer->addAddress(Config::LAMAIL);
        }else{
            if (count($emails) < 13){
                foreach ($emails as $email){
                    $mailer->addAddress($email);
                }
            }else{
                foreach ($emails as $email){
                    $mailer->addBCC($email);
                }
                $mailer->addAddress("noreply@einrad.hockey", "Rundmail");
            }
            
        }
        $mailer->Subject = $betreff; // Betreff der Email
        $mailer->Body = $text . "\r\nVersendet mit dem Kontaktformular";
        db::debug($mailer);
        /*if ($mailer->send()){
            Form::affirm("Email wurde versendet");
        }else{
            Form::error("Es ist ein Fehler aufgetreten: Email wurde nicht versendet!");
        }*/
    }
    Form::affirm(htmlentities($text));
}

Form::affirm("Die Mails werden noch nicht tatsächlich versendet.");
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = 'Kontaktcenter | ' . $_SESSION['teamname'];
include '../../templates/header.tmp.php';
include '../../templates/emails.tmp.php';
?>
<!-- Anzeige des Formulars für den Emailversand -->
<?php if (!empty($emails)){ ?>                	    
    <div class="w3-card-4 w3-panel">
        <h3>Kontaktformular</h3>
        <form method="post" onsubmit="return confirm('Soll die Email wirklich abgeschickt werden?')">
            <p><b>Absender:</b></p>
            <p><?=$_SESSION['teamname']?></p>
            <p>
                <b>
                    <?php if (isset($_POST['rundmail']) or count($emails) > 12){?>
                        BCC <input type="hidden" name="all_bcc" value="all_bcc">
                    <?php }else{?> 
                        Empfänger
                    <?php } //end if?>
                    (<?=count($teamnamen ?? array())?>):
                </b>
            </p>
                <?php foreach (($emails ?? array()) as $email){?>
                    <input type="hidden" name="chosen_mails[]" value="<?=$email?>">
                <?php } //end if?>
            <p>
                <?php foreach (($teamnamen ?? array()) as $teamname){?>
                    <?=$teamname?><br>
                <?php } //end if?>
            </p>
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