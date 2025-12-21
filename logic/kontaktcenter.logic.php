<?php
// BCC Grenze
$grenze_bcc = Config::BCC_GRENZE;

// Für die Turnierauswahl
$turniere = nTurnier::get_turniere();

// Für Sortierung der Teams nach Blöcken
$akt_spieltag = Tabelle::get_aktuellen_spieltag();
$teams = Tabelle::get_rang_tabelle($akt_spieltag); // Sortierung nach Rangtabelle

// Damit sich $_SESSION von team- und ligacenter nicht vermischen
if (Helper::$ligacenter) {
    $list_id = 'lc_emails' . $_SESSION['logins']['la']['id'];
} elseif (Helper::$teamcenter) {
    $list_id = 'tc_emails' . $_SESSION['logins']['team']['id'];
} elseif (Helper::$team_social_media) {
    $list_id = 'oc_emails' . $_SESSION['logins']['oa']['id'];
}


// Formularauswertung Emailauswahl

// Emails zurücksetzen
if (isset($_POST['reset'])) {
    unset ($_SESSION[$list_id]);
}

//Turnier wurde ausgewählt
if (isset($_POST['turnier_id']) && is_numeric($_POST['turnier_id'])) {
    unset ($_SESSION[$list_id]);
    $turnier = nTurnier::get((int) $_POST['turnier_id']);
    if (empty($turnier->get_turnier_id())) {
        Html::error("Turnier wurde nicht gefunden");
        header('Location: ' . db::escape($_SERVER['PHP_SELF']));
        die();
    }
    $array = Kontakt::get_emails_turnier($_POST['turnier_id']);
    $_SESSION[$list_id]['type'] = 'Turnier in ' . $turnier->get_ort() . ' (' . date("d.m.Y", strtotime($turnier->get_datum())) . ', ' . $turnier->get_tblock() . ')';
    $_SESSION[$list_id]['emails'] = $array['emails'];
    $_SESSION[$list_id]['empfaenger'] = $array['teamnamen'];

    if (isset($_POST['la'])) {
        array_unshift($_SESSION[$list_id]['emails'], Env::LAMAIL);
        array_unshift($_SESSION[$list_id]['empfaenger'], 'Ligaausschuss');
    }

    Html::notice("Achtung: Nichtligateams müssen seperat angeschrieben werden!");
}

// Rundmail wurde ausgewählt
if (isset($_POST['rundmail'])) {
    unset ($_SESSION[$list_id]);
    $_SESSION[$list_id]['type'] = 'Rundmail';
    $_SESSION[$list_id]['empfaenger'] = Team::get_liste();
    $_SESSION[$list_id]['emails'] = Kontakt::get_emails_rundmail();

    array_unshift($_SESSION[$list_id]['emails'], Env::LAMAIL);
    array_unshift($_SESSION[$list_id]['empfaenger'], 'Ligaausschuss');
}

// Teams wurden ausgewählt
if (isset($_POST['teams_emails'])) {
    unset ($_SESSION[$list_id]);
    $_SESSION[$list_id]['type'] = 'Teamauswahl';

    $emails = $teamnamen = [];
    foreach (($_POST['team'] ?? []) as $team_id) {
        $akt_team = new Kontakt($team_id);
        $team_emails = $akt_team->get_emails();
        foreach ($team_emails as $email) {
            // Doppelte Email-Einträge vermeiden
            if (!in_array($email, $emails)) {
                $emails[] = $email;
            }
        }
        $teamnamen[] = Team::id_to_name($team_id);
    }
    $_SESSION[$list_id]['emails'] = $emails;
    $_SESSION[$list_id]['empfaenger'] = $teamnamen;
    if (isset($_POST['la'])) {
        array_unshift($_SESSION[$list_id]['emails'], Env::LAMAIL);
        array_unshift($_SESSION[$list_id]['empfaenger'], 'Ligaausschuss');
    }

}

//Mailversand
if (isset($_POST['send_mail'], $_SESSION[$list_id])) {
    $error = false;
    $emails = $_SESSION[$list_id]['emails'];
    $betreff = $_POST['betreff'];
    $text = $_POST['text'];

    if (empty($emails) || empty($betreff) || empty($text)) {
        Html::error("Kontaktformular unvollständig");
        $error = true;
    }

    if (!$error) {
        $mailer = MailBot::start_mailer();
        if (Helper::$ligacenter) {
            $mailer->setFrom(Env::LAMAIL, 'Ligaausschuss');
            $mailer->addBCC(Env::LAMAIL_ANTWORT);
        } elseif (Helper::$team_social_media) {
            $mailer->setFrom(Env::LAMAIL, 'Öffentlichkeitsausschuss');
            $mailer->addBCC(Env::OEFFIMAIL);
        } elseif (Helper::$teamcenter) {
            $akt_kontakt = new Kontakt($_SESSION['logins']['team']['id']); //Absender Mails bekommen
            $absender = $akt_kontakt->get_emails();
            foreach ($absender as $email) {
                $mailer->AddReplyTo($email, $_SESSION['logins']['team']['name']); //Antwort an den Absender
                if (!in_array($email, $emails)) {
                    $mailer->addBCC($email); //Als Kontroll-Email an den Absender
                }
                $mailer->setFrom("noreply@einrad.hockey", $_SESSION['logins']['team']['name']);
            }
        }

        // BCC oder Adressat?
        // Überprüfung, wann BCC angeschrieben werden muss
        if (count($emails) > Config::BCC_GRENZE) {
            foreach ($emails as $email) {
                $mailer->addBCC($email);
            }
        } else {
            foreach ($emails as $email) {
                $mailer->addAddress($email);
            }
        }

        // Text und Betreff hinzufügen
        $mailer->Subject = $betreff;
        $mailer->Body = $text . "\r\n\r\nVersendet aus dem Kontaktcenter von einrad.hockey";


        Helper::log(Config::LOG_EMAILS, "Betreff: $betreff" . " an " . implode(',', $emails));
        // Email-versenden
        if (MailBot::send_mail($mailer)) {
            Html::info("Die E-Mail wurde versandt.");
            unset($_SESSION[$list_id]);
            header('Location: ' . db::escape($_SERVER['PHP_SELF']));
            die();
        }

        Html::error("Es ist ein Fehler aufgetreten. Mail konnte nicht versendet werden.");
    }
}

// Zum Ausfüllen des Absendeforumulars
if (isset($_SESSION[$list_id])) {
    $anzahl_emails = count($_SESSION[$list_id]['emails']);
    $adressaten = $bcc = [];
    if (Helper::$ligacenter) {
        $from = Env::LAMAIL;
        $tos = $_SESSION[$list_id]['emails'];
    } elseif (Helper::$team_social_media) {
        $from = Env::OEFFIMAIL;
        $tos = $_SESSION[$list_id]['emails'];
    } elseif (Helper::$teamcenter) {
        $from = $_SESSION['logins']['team']['name'];
        $tos = $_SESSION[$list_id]['empfaenger'];
    }
    if (empty($_SESSION[$list_id]['emails'])) {
        Html::error("Es wurden keine E-Mail-Adressen gefunden.");
    }
}

if (Helper::$ligacenter) {
    $las = Ligaleitung::get_all('ligaausschuss');
    $signatur = "\r\n\r\n\r\nDein Ligaausschuss\r\n--\r\n";
    foreach ($las as $la){
        $signatur .= $la['vorname'] . ' ' . $la['nachname']
            . (!empty($la['teamname']) ? ' (' . $la['teamname'] . ')' : '') . "\r\n";
    }
}

if (Helper::$team_social_media) {
    $signatur = "\r\n\r\n\r\nDein Öffentlichkeitsausschuss";
}