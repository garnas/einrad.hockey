<?php

/**
 * Class MailBot
 */
class MailBot
{
    /**
     * Beginn einer HTML-Mail vom MailBot
     * @var string
     */
    public static string $mail_beginning = '<html lang="de"><head><meta charset="UTF-8"><title>E-Mail der Deutschen Einradhockeyliga</title></head>';
    /**
     * Ende einer HTML-Mail vom MailBot mit Abmeldehinweis
     * @var string
     */
    public static string $mail_ending = "<br><br>Falls du keine automatischen E-Mails mehr von der Einradhockeyliga erhalten willst, kannst du dies <a href='" . Config::BASE_LINK . "/teamcenter/tc_teamdaten_aendern'>hier</a> deaktivieren."
                                        . "<br><br>Bis zum nächsten Mal"
                                        . "<br>Eure Einradhockeyliga</html>";

    /**
     * Initiert den PHP-Mailer mit Grundlegende Einstellungen für den Mailversand
     *
     * @return \PHPMailer\PHPMailer\PHPMailer
     */
    public static function start_mailer(): \PHPMailer\PHPMailer\PHPMailer
    {
        $mailer = PHPMailer::load_phpmailer();
        $mailer->isSMTP();
        $mailer->Host = Config::SMTP_HOST;
        $mailer->SMTPAuth = true;
        $mailer->Username = Config::SMTP_USER;
        $mailer->Password = Config::SMTP_PW;
        $mailer->SMTPSecure = 'tls';
        $mailer->Port = Config::SMTP_PORT;
        $mailer->CharSet = 'UTF-8';
        return $mailer;
    }

    /**
     * Versendet eine Mail mit dem PHPMailer
     *
     * @param \PHPMailer\PHPMailer\PHPMailer $mailer PHPMailer Objekt
     * @return bool Wurde die Mail erfolgreich versendet?
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function send_mail(\PHPMailer\PHPMailer\PHPMailer $mailer): bool
    {
        if (Config::ACTIVATE_EMAIL) {
            if ($mailer->send()) {
                Form::log('emails.log', 'Mail erfolgreich versendet');
                return true;
            } else {
                Form::log('emails.log', 'Fehler:' . $mailer->ErrorInfo);
                return false;
            }
        } else { // Debugging
            if (!($ligacenter ?? false)) {
                $mailer->Password = '***********'; // Passwort verstecken
                $mailer->ClearAllRecipients();
                Form::log('emails.log', 'Mail erfolgreich versendet');
            }
            db::debug($mailer);
            return false;
        }
    }

    /**
     * Der Mailbot nimmt Emails aus der Datenbank und versendet diese
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function mail_bot()
    {
        $sql = "
                SELECT * 
                FROM mailbot 
                WHERE mail_status = 'warte'
                ORDER BY zeit 
                LIMIT 50
                ";
        $result = db::read($sql);
        while ($mail = mysqli_fetch_assoc($result)) {
            $mailer = self::start_mailer();
            $mailer->isHTML(true); // Für die Links
            $mailer->setFrom($mail['absender'], 'Einradhockey-Mailbot'); // Absenderemail und -name setzen
            $mail_addresses = explode(',', $mail['adressat']); // Aus der Datenbank rausholen
            $anz_mail_addresses = count($mail_addresses);
            foreach ($mail_addresses as $mail_address) {
                if ($anz_mail_addresses > 15) {
                    $mailer->addBCC($mail_address);
                }
                $mailer->addAddress($mail_address); // Empfängeradresse
            }

            $mailer->Subject = $mail['betreff']; // Betreff der Email
            $mailer->Body = $mail['inhalt']; // Inhalt der Email

            // Email-versenden
            if (self::send_mail($mailer)) {
                self::set_status($mail['mail_id'], 'versendet');
            } else {
                self::set_status($mail['mail_id'], 'Fehler', $mailer->ErrorInfo);
                Form::error($mailer->ErrorInfo);
            }
        }
        Form::affirm('Mailbot wurde ausgeführt.');
    }

    /**
     * Fügt eine Email zur Datenbank hinzu
     *
     * Nur für automatische Emails verwenden. Die Mails werden erst bei der nächsten Ausführung des Mailbots versendet.
     *
     * @param string $betreff
     * @param string $inhalt
     * @param string|array $adressaten
     * @param string $absender
     */
    public static function add_mail(string $betreff, string $inhalt, string|array $adressaten, string $absender = Config::SMTP_USER)
    {
        if (!empty($adressaten)) { //Nur wenn Mailadressen vorhanden sind, wird eine Mail hinzugefügt
            $betreff = db::sanitize($betreff);
            $inhalt = db::sanitize($inhalt);
            if (is_array($adressaten)) {
                $adressaten = implode(",", $adressaten); // in String umwandeln
            }
            $sql = "INSERT INTO mailbot (betreff, inhalt, adressat, absender, mail_status)
                    VALUES ('$betreff', '$inhalt', '$adressaten', '$absender', 'warte')";
            db::write($sql);
        }
    }

    /**
     * Ändert den Status einer Email in der Datenbank
     *
     * @param int $mail_id
     * @param string $mail_status
     * @param string|null $fehler
     */
    public static function set_status(int $mail_id, string $mail_status, string $fehler = NULL)
    {
        if ($fehler === NULL) {
            $sql = "
                UPDATE mailbot 
                SET mail_status = '$mail_status', zeit = zeit 
                WHERE mail_id = '$mail_id'
                ";
        } else {
            $sql = "
                UPDATE mailbot 
                SET mail_status = '$mail_status', zeit = zeit, fehler = '$fehler' 
                WHERE mail_id = '$mail_id'
                ";
        }
        db::write($sql);
    }

    /**
     * Erstellt eine Warnung im Ligacenter, wenn der Mailbot manche mails nicht versenden kann.
     */
    public static function warning_mail()
    {
        $sql = "
            SELECT count(*) as anzahl
            FROM mailbot 
            WHERE mail_status = 'fehler'
            ";
        $result = db::read($sql);
        $anzahl = mysqli_fetch_assoc($result)['anzahl'];
        if ($anzahl > 0) {
            Form::attention("Der Mailbot kann $anzahl Mail(s) nicht versenden - siehe Datenbank.");
        }
    }

    /**
     * Schreibt eine Mail in die Datenbank an alle spielberechtigten Teams, wenn es (zum Übergang zur Meldephase) noch
     * freie Plätze gibt.
     *
     * @param Turnier $turnier
     */
    public static function mail_plaetze_frei(Turnier $turnier)
    {
        if ($turnier->get_anzahl_freie_plaetze() > 0 && in_array($turnier->details['art'], ['I', 'II', 'III'])) {
            $team_ids = Team::get_ligateams_id();
            foreach ($team_ids as $team_id) {
                // Noch Plätze frei
                if (!$turnier->check_team_angemeldet($team_id) && $turnier->check_team_block($team_id) && !$turnier->check_doppel_anmeldung($team_id)) {
                    $betreff = $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . " hat noch freie Plätze";
                    $inhalt = self::$mail_beginning . "Hallo " . Team::teamid_to_teamname($team_id) . ","
                        . "<br><br>das " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . " am " . date("d.m.Y", strtotime($turnier->details['datum'])) . " ist in die Meldephase übergegangen und hat noch freie Spielen-Plätze"
                        . "(<a href='" . Config::BASE_LINK . "/liga/turnier_details?turnier_id=" . $turnier->details['turnier_id'] . "'>Link zum Turnier</a>). Ihr erhaltet diese automatische E-Mail, weil Ihr einen passenden Turnierblock habt."
                        . self::$mail_ending;
                    $akt_kontakt = new Kontakt ($team_id);
                    $emails = $akt_kontakt->get_emails('info');
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }

    /**
     * Erstellt eine Mail in der Datenbank an Teams, welche in von der Warteliste aufgerückt sind
     *
     * @param Turnier $turnier
     * @param int $team_id
     */
    public static function mail_warte_zu_spiele(Turnier $turnier, int $team_id)
    {
        $betreff = $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . ": Auf Spielen-Liste aufgerückt";
        $inhalt = self::$mail_beginning
            . "Hallo " . Team::teamid_to_teamname($team_id) . ","
            . "<br><br>dein Team ist auf dem " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . " am " . date("d.m.Y", strtotime($turnier->details['datum'])) . " von der Warteliste auf die Spielen-Liste aufgerückt."
            . "<br><br><a href='" . Config::BASE_LINK . "/liga/turnier_details?turnier_id=" . $turnier->details['turnier_id'] . "'>Link zum Turnier</a>"
            . self::$mail_ending;
        $akt_kontakt = new Kontakt ($team_id);
        $emails = $akt_kontakt->get_emails('info');
        self::add_mail($betreff, $inhalt, $emails);
    }

    /**
     * Erstellt eine Mail in der Datenbank an alle vom Losen betroffenen Teams
     *
     * @param Turnier $turnier
     */
    public static function mail_gelost(Turnier $turnier)
    {
        if (in_array($turnier->details['art'], ['I', 'II', 'III'])) {
            $team_ids = Team::get_ligateams_id();
            foreach ($team_ids as $team_id) {
                // Team angemeldet?
                if ($turnier->check_team_angemeldet($team_id)) {
                    // Auf Warteliste gelandet
                    if ($turnier->get_team_liste($team_id) == 'warte') {
                        $betreff = "Warteliste: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
                        $inhalt = self::$mail_beginning . "Hallo " . Team::teamid_to_teamname($team_id) . ","
                            . "<br><br>das " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . " am " . date("d.m.Y", strtotime($turnier->details['datum'])) . " ist in die Meldephase übergegangen und die freien Spielen-Plätze wurden nach Modus 4.4.2 verteilt. Euer Team steht nun auf der <b>Warteliste</b>."
                            . " Erfahre <a " . Config::BASE_LINK . "/liga/turnier_details?turnier_id=" . $turnier->details['turnier_id'] . "'>hier</a> mehr."
                            . self::$mail_ending;
                        // Auf Spielen-Liste gelandet
                    } elseif ($turnier->get_team_liste($team_id) == 'spiele') {
                        $betreff = "Spielen-Liste: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
                        $inhalt = self::$mail_beginning
                            . "Hallo " . Team::teamid_to_teamname($team_id) . ","
                            . "<br><br>das " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . " am " . date("d.m.Y", strtotime($turnier->details['datum'])) . " ist in die Meldephase übergegangen und die freien Spielen-Plätze wurden nach Modus 4.4.2 verteilt. Euer Team steht auf der <b>Spielen-Liste</b>."
                            . " Erfahre <a href='" . Config::BASE_LINK . "/liga/turnier_details?turnier_id=" . $turnier->details['turnier_id'] . "'>hier</a> mehr."
                            . self::$mail_ending;
                    } else {
                        return;
                    }
                    $akt_kontakt = new Kontakt ($team_id);
                    $emails = $akt_kontakt->get_emails('info');
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }

    /**
     * Erstellt eine Mail in der Datenbank an alle spielberechtigten Teams, wenn ein neues Turnier eingetragen wird
     *
     * @param Turnier $turnier
     */
    public static function mail_neues_turnier(Turnier $turnier)
    {
        if (in_array($turnier->details['art'], ['I', 'II', 'III'])) {
            $team_ids = Team::get_ligateams_id();
            foreach ($team_ids as $team_id) {
                // Noch Plätze frei?
                if ($turnier->check_team_block($team_id) && !$turnier->check_doppel_anmeldung($team_id)) {
                    $betreff = "Neues " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
                    $inhalt = self::$mail_beginning
                        . "Hallo " . Team::teamid_to_teamname($team_id) . ","
                        . "<br><br>es wurde ein neues Turnier eingetragen, für welches ihr euch anmelden könnt: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . " am " . date("d.m.Y", strtotime($turnier->details['datum']))
                        . " (<a href='" . Config::BASE_LINK . "/liga/turnier_details?turnier_id=" . $turnier->details['turnier_id'] . "'>Link des neuen Turniers</a>)"
                        . self::$mail_ending;
                    $akt_kontakt = new Kontakt ($team_id);
                    $emails = $akt_kontakt->get_emails('info');
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }

    /**
     * Erstellt eine Mail in der Datenbank, wenn ein Team trotz Freilos beim übergang in die Datenbank abgemeldet wird.
     *
     * @param Turnier $turnier
     * @param int $team_id
     */
    public static function mail_freilos_abmeldung(Turnier $turnier, int $team_id)
    {
        if (in_array($turnier->details['art'], ['I', 'II', 'III'])) {
            $betreff = "Falscher Freilosblock: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
            $inhalt = self::$mail_beginning
                . "Hallo " . Team::teamid_to_teamname($team_id) . ","
                . "<br><br>Ihr hattet für das " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . " am " . date("d.m.Y", strtotime($turnier->details['datum']))
                . " (<a href='" . Config::BASE_LINK . "/liga/turnier_details?turnier_id=" . $turnier->details['turnier_id'] . "'>Link zum Turnier</a>) ein Freilos gesetzt. Da euer Teamblock höher war als der des Turnierblocks, wurdet ihr von der Spielen-Liste abgemeldet und seid nun auf der Warteliste. Das Freilos wurde euch erstattet."
                . self::$mail_ending;
            $emails = (new Kontakt ($team_id))->get_emails('info');
            self::add_mail($betreff, $inhalt, $emails);
        }
    }

    /**
     * Erstellt eine Mail in der Datenbank an den Ligaausschuss, wenn ein Team Turnierdaten ändert.
     *
     * @param Turnier $turnier
     */
    public static function mail_turnierdaten_geaendert(Turnier $turnier)
    {
        $betreff = "Turnierdaten geändert: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
        $inhalt = self::$mail_beginning
            . "Hallo Ligaausschuss,"
            . "<br><br>" . $turnier->details["teamname"] . " hat als Ausrichter seine Turnierdaten vom " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'] . " verändert: <a href='" . Config::BASE_LINK . "/ligacenter/lc_turnier_log?turnier_id=" . $turnier->details['turnier_id'] . "'>Link zum Turnier</a>"
            . "<br><br><b>Teams werden nicht mehr automatisch benachrichtigt.</b>"
            . "<br>Euer Mailbot</html>";
        self::add_mail($betreff, $inhalt, Config::LAMAIL);
    }
}