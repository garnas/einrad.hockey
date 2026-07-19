<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
$saison = (isset($_GET['saison'])) ? (int) $_GET['saison'] : Config::SAISON;

$captcha = Captcha::load();
$_SESSION['captcha'] = $captcha->getPhrase();

// Formularauswertung
$error = false;
$send = false; // Email wurde abgesendet
if (isset($_POST['absenden'])) {

    $absender = $_POST['absender'];
    $name = $_POST['name'];
    $betrag = $_POST['betrag'];
    $text = $_POST['text'];
    $user_captcha = $_POST['captcha'];
    if (empty($absender) || empty($betrag) || empty($text)) {
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

    if (!$error) {
        //Mail an die Liga
        $mailer = MailBot::start_mailer();
        $mailer->setFrom("noreply@einrad.hockey", $name);
        $mailer->addReplyTo($absender, $name); // Absenderemail und -name setzen
        $mailer->addAddress(Env::LAMAIL); // Empfängeradresse
        $mailer->Subject = 'Formular: Antrag auf Fördermittel'; // Betreff der Email
        $mailer->Body = "Betrag: $betrag\n\n\n"
            . $text
            . "\n\n\nAntwort an: " . $absender;

        // Email an den Ligaausschuss versenden
        if (MailBot::send_mail($mailer)) {
            Html::info("Die E-Mail wurde versandt.");
            $send = true; //Email an den User nur schicken, wenn die Mail an LA rausging
        } else {
            Html::error("Es ist ein Fehler aufgetreten. E-Mail konnte nicht versendet werden.
             Manuell versenden: " . Html::mailto(Env::LAMAIL), esc: false);
            Helper::log("antrag.log", "Error Mail:\n" . print_r($_POST, true) . $mailer->ErrorInfo);
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
                Helper::reload('/teamcenter/tc_start.php');
            }
            Html::error("Es ist ein Fehler aufgetreten: Eine Kopie der E-Mail wurde nicht an dich versendet! Stimmt \"$absender\"?");
            Helper::log("antrag.log", "Error Mailback:\n" . print_r($_POST, true) . $mailer->ErrorInfo);
        } // send
    } // error
} // Form

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Fördermittel | Deutsche Einradhockeyliga";
Html::$content = "Antragsforumlar der Deutschen Einradhockeyliga";
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Antrag auf Fördermittel</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>
    
<div class="w3-card w3-panel">
    <form method="post">
        <p>
            <label class="w3-text-grey" for="name">Name</label>
            <input class="w3-input w3-border w3-border-primary"
                    type="text"
                    id="name"
                    name="name"
                    value="<?= e($_POST['name'] ?? '') ?>"
                    required
            >
        </p>
        <p>
            <label class="w3-text-grey" for="absender">Email</label>
            <input class="w3-input w3-border w3-border-primary"
                    type="email"
                    id="absender"
                    name="absender"
                    value="<?= e($_POST['absender'] ?? '') ?>"
                    required
            >
        </p>
        <p>
            <label class="w3-text-grey" for="betrag">Gewünschter Förderbetrag in Euro</label>
            <input class="w3-input w3-border w3-border-primary" <?= Html::value_from_post("betrag") ?> type="number"
                    step="1" min="1" name="betrag" id="betrag">
        </p>
        <p>
            <label class="w3-text-grey" for="text">Weitere Informationen</label>
            <textarea class="w3-input w3-border w3-border-primary"
                        rows="10"
                        id="text"
                        name="text"
                        required
            ><?= e($_POST['text'] ?? '') ?></textarea>
        </p>
        <p>
            <label class="w3-text-grey" for="captcha">
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
                    value="<?= e($_POST['captcha'] ?? '') ?>"
                    required
            >
        </p>
        <?php if (isset($_POST) && !isset($_POST['absenden'])): ?>
            <script>document.getElementById('captcha').scrollIntoView(true);</script>
        <?php endif; ?>
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
