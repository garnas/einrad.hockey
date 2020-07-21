<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../../frameworks/phpmailer/src/Exception.php';
require_once '../../frameworks/phpmailer/src/PHPMailer.php';

class MailBot {

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

    public static function mail_bot()
    {
        $sql = "SELECT * FROM mailbot WHERE mail_status = 'warte' ORDER BY zeit ASC LIMIT 75";
        $result = db::readdb($sql);
        while ($mail = mysqli_fetch_assoc($result)){
            $mailer = new PHPMailer();
            $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
            $mailer->setFrom($mail['absender']); // Absenderemail und -name setzen
            $mailer->addAddress('merkmich@gmail.com'); // Empfängeradresse
            $mailer->Subject = $mail['betreff']; // Betreff der Email
            $mailer->Body = $mail['inhalt']; // Betreff der Email
            db::debug($mailer);
            if ($mailer->send()){
                self::set_status($mail['mail_id'], 'versendet');
            }else{
                self::set_status($mail['mail_id'], 'Fehler', $mailer->ErrorInfo);
            }
        }
    }
    public static function set_status($mail_id, $mail_status, $fehler = '')
    {
        if (empty($fehler)){
            $sql = "UPDATE mailbot SET mail_status = '$mail_status', zeit = zeit WHERE mail_id = '$mail_id'";
        }else{
            $sql = "UPDATE mailbot SET mail_status = '$mail_status', zeit = zeit, fehler = '$fehler' WHERE mail_id = '$mail_id'";
        }
        db::writedb($sql);
    }

    public static function send_fehler()
    {
        $sql = "SELECT * FROM mailbot WHERE mail_status = 'fehler' ORDER BY zeit ASC";
        $result = db::readdb($sql);
        $inhalt = '';
        if($result->num_rows != 0){
            $mail_ids = array();
            while ($mail = mysqli_fetch_assoc($result)){
                array_push($mail_ids, $mail['mail_id']);
                $inhalt .= "\r\nBetreff:" . $mail['betreff'] . '\r\n' . $mail['adressat'] . '\r\n' .$mail['fehler'];
            }
            $inhalt = stripcslashes($inhalt);
            $mailer = new PHPMailer();
            $mailer->CharSet = 'UTF-8'; // Charset setzen (für richtige Darstellung von Sonderzeichen/Umlauten)
            $mailer->setFrom('noreply@einrad.hockey'); // Absenderemail und -name setzen
            $mailer->addAddress(Config::LAMAIL); // Empfängeradresse
            $mailer->Subject = "Mailbot: Mails konnten nicht gesendet werden"; // Betreff der Email
            $mailer->Body = $inhalt; // Betreff der Email
            db::debug($mailer);

            /*if ($mailer->send()){
                foreach ($mail_ids as $mail_id){
                    self::set_status($mail_id, 'nicht versendet');
                }
            }*/
        }
    }
    public static function warning_mail()
    {
        $sql = "SELECT count(*) FROM mailbot WHERE mail_status = 'fehler'";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        if ($result['count(*)'] > 0){
            Form::attention("Der Mailbot kann manche Mails nicht versenden - siehe Datenbank.");
        }
    }
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
                        . "\r\n\r\nFalls du keine automatischen E-Mails mehr von der Einradhockeyliga erhalten willst, kannst du dies <a href='https://einrad.hockey/teamcenter/teamdaten_aendern'>hier</a> deaktivieren."
                        . "\r\n\r\nBis zum nächsten Mal,"
                        . "\r\nEure Einradhockeyliga</html>";
                    $akt_kontakt = new Kontakt ($team_id);
                    $emails = $akt_kontakt->get_emails_info();
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }
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
                        $inhalt = "</html>Hallo " . Team::teamid_to_teamname($team_id) . ","
                            . "\r\n\r\ndas " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " am " . date("d.m.Y", strtotime($akt_turnier->daten['datum'])) . " ist in die Meldephase übergegangen und die freien Spielen-Plätze wurden nach Modus 4.4.2 verteilt. Euer Team steht nun auf der Warteliste."
                            . " Erfahre <a href='https://einrad.hockey/liga/turnier_details?turnier_id=" . $akt_turnier->daten['turnier_id'] . "'>hier</a> mehr."
                            . "\r\n\r\nFalls es nicht gewünscht ist automatische E-Mails durch die Webseite der Einradhockeyliga zu erhalten, kannst du dies <a href='https://einrad.hockey/teamcenter/teamdaten_aendern'>hier</a> deaktivieren."
                            . "\r\n\r\nBis zum nächsten Mal,"
                            . "\r\nEure Einradhockeyliga</html>";
                    //Auf Spielen-Liste gelandet
                    }elseif ($akt_turnier->get_team_liste($team_id) == 'spiele'){
                        $betreff = "Spielen-Liste: " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'];
                        $inhalt = "<html>Hallo " . Team::teamid_to_teamname($team_id) . ","
                            . "\r\n\r\ndas " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " am " . date("d.m.Y", strtotime($akt_turnier->daten['datum'])) . " ist in die Meldephase übergegangen und die freien Spielen-Plätze wurden nach Modus 4.4.2 verteilt. Euer Team steht auf der Spielen-Liste."
                            . " Erfahre <a href='https://einrad.hockey/liga/turnier_details?turnier_id=" . $akt_turnier->daten['turnier_id'] . "'>hier</a> mehr."
                            . "\r\n\r\nFalls es nicht gewünscht ist automatische E-Mails durch die Webseite der Einradhockeyliga zu erhalten, kannst du dies <a href='https://einrad.hockey/teamcenter/teamdaten_aendern'>hier</a> deaktivieren."
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
                        . "\r\n\r\nFalls es nicht gewünscht ist automatische E-Mails durch die Webseite der Einradhockeyliga zu erhalten, kannst du dies <a href='https://einrad.hockey/teamcenter/teamdaten_aendern'>hier</a> deaktivieren."
                        . "\r\n\r\nBis zum nächsten Mal,"
                        . "\r\nEure Einradhockeyliga</html>";
                    $akt_kontakt = new Kontakt ($team_id);
                    $emails = $akt_kontakt->get_emails_info();
                    self::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }
    public static function mail_freilos_abmeldung($akt_turnier, $team_id)
    {   
        if (in_array($akt_turnier->daten['art'], array('I','II','III'))){
            $betreff = "Falscher Freilosblock: " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'];
            $inhalt = "<html>Hallo " . Team::teamid_to_teamname($team_id) . ","
                . "\r\n\r\nIhr hattet für das " . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'] . " am " . date("d.m.Y", strtotime($akt_turnier->daten['datum']))
                . " (<a href='https://einrad.hockey/liga/turnier_details?turnier_id=" . $akt_turnier->daten['turnier_id'] . "'>Link zum Turnier</a>) ein Freilos gesetzt. Da euer Teamblock höher war als der des Turniersblocks, wurdet ihr von der Spielen-Liste abgemeldet und das Freilos wurde euch erstattet."
                . "\r\n\r\nFalls es nicht gewünscht ist automatische E-Mails durch die Webseite der Einradhockeyliga zu erhalten, kann dies unter https://einrad.hockey/teamcenter/teamdaten_aendern deaktiviert werden."
                . "\r\n\r\nBis zum nächsten Mal,"
                . "\r\nEure Einradhockeyliga</html>";
            $akt_kontakt = new Kontakt ($team_id);
            $emails = $akt_kontakt->get_emails_info();
            self::add_mail($betreff, $inhalt, $emails);
        }
    }
    public static function mail_turnierdaten_geaendert($akt_turnier){
        $betreff = "Turnierdaten geändert:" . $akt_turnier->daten['tblock'] . "-Turnier in " . $akt_turnier->daten['ort'];
        $inhalt = "Hallo Ligaausschuss,"
            . "\r\n\r\nEin Ausrichter hat seine Turnierdaten verändert: https://einrad.hockey/ligacenter/lc_turnier_log?turnier_id=" . $akt_turnier->daten['turnier_id']
            . "\r\n\r\nTeams werden nicht mehr automatisch benachrichtigt."
            . "\r\n\r\nEuer Mailbot";
        self::add_mail($betreff, $inhalt, Config::LAMAIL);
    }
}