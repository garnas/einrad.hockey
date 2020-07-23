<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
// PHP-Mailer hinzufügen //QUELLE: https://www.html-seminar.de/forum/thread/6852-kontaktformular-tutorial/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../frameworks/phpmailer/src/Exception.php';
require_once '../../frameworks/phpmailer/src/PHPMailer.php';

//Formularauswertung

//Mailauswahl
require_once '../../logic/emails.logic.php';

//Mailversand
if (isset($_POST['send_mail'])){
    $emails = $_POST['chosen_mails'];
    $betreff = $_POST['betreff'];
    $text = stripcslashes($_POST['text']); //stripcslashes: \r\n für newline-chars wieder escaped.
    if (empty($emails) or empty($betreff) or empty($text)){
        Form::error("Kontaktformular unvollständig");
    }else{
        //Mail an die Liga
        $mailer = new PHPMailer();
        $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
        $akt_kontakt = new Kontakt($_SESSION['team_id']); //Absender Mails bekommen
        $absender = $akt_kontakt->get_emails();
        foreach($absender as $email){
            $mailer->AddReplyTo($email, $_SESSION['teamname']); //Antwort an den Absender
            $mailer->addBCC($email); //Als Kontroll-Email an den Absender
        }
        $mailer->setFrom("noreply@einrad.hockey", $_SESSION['teamname']); // Absenderemail und -name setzen

        //All_bcc ist gesetzt, wenn es sich um eine Rundmail handelt, und alle Mails ins BCC sollen.
        if (isset($_POST['all_bcc'])){
            foreach ($emails as $email){
                $mailer->addBCC($email);
            }
            $mailer->addAddress(Config::LAMAIL);
        }else{
            //Die Empfänger gehen ebenfalls in den BCC, wenn es sich um 13 oder mehr Email-Adressen handelt
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
        $mailer->Body = $text . "\r\n\r\nVersendet via einrad.hockey";
        db::debug($mailer);
        /*if ($mailer->send()){
            Form::affirm("Email wurde versendet");
            header('Location: tc_emails.php');
            die();
        }else{
            Form::error("Es ist ein Fehler aufgetreten: Email wurde nicht versendet!");
        }*/
    }
}

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