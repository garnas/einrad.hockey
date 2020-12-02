<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
//db::debug($x = @openssl_encrypt(time(),'AES-256-CBC', Config::DATABASE));
//db::debug(@openssl_decrypt($x,'AES-256-CBC', Config::DATABASE));
//Formularauswertung
$error = false;
$send = false;
if(isset($_POST['absenden'])) {
    $absender = $_POST['absender'];
    $name = $_POST['name'];
    $betreff = $_POST['betreff'];
    $text = stripcslashes($_POST['text']); //Revidiere die Sql-Sanitation von db::sanitize 

    if(empty($absender) or empty($betreff) or empty($text)){
        Form::error("Bitte Formular ausfüllen");
        $error = true;
    }

    //Honeypot vs Spam
    if (!empty($_POST['honey_pot'] ?? '')){
        Form::error("E-Mail konnte wegen Spamverdacht nicht versendet werden - bitte Felder nicht automatisch ausfüllen. Schreib uns bitte an via " . Form::mailto(Config::LAMAIL));
        $error = true;
        //Logdatei erstellen/beschreiben
        $log_kontakt = fopen('../../system/logs/log_kontaktformular.log', "a");
        $log = date('[Y-M-d H:i:s e]'). " Honey_Pot:\n" . print_r($_POST, true) . "\n\n";
        fwrite($log_kontakt, $log);
    }

    //Zeitmessung vs Bots
    //Um die Ausfüllzeit verdeckt gegenüber Spambots zu messen, wird time() verschlüsselt im Hidden-Feld gespeichert
    //Hier dann entschlüsselt. Key ist der Database-Name, welcher nicht auf Github veröffentlicht ist
    $time = time() - @openssl_decrypt($_POST['no_bot'],'AES-256-CBC', Config::DATABASE);
    if ($time < 6){ //Bot, wenn in unter 6 Sekunden das Formular abgeschickt wurde
        Form::error("E-Mail konnte wegen Spamverdacht nicht versendet werden, da das Formular zu schnell ausgefüllt wurde. Schreib uns bitte an via " . Form::mailto(Config::LAMAIL));
        $error = true;
        //Logdatei erstellen/beschreiben
        $log_kontakt = fopen('../../system/logs/log_kontaktformular.log', "a");
        $log = date('[Y-M-d H:i:s e]') . " Zu schnell: ". $time . " Sekunden\n" . print_r($_POST, true) . "\n\n";
        fwrite($log_kontakt, $log);
    }

    if(!$error){
        //Mail an die Liga
        $mailer=MailBot::start_mailer(); 
        $mailer->setFrom($absender, $name); // Absenderemail und -name setzen
        $mailer->addAddress(Config::LAMAIL); // Empfängeradresse
        $mailer->Subject = 'Kontaktformular: ' . $betreff; // Betreff der Email
        $mailer->Body = $text;

        //Email-versenden
        if (Config::ACTIVATE_EMAIL){
            if ($mailer->send()){
                Form::affirm("Die E-Mail wurde versandt.");
                $send = true; //Email an den User nur schicken, wenn die Mail an LA rausging
            }else{
                Form::error("Es ist ein Fehler aufgetreten. E-Mail konnte nicht versendet werden. Manuell versenden: " . Form::mailto(Config::LAMAIL));
                $log_kontakt = fopen('../../system/logs/log_kontaktformular.log', "a");
                $log = date('[Y-M-d H:i:s e]') . " Fehler:\n" . print_r($_POST, true) . $mailer->ErrorInfo . "\n\n";
                fwrite($log_kontakt, $log);
                fclose($log_kontakt);
            }
        }else{ //Debugging
            if (!($ligacenter ?? false)){
                $mailer->Password = '***********'; //Passwort verstecken
                $mailer->ClearAllRecipients(); 
            }
            db::debug($mailer);
            $send = true;
        }

        if ($send){
            //Mail an die angegebene Absendeadresse
            $mailer = MailBot::start_mailer();
            $mailer->setFrom(Config::LAMAIL); // Absenderemail und -name setzen
            $mailer->addAddress($_POST['absender'],$_POST['name']); // Empfängeradresse
            $mailer->Subject = 'Kontaktformular: ' . $_POST['betreff']; // Betreff der Email
            $mailer->Body = "Danke für deine Mail! Du hast uns folgendes gesendet:\r\n\r\n" . $text;
            //Email-versenden
            if (Config::ACTIVATE_EMAIL){
                if ($mailer->send()){
                    Form::affirm("Es wurde eine Kopie an $absender gesendet.");
                    header('Location: ../liga/neues.php');
                    die();
                }else{
                    Form::error("Es ist ein Fehler aufgetreten: Eine Kopie der E-Mail wurde nicht an dich versendet! Stimmt \"$absender\"?");
                    $log_kontakt = fopen('../../system/logs/log_kontaktformular.log', "a");
                    $log = date('[Y-M-d H:i:s e]') . "Fehler:\n" . print_r($_POST, true) . $mailer->ErrorInfo . "\n\n";
                    fwrite($log_kontakt, $log);
                    fclose($log_kontakt);
                }
            }else{ //Debugging
                if (!($ligacenter ?? false)){
                    $mailer->Password = '***********'; //Passwort verstecken
                    $mailer->ClearAllRecipients(); 
                }
                db::debug($mailer);
            }
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
    <h1 class="w3-text-primary">Kontaktformular </h1>
    <p><span class="w3-text-grey"><i class="material-icons">info_outline</i> Empfänger</span><br><?=Form::mailto(Config::LAMAIL)?></p>
    <form method="post">
        <input type="text" name='honey_pot' style="opacity: 0; position: absolute;top: 0; left: 0;height: 0; width: 0;z-index: -1;">
        <input type="hidden" name="no_bot" value="<?=@openssl_encrypt(time(),'AES-256-CBC', Config::DATABASE)?>">
        <p>
            <label class="w3-text-grey" for="name"><i class="material-icons">perm_identity</i> Name</label>
            <input class="w3-input w3-border w3-border-primary" type="text" id="name" name="name" value="<?=$_POST['name'] ?? ''?>" required>
        </p>
        <p>
            <label class="w3-text-grey" for="absender"><i class="material-icons">alternate_email</i> Email</label>
            <input class="w3-input w3-border w3-border-primary" type="email" id="absender" name="absender" value="<?=$_POST['absender'] ?? ''?>" required>
        </p>
        <p>
            <label class="w3-text-grey" for="betreff"><i class="material-icons">label_outline</i> Betreff</label>
            <input class="w3-input w3-border w3-border-primary" type="text" id="betreff" name="betreff" value="<?=$_POST['betreff'] ?? ''?>" required>
        </p>
        <p>
            <label class="w3-text-grey" for="text"><i class="material-icons">subject</i> Text</label>
            <textarea class="w3-input w3-border w3-border-primary" rows="10" id="text" name="text" required><?=stripcslashes($_POST['text'] ?? '')?></textarea>
        </p>
        <p>
            <input type="submit" name="absenden" class="w3-tertiary w3-ripple w3-round w3-button" value="Senden">
        </p>
    </form>
</div>

<?php include '../../templates/footer.tmp.php';