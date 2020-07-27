<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

//Honeypot vs Spam
if (!empty($_POST['cc'] ?? '')){
    Form::error("Email konnte wegen Spamverdacht nicht versendet werden. Schreib uns bitte an via " . Form::mailto(Config::LAMAIL));
    header("Location: kontakt.php");
    die();
}

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
        $mailer=MailBot::start_mailer(); 
        $mailer->setFrom($absender, $name); // Absenderemail und -name setzen
        $mailer->addAddress(Config::LAMAIL); // Empfängeradresse
        $mailer->Subject = 'Kontaktformular: ' . $betreff; // Betreff der Email
        $mailer->Body = $text;

        //Email-versenden
        if (Config::ACTIVATE_EMAIL){
            if ($mailer->send()){
                Form::affirm("Email wurde versendet");
                $send = true; //Email an den User nur schicken, wenn die Mail an LA rausging
            }else{
                Form::error("Es ist ein Fehler aufgetreten. E-Mail konnte nicht versendet werden. Manuell versenden: " . Form::mailto(Config::LAMAIL));
                Form::error($mailer->ErrorInfo);
            }
        }else{ //Debugging
            if (!($ligacenter ?? false)){
                $mailer->Password = '***********'; //Passwort verstecken
                $mailer->ClearAllRecipients( ); 
            }
            db::debug($mailer);
            $send = true;
        }

        if ($send){
            //Mail an die angegebene Absendeadresse
            $mailer=MailBot::start_mailer();
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
                    Form::error($mailer->ErrorInfo);
                }
            }else{ //Debugging
                if (!($ligacenter ?? false)){
                    $mailer->Password = '***********'; //Passwort verstecken
                    $mailer->ClearAllRecipients( ); 
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
        <input type="text" style="opacity: 0; position: absolute;top: 0; left: 0;height: 0; width: 0;z-index: -1;" name='cc'>
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
            <input type="submit" class="w3-tertiary w3-ripple w3-round w3-button" value="Senden">
        </p>
    </form>
</div>

<?php include '../../templates/footer.tmp.php';