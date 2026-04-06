<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Mail\MailService;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

// Formularauswertung
$error = false;
$send = false; // Email wurde abgesendet
if (isset($_POST['absenden'])) {

    $absender = $_POST['absender'];
    $name = $_POST['name'];
    $bereich = $_POST['bereich'];
    $betrag = $_POST['betrag'];
    $text = $_POST['text'];
    if (empty($absender) || empty($bereich) || empty($text)) {
        Html::error("Bitte Formular ausfüllen");
        $error = true;
    }

    if (!$error) {
        //Mail an die Liga
        $subject = 'Antrag ' . $bereich; // Betreff der Email
        $body = "Bereich: $bereich\nBetrag: $betrag\n\n\n"
            . $text
            . "\n\n\nAntwort an: " . $absender;

        // Email an den Ligaausschuss versenden
        if (MailService::send(
            subject: $subject,
            body: $body,
            addresses: [Env::LAMAIL],
            from: "noreply@einrad.hockey",
            fromName: $name,
            replyTos: $absender,
        )) {
            Html::info("Die E-Mail wurde versandt.");
            $send = true; // Email an den User nur schicken, wenn die Mail an LA rausging
        } else {
            Html::error("Es ist ein Fehler aufgetreten. E-Mail konnte nicht versendet werden.
             Manuell versenden: " . Html::mailto(Env::LAMAIL), esc: false);
            Helper::log("antrag.log", "Error Mail:\n" . print_r($_POST, true));
        }
        if ($send) {
            // Confirmation E-Mail an die angegebene Absendeadresse
            if (MailService::send(
                subject: $subject,
                body: "Danke für deine Mail! Du hast uns folgendes gesendet:\r\n\r\n" . $body,
                addresses: [Env::LAMAIL],
                from: "noreply@einrad.hockey",
                fromName: $name,
                ccs: [Env::SCHIRIMAIL],
                replyTos: $absender,
            )) {
                Html::info("Es wurde eine Kopie an $absender gesendet.");
                Helper::reload('/teamcenter/tc_start.php');
            }
            Html::error("Es ist ein Fehler aufgetreten: Eine Kopie der E-Mail wurde nicht an dich versendet! Stimmt \"$absender\"?");
            Helper::log("antrag.log", "Error Mailback:\n" . print_r($_POST, true));
        }
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Fördermittel | Deutsche Einradhockeyliga";
Html::$content = "Antragsforumlar der Deutschen Einradhockeyliga";
include '../../templates/header.tmp.php';
?>

    <div class="w3-card w3-panel">
        <h1 class="w3-text-primary">Antragsformular Fördermittel</h1>
        <p>
            <span class="w3-text-grey">
                <?= Html::icon("info_outline") ?> Empfänger
            </span>
            <br>
            <?= Html::mailto(Env::LAMAIL) ?>
        </p>
        <form method="post">
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
                <label class="w3-text-grey" for="bereich">
                    <?= Html::icon('label_outline') ?>
                    Bereich
                </label>
                <select required
                        id="bereich"
                        name="bereich"
                        class="w3-select w3-border w3-border-primary"
                        >
                    <option value="" <?= Html::selected_from_post("bereich") ?> disabled>Bitte wählen</option>
                    <option <?= Html::selected_from_post("bereich", "Einradhockey Allgemein") ?>>Einradhockey
                        Allgemein
                    </option>
                    <option <?= Html::selected_from_post("bereich", "Nachwuchsförderung") ?>>Nachwuchsförderung
                    </option>
                    <option <?= Html::selected_from_post("bereich", "Schiedsrichterwesen") ?>>Schiedsrichterwesen
                    </option>
                    <option <?= Html::selected_from_post("bereich", "A-Kader") ?>>A-Kader</option>
                    <option <?= Html::selected_from_post("bereich", "B-Kader") ?>>B-Kader</option>
                    <option <?= Html::selected_from_post("bereich", "Sonstiges") ?>>Sonstiges</option>
                </select>
            </p>
            <p>
                <label class="w3-text-grey" for="betrag">
                    <?= Html::icon('euro') ?>
                    Gewünschter Förderbetrag
                </label>
                <input class="w3-input w3-border w3-border-primary" <?= Html::value_from_post("betrag") ?> type="number"
                       step="1" min="1" name="betrag" id="betrag">
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
