<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
// PHP-Mailer hinzufügen //QUELLE: https://www.html-seminar.de/forum/thread/6852-kontaktformular-tutorial/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../frameworks/phpmailer/src/Exception.php';
require_once '../../frameworks/phpmailer/src/PHPMailer.php';

//Formularauswertung
$error = false;
$send = false;
if(isset($_POST['absender'])) {
    $absender = $_POST['absender'];
    $name = $_POST['name'];
    $betreff = $_POST['betreff'];
    $text = stripcslashes($_POST['text']); //Revidiere die Sql-Sanitation von db::sanitize 

    if(empty($absender) or empty($betreff) or empty($text)){
        Form::error("Bitte Formular ausfüllen");
        $error = true;
    }
    
    if(!$error){
        //Mail an die Liga
        $mailer = new PHPMailer();
        $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
        $mailer->setFrom($absender,$name); // Absenderemail und -name setzen
        $mailer->addAddress(Config::LAMAIL); // Empfängeradresse
        $mailer->Subject = 'Kontaktformular: ' . $betreff; // Betreff der Email
        $mailer->Body = $text;
        db::debug($mailer);
        /*if ($mailer->send()) {
            Form::affirm("Email wurde versendet");
            $send = true; //Email an den User nur schicken, wenn die Mail an LA rausging
        } else {
            Form::error("Es ist ein Fehler aufgetreten: Email wurde nicht versendet!");
        }*/
        
        if ($send){
            //Mail an die angegebene Absendeadresse
            $mailer = new PHPMailer();
            $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
            $mailer->setFrom(Config::LAMAIL); // Absenderemail und -name setzen
            $mailer->addAddress($_POST['absender'],$_POST['name']); // Empfängeradresse
            $mailer->Subject = 'Kontaktformular: ' . $_POST['betreff']; // Betreff der Email
            $mailer->Body = "Danke für deine Mail! Du hast uns folgendes gesendet:\r\n\r\n" . $_POST['text'];
            db::debug($mailer);
            /*if ($mailer->send()) {
                Form::affirm("Es wurde eine Kopie an $absender gesendet.");
                header('Location: ../liga/ligaleitung.php');
                die();
            } else {
                Form::error("Es ist ein Fehler aufgetreten: Eine Kopie wurde nicht an dich versendet! Stimmt \"$absender\"?");
            }*/
        }//send
    }//error
}//Form

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Kontakt | Deutsche Einradhockeyliga";
$content = "Kontaktformular der Deutschen Einradhockeyliga";
include '../../templates/header.tmp.php';
?>

<div class="w3-card w3-panel">
    <h1 class="w3-text-primary">Kontaktformular</h1>
    <p><span class="w3-text-grey">Empfänger</span><br><?=Form::mailto(Config::LAMAIL)?></p>
    <form method="post">
        <p>
            <label class="w3-text-grey" for="name">Dein Name</label>
            <input class="w3-input w3-border w3-border-primary" type="text" id="name" name="name" value="<?=$_POST['name'] ?? ''?>" required>
        </p><p>
            <label class="w3-text-grey" for="absender">Deine Email</label>
            <input class="w3-input w3-border w3-border-primary" type="email" id="absender" name="absender" value="<?=$_POST['absender'] ?? ''?>" required>
        </p><p>
            <label class="w3-text-grey" for="betreff">Betreff</label>
            <input class="w3-input w3-border w3-border-primary" type="text" id="betreff" name="betreff" value="<?=$_POST['betreff'] ?? ''?>" required>
        </p><p>
            <label class="w3-text-grey" for="text">Text</label>
            <textarea class="w3-input w3-border w3-border-primary" rows="10" id="text" name="text" required><?=stripcslashes($_POST['text'] ?? '')?></textarea>
        </p><p>
            <input type="submit" class="w3-tertiary w3-button" value="Senden">
        </p>
    </form>
</div>

<?php include '../../templates/footer.tmp.php';