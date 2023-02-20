<?php

use App\Entity\Sonstiges\nMailbot;
use App\Repository\Mailbot\MailbotRepository;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

include '../../templates/header.tmp.php'; ?>


    <h1>Alle Mailbot-Mails der letzten 30 Tage</h1>
    <h3 class="w3-bottombar"></h3>
<?php
$mails = MailbotRepository::get()->findAll();
/** @var nMailbot[] $mails */
$mails = array_reverse($mails, true);

foreach ($mails as $mail) {
    if ($mail->getZeit()->getTimestamp() > time() - 90 * 24 * 60 * 60) { ?>
        <p class="w3-center"><em>Status</em></p>
        <p class="w3-center"><?=$mail->getMailStatus()?> <?=$mail->getZeit()->format("Y-m-d H:i:s")?></p>
        <p class="w3-center"><strong>An</strong></p>
        <p class="w3-center"><?=$mail->getAdressat()?></p>
        <p class="w3-center"><strong>Betreff</strong></p>
        <p class="w3-center"><?=$mail->getBetreff()?></p>
        <p><strong>Inhalt</strong></p>
        <p><?=$mail->getInhalt()?></p>
        <h3 class="w3-bottombar"></h3>
    <?php }
}

include '../../templates/footer.tmp.php';