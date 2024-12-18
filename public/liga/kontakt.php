<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

// Captcha erstellen
$captcha = Captcha::load();
$_SESSION['captcha'] = $captcha->getPhrase();

// Formularauswertung
$error = false;
$send = false; // Email wurde abgesendet
if (isset($_POST['absenden'])) {
    $absender = $_POST['absender'];
    $name = $_POST['name'];
    $betreff = $_POST['betreff'];
    $text = $_POST['text'];
    $user_captcha = $_POST['captcha'];
    if (empty($absender) || empty($betreff) || empty($text)) {
        Html::error("Bitte Formular ausfüllen");
        $error = true;
    }

    // Captcha validieren
    if (!$captcha->testPhrase($user_captcha)) {
        Html::error("Falsches Captcha, bitte versuche es erneut.");
        $error = true;
        // Logdatei erstellen/beschreiben
        Helper::log(Config::LOG_KONTAKTFORMULAR, "Falsches Captcha: " . $_SESSION['captcha'] . "\n" . print_r($_POST, true));
    }

    // Zeitmessung vs Bots
    /* Um die Ausfüllzeit verdeckt gegenüber Spambots zu messen, wird time() verschlüsselt im Hidden-Feld gespeichert
    Hier dann entschlüsselt. Key ist der Database-Name, welcher nicht auf Github veröffentlicht ist */
    $time = time() - @openssl_decrypt($_POST['no_bot'], 'AES-256-CBC', Env::DATABASE);
    if ($time < 4) { //Bot, wenn in unter 4 Sekunden das Formular abgeschickt wurde
        Html::error("E-Mail konnte wegen Spamverdacht nicht versendet werden,
         da das Formular zu schnell ausgefüllt wurde. Schreib uns bitte an via "
            . Html::mailto(Env::LAMAIL), esc: false);
        $error = true;
        // Logdatei erstellen/beschreiben
        Helper::log(Config::LOG_KONTAKTFORMULAR, "Zu schnell: " . $time . " Sekunden\n" . print_r($_POST, true));
    }

    if (!$error) {
        //Mail an die Liga
        $mailer = MailBot::start_mailer();
        $mailer->setFrom("noreply@einrad.hockey", $name);
        $mailer->addReplyTo($absender, $name); // Absenderemail und -name setzen
        $mailer->addAddress(Env::LAMAIL); // Empfängeradresse
        $mailer->Subject = 'Kontaktformular: ' . $betreff; // Betreff der Email
        $mailer->Body = $text . "\n\n\nAntwort an: " . $absender;

        // Email an den Ligaausschuss versenden
        if (MailBot::send_mail($mailer)) {
            Html::info("Die E-Mail wurde versandt.");
            $send = true; //Email an den User nur schicken, wenn die Mail an LA rausging
        } else {
            Html::error("Es ist ein Fehler aufgetreten. E-Mail konnte nicht versendet werden.
             Manuell versenden: " . Html::mailto(Env::LAMAIL), esc: false);
            Helper::log(Config::LOG_KONTAKTFORMULAR, "Error Mail:\n" . print_r($_POST, true) . $mailer->ErrorInfo);
        }
        if ($send) {
            // Confirmation Mail an die angegebene Absendeadresse
            $mailer = MailBot::start_mailer();
            $mailer->setFrom(Env::LAMAIL); // Absenderemail und -name setzen
            $mailer->addAddress($_POST['absender'], $_POST['name']); // Empfängeradresse
            $mailer->Subject = 'Kontaktformular: ' . $_POST['betreff']; // Betreff der Email
            $mailer->Body = "Danke für deine Mail! Du hast uns folgendes gesendet:\r\n\r\n" . $text;
            // Email-versenden
            if (MailBot::send_mail($mailer)) {
                Html::info("Es wurde eine Kopie an $absender gesendet.");
                unset($_SESSION['captcha']); // Captcha aus der Session löschen
                Helper::reload('/liga/neues.php');
            }
            Html::error("Es ist ein Fehler aufgetreten: Eine Kopie der E-Mail wurde nicht an dich versendet! Stimmt \"$absender\"?");
            Helper::log(Config::LOG_KONTAKTFORMULAR, "Error Mailback:\n" . print_r($_POST, true) . $mailer->ErrorInfo);
        } // send
    } // error
} // Form

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Kontakt | Deutsche Einradhockeyliga";
Html::$content = "Kontaktformular der Deutschen Einradhockeyliga";
include '../../templates/header.tmp.php';
?>

    <div class="w3-card w3-panel">
        <h1 class="w3-text-primary">Kontaktformular </h1>
        <p>
            <span class="w3-text-grey">
                <?= Html::icon("info_outline") ?> Empfänger
            </span>
            <br>
            <?= Html::mailto(Env::LAMAIL) ?>
        </p>
        <form method="post">
            <input type="hidden"
                   name="no_bot"
                   value="<?= @openssl_encrypt(time(), 'AES-256-CBC', Env::DATABASE) ?>"
            >
            <p>
                <label class="w3-text-grey" for="name">
                    <?= Html::icon("perm_identity") ?>
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
                    <?= Html::icon("alternate_email") ?>
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
                    <?= Html::icon('label_outline') ?>
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
                    <?= Html::icon('subject') ?>
                    Text
                </label>
                <textarea class="w3-input w3-border w3-border-primary"
                          rows="10"
                          id="text"
                          name="text"
                          required
                ><?= $_POST['text'] ?? '' ?></textarea>
            </p>
            <!-- Captcha -->
            <p>
                <label class="w3-text-grey" for="captcha">
                    <?= Html::icon("lock") ?> Captcha
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
                    <?= Html::icon('refresh') ?>
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
                    <?= Html::icon('send') ?> Senden
                </button>
            </p>
        </form>
    </div>

<?php include '../../templates/footer.tmp.php';