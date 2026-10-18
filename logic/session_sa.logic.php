<?php

use App\Ligaleitung\Service\LigaleitungService;

// Dies hier muss in jeder geschützten Seite des Schiricenters direkt nach init.php eingefügt werden!
// Der Ligaausschuss hat ebenfalls Zugriff auf das Schiricenter.
if (LigaleitungService::isLoggedIn("ligaausschuss")) {
    require __DIR__ . '/session_la.logic.php';
    return;
}

if (!LigaleitungService::isLoggedIn("schiriausschuss")) {
    $_SESSION['sc_redirect'] = db::escape($_SERVER['REQUEST_URI']); // Damit man nach dem Login direkt auf die gewünschte Seite geführt wird
    Html::info("Bitte logge dich als Schiriausschuss ein.");
    Helper::reload('/login.php?redirect');
}

HTML::$titel = 'Schiricenter';
