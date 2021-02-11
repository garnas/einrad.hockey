<?php
//Max Anzahl bevor alle im BCC angeschrieben werden
$grenze_bcc = 12;

//Für die Turnierauswahl
$turniere = Turnier::get_turniere('alle', false, false);

//Für Sortierung der Teams nach Blöcken
$akt_spieltag = Tabelle::get_aktuellen_spieltag();
$teams = Tabelle::get_rang_tabelle($akt_spieltag); // Sortierung nach Rangtabelle

//Damit sich $_SESSION von team- und ligacenter nicht vermischen
if ($ligacenter) {
    $list_id = 'lc_emails' . $_SESSION['la_id'];
} elseif ($teamcenter) {
    $list_id = 'tc_emails' . $_SESSION['team_id'];
}


//Formularauswertung Emailauswahl

//Emails zurücksetzen
if (isset($_POST['reset'])) {
    unset ($_SESSION[$list_id]);
}

//Turnier wurde ausgewählt
if (isset($_POST['turnier_id']) && is_numeric($_POST['turnier_id'])) {
    unset ($_SESSION[$list_id]);
    $turnier = new Turnier((int) $_POST['turnier_id']);
    if (empty($turnier->details)) {
        Form::error("Turnier wurde nicht gefunden");
        header('Location: ' . db::escape($_SERVER['PHP_SELF']));
        die();
    }
    $array = Kontakt::get_emails_turnier($_POST['turnier_id']);
    $_SESSION[$list_id]['type'] = 'Turnier in ' . $turnier->details['ort'] . ' (' . date("d.m.Y", strtotime($turnier->details['datum'])) . ', ' . $turnier->details['tblock'] . ')';
    $_SESSION[$list_id]['emails'] = $array['emails'];
    $_SESSION[$list_id]['empfaenger'] = $array['teamnamen'];

    if (isset($_POST['la'])) {
        array_unshift($_SESSION[$list_id]['emails'], Config::LAMAIL);
        array_unshift($_SESSION[$list_id]['empfaenger'], 'Ligaausschuss');
    }

    Form::attention("Achtung: Nichtligateams müssen seperat angeschrieben werden!");
}


//Rundmail wurde ausgewählt
if (isset($_POST['rundmail'])) {
    unset ($_SESSION[$list_id]);
    $_SESSION[$list_id]['type'] = 'Rundmail';
    $_SESSION[$list_id]['empfaenger'] = Team::get_liste_namen();
    $_SESSION[$list_id]['emails'] = Kontakt::get_emails_rundmail();

    array_unshift($_SESSION[$list_id]['emails'], Config::LAMAIL);
    array_unshift($_SESSION[$list_id]['empfaenger'], '<b>Ligaausschuss</b>');
}

//Teams wurden ausgewählt
if (isset($_POST['teams_emails'])) {
    unset ($_SESSION[$list_id]);
    $_SESSION[$list_id]['type'] = 'Teamauswahl';

    $emails = $teamnamen = [];
    foreach (($_POST['team'] ?? []) as $team_id) {
        $akt_team = new Kontakt($team_id);
        $team_emails = $akt_team->get_emails();
        foreach ($team_emails as $email) {
            //Doppelte Email-Einträge vermeiden
            if (!in_array($email, $emails)) {
                array_push($emails, $email);
            }
        }
        array_push($teamnamen, Team::teamid_to_teamname($team_id));
    }
    $_SESSION[$list_id]['emails'] = $emails;
    $_SESSION[$list_id]['empfaenger'] = $teamnamen;
    if (isset($_POST['la'])) {
        array_unshift($_SESSION[$list_id]['emails'], Config::LAMAIL);
        array_unshift($_SESSION[$list_id]['empfaenger'], '<b>Ligaausschuss</b>');
    }

}

//Mailversand
if (isset($_POST['send_mail']) && isset($_SESSION[$list_id])) {
    $error = false;
    $emails = $_SESSION[$list_id]['emails'];
    $betreff = $_POST['betreff'];
    $text = stripcslashes($_POST['text']); //stripcslashes: \r\n für newline-chars wieder escaped, $_POST wird ausnahmsweise nicht in db gespeichert.

    if (empty($emails) or empty($betreff) or empty($text)) {
        Form::error("Kontaktformular unvollständig");
        $error = true;
    }

    if (!$error) {
        $mailer = MailBot::start_mailer();
        if ($ligacenter) {
            $mailer->setFrom(Config::LAMAIL, 'Ligaausschuss');
            $mailer->addBCC(Config::LAMAIL_ANTWORT);
        } elseif ($teamcenter) {
            $akt_kontakt = new Kontakt($_SESSION['team_id']); //Absender Mails bekommen
            $absender = $akt_kontakt->get_emails();
            foreach ($absender as $email) {
                $mailer->AddReplyTo($email, $_SESSION['teamname']); //Antwort an den Absender
                if (!in_array($email, $emails)) {
                    $mailer->addBCC($email); //Als Kontroll-Email an den Absender
                }
                $mailer->setFrom("noreply@einrad.hockey", $_SESSION['teamname']);
            }
        }

        //BCC oder Adressat?
        if (count($emails) > $grenze_bcc) {
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

        // Email-versenden
        if (MailBot::send_mail($mailer)) {
            Form::affirm("Die E-Mail wurde versandt.");
            unset($_SESSION[$list_id]);
            header('Location: ' . db::escape($_SERVER['PHP_SELF']));
            die();
        } else {
            Form::error("Es ist ein Fehler aufgetreten. Mail konnte nicht versendet werden. Manuell Mail versenden: " . Form::mailto(Config::LAMAIL));
            Form::error($mailer->ErrorInfo);
        }
    }
}

//Zum Ausfüllen des Absendeforumulars
if (isset($_SESSION[$list_id])) {
    $anzahl_emails = count($_SESSION[$list_id]['emails']);
    $adressaten = $bcc = [];
    if ($ligacenter) {
        $from = Config::LAMAIL;
        $tos = $_SESSION[$list_id]['emails'];
    } elseif ($teamcenter) {
        $from = $_SESSION['teamname'];
        $tos = $_SESSION[$list_id]['empfaenger'];
    }
    if (empty($_SESSION[$list_id]['emails'])) {
        Form::error("Es wurden keine E-Mail-Adressen gefunden.");
    }
}

