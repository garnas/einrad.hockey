<?php

/**
 * Class MailBot
 */
class MailBot
{
    /**
     * Initiert den PHP-Mailer mit Grundlegende Einstellungen für den Mailversand
     *
     * @return \PHPMailer\PHPMailer\PHPMailer
     */
    public static function start_mailer(): \PHPMailer\PHPMailer\PHPMailer
    {
        $mailer = PHPMailer::load_phpmailer();
        $mailer->isSMTP();
        $mailer->Host = Env::SMTP_HOST;
        $mailer->SMTPAuth = true;
        $mailer->Username = Env::SMTP_USER;
        $mailer->Password = Env::SMTP_PW;
        $mailer->SMTPSecure = 'tls';
        $mailer->Port = Env::SMTP_PORT;
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
        if (Env::ACTIVATE_EMAIL) {

            if ($mailer->send()) {
                return true;
            }

            Helper::log(Config::LOG_EMAILS, 'Fehler: ' . $mailer->ErrorInfo);
            return false;
        }

        // Debugging
        if (!Helper::$ligacenter) {
            $mailer->Password = '***********'; // Passwort verstecken
            $mailer->ClearAllRecipients();
        }

        Helper::log(Config::LOG_EMAILS, 'E-Mail-Debug-Pseudo-Versand erfolgreich');
        db::debug($mailer);
        return true;
    }

    /**
     * Der Mailbot nimmt Emails aus der Datenbank und versendet diese
     *
     */
    public static function mail_bot(): void
    {
        $sql = "
                SELECT * 
                FROM mailbot 
                WHERE mail_status = 'warte'
                ORDER BY zeit 
                LIMIT 50
                ";
        $mails = db::$db->query($sql)->fetch();
        foreach($mails as $mail){
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
                Html::error($mailer->ErrorInfo);
            }
        }
        Html::info('Mailbot wurde ausgeführt.');
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
    public static function add_mail(string $betreff, string $inhalt, string|array $adressaten,
                                    string $absender = Env::SMTP_USER): void
    {
        // Nur wenn Mailadressen vorhanden sind, wird eine Mail hinzugefügt
        if (empty($adressaten)){
            return;
        }

        // In String umwandeln
        if (is_array($adressaten)){
            $adressaten = implode(",", $adressaten);
        }

        $sql = "
                INSERT INTO mailbot (betreff, inhalt, adressat, absender, mail_status)
                VALUES (?, ?, ?, ?, 'warte')
                ";
        $params = [$betreff, $inhalt, $adressaten, $absender];
        db::$db->query($sql, $params)->log();
    }

    /**
     * Ändert den Status einer Email in der Datenbank
     *
     * @param int $mail_id
     * @param string $mail_status
     * @param string|null $fehler
     */
    public static function set_status(int $mail_id, string $mail_status, string $fehler = NULL): void
    {
        $sql = "
            UPDATE mailbot 
            SET mail_status = ?, zeit = zeit, fehler = ? 
            WHERE mail_id = ?
            ";
        db::$db->query($sql, $mail_status, $fehler, $mail_id)->log();
    }

    /**
     * Erstellt eine Warnung im Ligacenter, wenn der Mailbot manche mails nicht versenden kann.
     */
    public static function warning(): void
    {
        $sql = "
            SELECT mail_id
            FROM mailbot 
            WHERE mail_status = 'fehler'
            ";
        if (($anzahl = db::$db->query($sql)->num_rows()) > 0) {
            Html::notice("Der Mailbot kann $anzahl Mail(s) nicht versenden - siehe Datenbank.");
        }
    }

    /**
     * Schreibt eine Mail in die Datenbank an alle spielberechtigten Teams, wenn es (zum Übergang zur Meldephase) noch
     * freie Plätze gibt.
     *
     * @param Turnier $turnier
     */
    public static function mail_plaetze_frei(Turnier $turnier): void
    {
        if ($turnier->get_anzahl_freie_plaetze() > 0 && in_array($turnier->details['art'], ['I', 'II', 'III'])) {
            $team_ids = Team::get_liste_ids();
            foreach ($team_ids as $team_id) {
                // Noch Plätze frei
                if (
                    !$turnier->check_team_angemeldet($team_id)
                    && $turnier->check_team_block($team_id)
                    && !$turnier->check_doppel_anmeldung($team_id)
                ) {
                    $betreff = "Freie Plätze: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
                    ob_start();
                        include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_plaetze_frei.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
                    $inhalt = ob_get_clean();
                    $emails = (new Kontakt ($team_id))->get_emails('info');
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
    public static function mail_warte_zu_spiele(Turnier $turnier, int $team_id): void
    {
        $betreff = "Spielen-Liste: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
        ob_start();
            include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_warte_zu_spiele.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
        $inhalt = ob_get_clean();
        $akt_kontakt = new Kontakt ($team_id);
        $emails = $akt_kontakt->get_emails('info');
        self::add_mail($betreff, $inhalt, $emails);
    }

    /**
     * Erstellt eine Mail in der Datenbank an alle vom Losen betroffenen Teams
     *
     * @param Turnier $turnier
     */
    public static function mail_gelost(Turnier $turnier): void
    {
        if (in_array($turnier->details['art'], ['I', 'II', 'III'])) {
            $team_ids = Team::get_liste_ids();
            foreach ($team_ids as $team_id) {
                // Team angemeldet?
                if ($turnier->check_team_angemeldet($team_id)) {
                    // Auf Warteliste gelandet
                    if ($turnier->get_liste($team_id) == 'warte') {
                        $liste = "Warteliste";
                        // Auf Spielen-Liste gelandet
                    } elseif ($turnier->get_liste($team_id) == 'spiele') {
                        $liste = "Spielen-Liste";
                    } else {
                        return;
                    }
                    $betreff = "$liste: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
                    ob_start();
                        include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_gelost.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
                    $inhalt = ob_get_clean();
                    $emails = (new Kontakt ($team_id))->get_emails('info');
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }

    /**
     * Erstellt eine Mail in der Datenbank an alle spielberechtigten Teams, wenn ein neues Turnier eingetragen wird
     *
     * @param nTurnier $turnier
     */
    public static function mail_neues_turnier(nTurnier $turnier): void
    {
        if (in_array($turnier->art, ['I', 'II', 'III'])) {
            $team_ids = Team::get_liste_ids();
            foreach ($team_ids as $team_id) {
                // Noch Plätze frei?
                if ($turnier->check_team_block($team_id) && !$turnier->check_doppel_anmeldung($team_id)) {
                    $betreff = "Neues " . $turnier->tblock . "-Turnier in " . $turnier->ort;
                    ob_start();
                        include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_neues_turnier.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
                    $inhalt = ob_get_clean();
                    $emails = (new Kontakt ($team_id))->get_emails('info');
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
    public static function mail_freilos_abmeldung(Turnier $turnier, int $team_id): void
    {
        if (!in_array($turnier->details['art'], ['I', 'II', 'III'])) return;

        $betreff = "Falscher Freilosblock: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
        ob_start();
            include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_freilos_abmeldung.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
        $inhalt = ob_get_clean();
        $emails = (new Kontakt ($team_id))->get_emails('info');

        self::add_mail($betreff, $inhalt, $emails);
    }

    /**
     * Erstellt eine Mail in der Datenbank an den Ligaausschuss, wenn ein Team Turnierdaten ändert.
     *
     * @param Turnier $turnier
     */
    public static function mail_turnierdaten_geaendert(Turnier $turnier): void
    {
        if ($turnier->details['art'] === 'spass') {
            return;
        }
        $betreff = "Turnierdaten geändert: " . $turnier->details['tblock'] . "-Turnier in " . $turnier->details['ort'];
        ob_start();
            include(Env::BASE_PATH . "/templates/mails/mail_turnierdaten_geaendert.tmp.php");
        $inhalt = ob_get_clean();
        self::add_mail($betreff, $inhalt, Env::LAMAIL);
    }

    /**
     * Erstellt eine Mail in der Datenbank, dass ein Team ein zweites Freilos erhalten hat.
     *
     * @param Team $team
     */
    public static function mail_zweites_freilos(Team $team): void
    {
        $betreff = "Zweites Freilos erhalten";
        ob_start();
        include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
        include(Env::BASE_PATH . "/templates/mails/mail_zweites_freilos.tmp.php");
        include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
        $inhalt = ob_get_clean();
        $emails = (new Kontakt ($team->id))->get_emails('info');
        self::add_mail($betreff, $inhalt, $emails);
    }

}