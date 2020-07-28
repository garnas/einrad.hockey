<?php
// PHP-Mailer hinzufügen //QUELLE: https://www.html-seminar.de/forum/thread/6852-kontaktformular-tutorial/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../frameworks/phpmailer/src/Exception.php';
require_once '../../frameworks/phpmailer/src/PHPMailer.php';
require_once '../../frameworks/phpmailer/src/SMTP.php';

class MailBot {

    //Mailversand mit PHPMailer initieren
    public static function start_mailer(){
        $mailer = new PHPMailer();
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

    //Der Mailbot nimmt Emails aus der Datenbank und versendet diese 
    public static function mail_bot()
    {
        $sql = "SELECT * FROM mailbot WHERE mail_status = 'warte' ORDER BY zeit ASC LIMIT 50";
        $result = db::readdb($sql);
        while ($mail = mysqli_fetch_assoc($result)){
            $mailer = self::start_mailer();
            $mailer->isHTML(true); // Für die Links
            $mailer->setFrom($mail['absender']); // Absenderemail und -name setzen

            $mail_addresses = explode(',',$mail['adressat']); //Aus der Datenbank rausholen
            $anz_mail_addresses = count($mail_addresses);
            foreach ($mail_addresses as $mail_address){
                if ($anz_mail_addresses > 15){
                    $mailer->addBCC($mail_address);
                }
                    $mailer->addAddress($mail_address); // Empfängeradresse
            }

            $mailer->Subject = $mail['betreff']; // Betreff der Email
            $mailer->Body = $mail['inhalt']; // Inhalt der Email

            //Email-versenden
            if (Config::ACTIVATE_EMAIL){
                if ($mailer->send()){
                    self::set_status($mail['mail_id'], 'versendet');
                }else{
                    self::set_status($mail['mail_id'], 'Fehler', $mailer->ErrorInfo);
                    Form::error($mailer->ErrorInfo);
                }
            }else{ //Debugging
                if (!($ligacenter ?? false)){
                    $mailer->Password = '***********'; //Passwort verstecken
                    $mailer->ClearAllRecipients(); 
                }
                db::debug($mailer);
            }
        }
        Form::affirm('Mailbot wurde ausgeführt.');
    }

    //Fügt eine Email zur Datenbank hinzu
    //Nur für automatische Emails verwenden!
    public static function add_mail($betreff, $inhalt ,$adressaten, $absender = Config::LAMAIL)
    {   
        if (!empty($adressaten)){
            $betreff = db::sanitize($betreff);
            $inhalt = db::sanitize($inhalt);
            if (is_array($adressaten)){
                $adressaten=implode(",",$adressaten); // in String umwandeln
            }
            $sql = "INSERT INTO mailbot (betreff, inhalt, adressat, absender, mail_status)
                    VALUES ('$betreff', '$inhalt', '$adressaten', '$absender', 'warte')";
            db::writedb($sql);
        } 
    }

    //Ändert den Status einer Email in der Datenbank
    public static function set_status($mail_id, $mail_status, $fehler = '')
    {
        if (empty($fehler)){
            $sql = "UPDATE mailbot SET mail_status = '$mail_status', zeit = zeit WHERE mail_id = '$mail_id'";
        }else{
            $sql = "UPDATE mailbot SET mail_status = '$mail_status', zeit = zeit, fehler = '$fehler' WHERE mail_id = '$mail_id'";
        }
        db::writedb($sql);
    }

    //Erstellt eine Warnung im Ligacenter, wenn der Mailbot manche mails nicht versenden kann.
    public static function warning_mail()
    {
        $sql = "SELECT count(*) FROM mailbot WHERE mail_status = 'fehler'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        if ($result['count(*)'] > 0){
            Form::attention("Der Mailbot kann manche Mails nicht versenden - siehe Datenbank.");
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////Automatische Infomails/////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////

    //Erstellt eine Mail in der Datenbank an alle spielberechtigten Teams, wenn es zum Übergang zur Meldephase noch freie Plätze gibt
    public static function mail_plaetze_frei($akt_turnier)
    {   
        if ($akt_turnier->anzahl_freie_plaetze() > 0 && in_array($akt_turnier->daten['art'], array('I','II','III'))){
            $team_ids = Team::get_all_teamids();
            foreach ($team_ids as $team_id){
                //Noch Plätze frei
                if (!$akt_turnier->check_team_angemeldet($team_id) && $akt_turnier->check_team_block($team_id) && !$akt_turnier->check_doppel_anmeldung($team_id)){
                    $betreff = $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " hat noch freie Plätze";
                    $inhalt = "<html>Hallo " . Team::teamid_to_teamname($team_id) . ","
                        . "\r\n\r\ndas " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " am " . date("d.m.Y", strtotime($akt_turnier->daten['datum'])) . " ist in die Meldephase übergegangen und hat noch freie Spielen-Plätze. Ihr erhaltet diese automatische E-Mail, weil Ihr einen passenden Turnierblock habt."
                        . "\r\n\r\nFalls du keine automatischen E-Mails mehr von der Einradhockeyliga erhalten willst, kannst du dies <a href='https://einrad.hockey/teamcenter/tc_teamdaten_aendern'>hier</a> deaktivieren."
                        . "\r\n\r\nBis zum nächsten Mal,"
                        . "\r\nEure Einradhockeyliga</html>";
                    $akt_kontakt = new Kontakt ($team_id);
                    $emails = $akt_kontakt->get_emails_info();
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }

    //Erstellt eine Mail in der Datenbank an alle vom Losen betroffenen Teams
    public static function mail_gelost($akt_turnier)
    {   
        if (in_array($akt_turnier->daten['art'], array('I','II','III'))){
            $team_ids = Team::get_all_teamids();
            foreach ($team_ids as $team_id){
                //Team angemeldet?
                if ($akt_turnier->check_team_angemeldet($team_id)){
                    //Auf Warteliste gelandet
                    if ($akt_turnier->get_team_liste($team_id) == 'warte'){
                        $betreff = "Warteliste: " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'];
                        $inhalt = "<html>Hallo " . Team::teamid_to_teamname($team_id) . ","
                            . "\r\n\r\ndas " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " am " . date("d.m.Y", strtotime($akt_turnier->daten['datum'])) . " ist in die Meldephase übergegangen und die freien Spielen-Plätze wurden nach Modus 4.4.2 verteilt. Euer Team steht nun auf der <b>Warteliste</b>."
                            . " Erfahre <a href='https://einrad.hockey/liga/turnier_details?turnier_id=" . $akt_turnier->daten['turnier_id'] . "'>hier</a> mehr."
                            . "\r\n\r\nFalls es nicht gewünscht ist automatische E-Mails durch die Webseite der Einradhockeyliga zu erhalten, kannst du dies <a href='https://einrad.hockey/teamcenter/tc_teamdaten_aendern'>hier</a> deaktivieren."
                            . "\r\n\r\nBis zum nächsten Mal,"
                            . "\r\nEure Einradhockeyliga</html>";
                    //Auf Spielen-Liste gelandet
                    }elseif ($akt_turnier->get_team_liste($team_id) == 'spiele'){
                        $betreff = "Spielen-Liste: " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'];
                        $inhalt = "<html>Hallo " . Team::teamid_to_teamname($team_id) . ","
                            . "\r\n\r\ndas " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " am " . date("d.m.Y", strtotime($akt_turnier->daten['datum'])) . " ist in die Meldephase übergegangen und die freien Spielen-Plätze wurden nach Modus 4.4.2 verteilt. Euer Team steht auf der <b>Spielen-Liste</b>."
                            . " Erfahre <a href='https://einrad.hockey/liga/turnier_details?turnier_id=" . $akt_turnier->daten['turnier_id'] . "'>hier</a> mehr."
                            . "\r\n\r\nFalls es nicht gewünscht ist automatische E-Mails durch die Webseite der Einradhockeyliga zu erhalten, kannst du dies <a href='https://einrad.hockey/teamcenter/tc_teamdaten_aendern'>hier</a> deaktivieren."
                            . "\r\n\r\nBis zum nächsten Mal,"
                            . "\r\nEure Einradhockeyliga<html>";
                     }
                    $akt_kontakt = new Kontakt ($team_id);
                    $emails = $akt_kontakt->get_emails_info();
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }
    //Erstellt eine Mail in der Datenbank an alle spielberechtigten Teams, wenn ein neues Turnier eingetragen wird
    public static function mail_neues_turnier($akt_turnier)
    {   
        if (in_array($akt_turnier->daten['art'], array('I','II','III'))){
            $team_ids = Team::get_all_teamids();
            foreach ($team_ids as $team_id){
                //Noch Plätze frei
                if ($akt_turnier->check_team_block($team_id) && !$akt_turnier->check_doppel_anmeldung($team_id)){
                    $betreff = "Neues " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'];
                    $inhalt = "<html>Hallo " . Team::teamid_to_teamname($team_id) . ","
                        . "\r\n\r\nEs wurde ein neues Turnier eingetragen, für welches ihr euch Anmelden könnt: " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " am " . date("d.m.Y", strtotime($akt_turnier->daten['datum']))
                        . " (<a href='https://einrad.hockey/liga/turnier_details?turnier_id=" . $akt_turnier->daten['turnier_id'] . "'>Link des neuen Turniers</a>)"
                        . "\r\n\r\nFalls es nicht gewünscht ist automatische E-Mails durch die Webseite der Einradhockeyliga zu erhalten, kannst du dies <a href='https://einrad.hockey/teamcenter/tc_teamdaten_aendern'>hier</a> deaktivieren."
                        . "\r\n\r\nBis zum nächsten Mal,"
                        . "\r\nEure Einradhockeyliga</html>";
                    $akt_kontakt = new Kontakt ($team_id);
                    $emails = $akt_kontakt->get_emails_info();
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }
    //Erstellt eine Mail in der Datenbank, wenn ein Team trotz Freilos beim übergang in die Datenbank abgemeldet wird.
    public static function mail_freilos_abmeldung($akt_turnier, $team_id)
    {   
        if (in_array($akt_turnier->daten['art'], array('I','II','III'))){
            $betreff = "Falscher Freilosblock: " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'];
            $inhalt = "<html>Hallo " . Team::teamid_to_teamname($team_id) . ","
                . "\r\n\r\nIhr hattet für das " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " am " . date("d.m.Y", strtotime($akt_turnier->daten['datum']))
                . " (<a href='https://einrad.hockey/liga/turnier_details?turnier_id=" . $akt_turnier->daten['turnier_id'] . "'>Link zum Turnier</a>) ein Freilos gesetzt. Da euer Teamblock höher war als der des Turnierblocks, wurdet ihr von der Spielen-Liste abgemeldet und seid nun auf der Warteliste. Das Freilos wurde euch erstattet."
                . "\r\n\r\nFalls es nicht gewünscht ist automatische E-Mails durch die Webseite der Einradhockeyliga zu erhalten, kannst du dies <a href='https://einrad.hockey/teamcenter/tc_teamdaten_aendern'>hier</a> deaktivieren."
                . "\r\n\r\nBis zum nächsten Mal,"
                . "\r\nEure Einradhockeyliga</html>";
            $akt_kontakt = new Kontakt ($team_id);
            $emails = $akt_kontakt->get_emails_info();
            self::add_mail($betreff, $inhalt, $emails);
        }
    }
    //Erstellt eine Mail in der Datenbank an den Ligaausschuss, wenn ein Team Turnierdaten ändert.
    public static function mail_turnierdaten_geaendert($akt_turnier){
        $betreff = "Turnierdaten geändert:" . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'];
        $inhalt = "<html>Hallo Ligaausschuss,"
            . "\r\n\r\nEin Ausrichter hat seine Turnierdaten verändert: <a href='https://einrad.hockey/ligacenter/lc_turnier_log?turnier_id=" . $akt_turnier->daten['turnier_id'] . "'>Link zum Turnier</a>"
            . "\r\n\r\n<b>Teams werden nicht mehr automatisch benachrichtigt.</b>"
            . "\r\n\r\nEuer Mailbot</html>";
        self::add_mail($betreff, $inhalt, Config::LAMAIL);
    }
}