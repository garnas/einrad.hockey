<?php

namespace App\Service\Mail;

use App\Entity\Sonstiges\nMailbot;
use App\Repository\Mailbot\MailbotRepository;
use Config;
use DateTime;
use Env;
use Helper;
use Html;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    private static function createMailer(): PHPMailer
    {
        $mailer = new PHPMailer();
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
     * @throws Exception
     */
    private static function _send(PHPMailer $mailer): bool
    {
        $recipients = array_keys($mailer->getAllRecipientAddresses());
        $recipients_as_text = implode(', ', $recipients);

        Helper::log(Config::LOG_EMAILS, "Email-Versand an $recipients_as_text: $mailer->Subject");

        if (Env::ACTIVATE_EMAIL) {
            if ($mailer->send()) {
                return true;
            } else {
                $error = "Mailversand an $recipients_as_text fehlgeschlagen: $mailer->ErrorInfo";
                Helper::log(file_name: Config::LOG_EMAILS, line: $error);
                Html::error($error);
            }
            return false;
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public static function send(
        string $subject,
        string $body,
        array  $addresses,
        string $addressesName = "",
        string $from = Env::SMTP_HOST,
        string $fromName = "",
        array  $ccs = [],
        array  $bccs = [],
        array  $replyTos = [],
        bool   $isHtml = false,
    ): bool {
        $mailer = self::createMailer();
        $mailer->setFrom(address: $from, name: $addressesName);
        foreach ($ccs as $cc) {
            $mailer->addCC($cc);
        }
        foreach ($bccs as $bcc) {
            $mailer->addBCC($bcc);
        }
        foreach ($replyTos as $replyTo) {
            $mailer->addReplyTo(address: $replyTo, name: $fromName);
        }
        $mailer->Subject = $subject;
        if ($isHtml) {
            $mailer->msgHTML($body);
        } else {
            $mailer->Body = $body;
        }

        $isRundmail = (count($addresses) > Config::BCC_GRENZE);
        $success = true;
        if ($isRundmail) {
            foreach ($addresses as $address) {
                $mailer->clearAddresses();
                $mailer->addAddress(address: $address, name: $addressesName);
                if (!self::_send($mailer)) {
                    $success = false;
                }
            }
        } else {
            foreach ($addresses as $address) {
                $mailer->addAddress(address: $address, name: $addressesName);
            }
            if (!self::_send($mailer)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * @throws Exception
     */
    public static function queue(
        string $subject,
        string $content,
        string|array $recipients,
        string $sender = Env::SMTP_USER,
        bool $immediately = false,
    ): void {
        if (empty($recipients)) {
            return;
        }

        if (is_array($recipients)) {
            $recipients = implode(',', $recipients);
        }

        $mail = (new nMailbot())
            ->setBetreff($subject)
            ->setInhalt($content)
            ->setAdressat($recipients)
            ->setAbsender($sender)
            ->setMailStatus('warte')
            ->setZeit(new DateTime());

        MailbotRepository::get()->save($mail);

        if ($immediately) {
            self::dispatch(mailId: $mail->getMailId());
        }
    }

    /**
     * @throws Exception
     */
    public static function dispatch(?int $mailId = null): void
    {
        $repo = MailbotRepository::get();

        if ($mailId === null) {
            $mails = $repo->findPending();
        } else {
            $pending = $repo->findPendingById($mailId);
            $mails = $pending !== null ? [$pending] : [];
        }

        foreach ($mails as $mail) {
            if (self::send(
                subject: $mail->getSubject(),
                body: $mail->getBody(),
                addresses: $mail->getAddresses(),
                from: $mail->getAdressat(),
                replyTos: [Env::LAMAIL],
            )) {
                self::setStatus($mail, 'versendet');
            } else {
                self::setStatus($mail, 'Fehler', "Siehe Logs.");
            }
        }
        Html::info('Mailbot wurde ausgeführt.');
    }


    /**
     * @throws Exception
     */
    public static function applyRecipients(PHPMailer $mailer, array $addresses): void
    {
        $useBcc = count($addresses) > 15;
        foreach ($addresses as $address) {
            $useBcc ? $mailer->addBCC($address) : $mailer->addAddress($address);
        }
    }

    private static function setStatus(nMailbot $mail, string $status, ?string $error = null): void
    {
        $mail->setMailStatus($status);
        $mail->setFehler($error);
        MailbotRepository::get()->flush();
    }

    public static function warning(): void
    {
        if (($count = MailbotRepository::get()->countFailed()) > 0) {
            Html::notice("Der Mailbot kann $count Mail(s) nicht versenden - siehe Datenbank.");
        }
    }
}
