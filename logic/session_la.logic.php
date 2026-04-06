<?php

// Dies hier muss in jeder geschützten Seite direkt nach init.php eingefügt werden!
use App\Service\Mail\MailService;

if (!isset($_SESSION['logins']['la'])) {
    $_SESSION['lc_redirect'] = db::escape($_SERVER['REQUEST_URI']); //Damit man nach dem Login direkt auf die gewünschte Seite geführt wird
    Html::info("Bitte logge dich als Ligaausschuss ein.");
    Helper::reload('/login.php?redirect');

}
MailService::warning(); // Sendet eine Warnung, wenn Mails nicht versendet werden konnten.

HTML::$titel = 'Ligacenter';

Helper::$ligacenter = true; // Dies zeigt allen Dateien (insbeondere .tmp.php) , das man sich im Ligacenter befindet.
