<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

// Captcha erstellen
$captcha = Captcha::load();
$_SESSION['captcha'] = $captcha->getPhrase();

// Formularauswertung
$error = false;
$send = false; // Email wurde abgesendet
if (isset($_POST['absenden'])) {
    $log_file = "log_kontaktformular.log";
    $absender = $_POST['absender'];
    $name = $_POST['name'];
    $betreff = $_POST['betreff'];
    $text = $_POST['text'];
    $user_captcha = $_POST['captcha'];
    if (empty($absender) or empty($betreff) or empty($text)) {
        Form::error("Bitte Formular ausfüllen");
        $error = true;
    }

    // Captcha validieren
    if (!$captcha->testPhrase($user_captcha)){
        Form::error("Falsches Captcha, bitte versuche es erneut.");
        $error = true;
        // Logdatei erstellen/beschreiben
        Form::log($log_file, "Falsches Captcha: " . $_SESSION['captcha'] . "\n" . print_r($_POST, true));
    }

    // Zeitmessung vs Bots
    /* Um die Ausfüllzeit verdeckt gegenüber Spambots zu messen, wird time() verschlüsselt im Hidden-Feld gespeichert
    Hier dann entschlüsselt. Key ist der Database-Name, welcher nicht auf Github veröffentlicht ist */
    $time = time() - @openssl_decrypt($_POST['no_bot'], 'AES-256-CBC', Config::DATABASE);
    if ($time < 4) { //Bot, wenn in unter 4 Sekunden das Formular abgeschickt wurde
        Form::error("E-Mail konnte wegen Spamverdacht nicht versendet werden,
         da das Formular zu schnell ausgefüllt wurde. Schreib uns bitte an via "
            . Form::mailto(Config::LAMAIL), esc:false);
        $error = true;
        // Logdatei erstellen/beschreiben
        Form::log($log_file, "Zu schnell: " . $time . " Sekunden\n" . print_r($_POST, true));
    }

    if (!$error) {
        //Mail an die Liga
        $mailer = MailBot::start_mailer();
        $mailer->setFrom($absender, $name); // Absenderemail und -name setzen
        $mailer->addAddress(Config::LAMAIL); // Empfängeradresse
        $mailer->Subject = 'Kontaktformular: ' . $betreff; // Betreff der Email
        $mailer->Body = $text;

        // Email an den Ligaausschuss versenden
        if (MailBot::send_mail($mailer)) {
            Form::info("Die E-Mail wurde versandt.");
            $send = true; //Email an den User nur schicken, wenn die Mail an LA rausging
        } else {
            Form::error("Es ist ein Fehler aufgetreten. E-Mail konnte nicht versendet werden.
             Manuell versenden: " . Form::mailto(Config::LAMAIL), esc:false);
            Form::log($log_file, "Error Mail:\n" . print_r($_POST, true) . $mailer->ErrorInfo);
        }
        if ($send) {
            // Confirmation Mail an die angegebene Absendeadresse
            $mailer = MailBot::start_mailer();
            $mailer->setFrom(Config::LAMAIL); // Absenderemail und -name setzen
            $mailer->addAddress($_POST['absender'], $_POST['name']); // Empfängeradresse
            $mailer->Subject = 'Kontaktformular: ' . $_POST['betreff']; // Betreff der Email
            $mailer->Body = "Danke für deine Mail! Du hast uns folgendes gesendet:\r\n\r\n" . $text;
            // Email-versenden
            if (MailBot::send_mail($mailer)) {
                Form::info("Es wurde eine Kopie an $absender gesendet.");
                unset($_SESSION['captcha']); // Captcha aus der Session löschen
                header('Location: ../liga/neues.php');
                die();
            } else {
                Form::error("Es ist ein Fehler aufgetreten: Eine Kopie der E-Mail wurde nicht an dich versendet! Stimmt \"$absender\"?");
                Form::log($log_file, "Error Mailback:\n" . print_r($_POST, true) . $mailer->ErrorInfo);
            }
        } // send
    } // error
} // Form

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$titel = "Kontakt | Deutsche Einradhockeyliga";
Config::$content = "Kontaktformular der Deutschen Einradhockeyliga";
include '../../templates/header.tmp.php';
?>

    <div class="w3-card w3-panel">
        <h1 class="w3-text-primary">Kontaktformular </h1>
        <p>
            <span class="w3-text-grey">
                <i class="material-icons">info_outline</i> Empfänger
            </span>
            <br>
            <?= Form::mailto(Config::LAMAIL) ?>
        </p>
        <form method="post">
            <input type="hidden"
                   name="no_bot"
                   value="<?= @openssl_encrypt(time(), 'AES-256-CBC', Config::DATABASE) ?>"
            >
            <p>
                <label class="w3-text-grey" for="name">
                    <i class="material-icons">perm_identity</i>
                    Name
                </label>
                <input class="w3-input w3-border w3-border-primary"
                       type="text"
                       id="name"
                       name="name"
                       value="<?= $_POST['name'] ?? '' ?>"
                       required
                >
            </p>
            <p>
                <label class="w3-text-grey" for="absender">
                    <i class="material-icons">alternate_email</i>
                    Email
                </label>
                <input class="w3-input w3-border w3-border-primary"
                       type="email"
                       id="absender"
                       name="absender"
                       value="<?= $_POST['absender'] ?? '' ?>"
                       required
                >
            </p>
            <p>
                <label class="w3-text-grey" for="betreff">
                    <i class="material-icons">label_outline</i>
                    Betreff
                </label>
                <input class="w3-input w3-border w3-border-primary"
                       type="text"
                       id="betreff"
                       name="betreff"
                       value="<?= $_POST['betreff'] ?? '' ?>"
                       required
                >
            </p>
            <p>
                <label class="w3-text-grey" for="text">
                    <i class="material-icons">subject</i>
                    Text
                </label>
                <textarea class="w3-input w3-border w3-border-primary"
                          rows="10"
                          id="text"
                          name="text"
                          required
                ><?= $_POST['text'] ?></textarea>
            </p>
            <!-- Captcha -->
            <p>
                <label class="w3-text-grey" for="captcha">
                    <i class="material-icons">lock</i>Captcha
                    <br>
                    <img class="w3-card w3-image" alt='captcha' src="<?= $captcha->inline() ?>">
                </label>
                <!-- nicht Submit, da sonst bei Enter das Captcha neu geladen wird und nicht die Mail versendet wird -->
                <button class="w3-button w3-text-primary"
                        type="button"
                        onclick="this.form.submit()"
                        name="reload_captcha"
                        formnovalidate
                >
                    <i class="material-icons">refresh</i>
                </button>
                <input class="w3-input"
                       type="text"
                       id="captcha"
                       name="captcha"
                       placeholder="Captcha eingeben"
                       style="width: 200px;"
                       value="<?= $_POST['captcha'] ?? '' ?>"
                       required
                >
            </p>
            <?php if (isset($_POST) && !isset($_POST['absenden'])) { ?>
                <!-- View des Browsers auf das neu geladene Captcha -->
                <script>document.getElementById('captcha').scrollIntoView(true);</script>
            <?php } //endif?>
            <p>
                <button type="submit"
                       name="absenden"
                       class="w3-tertiary w3-ripple w3-round w3-button"
                >
                    <i class="material-icons">send</i> Senden
                </button>
            </p>
        </form>
    </div>

<?php include '../../templates/footer.tmp.php';