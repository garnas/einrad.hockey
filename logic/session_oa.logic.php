<?php
// Dies hier muss in jeder geschützten Seite direkt nach init.php eingefügt werden!
if (!LigaLeitung::is_logged_in("team_social_media")) {
  $_SESSION['lc_redirect'] = db::escape($_SERVER['REQUEST_URI']); // Damit man nach dem Login direkt auf die gewünschte Seite geführt wird
    Html::info("Bitte logge dich als Öffentlichkeitsausschuss ein.");
    Helper::reload('/login.php?redirect');
}

HTML::$titel = 'Öffentlichkeitscenter';

Helper::$team_social_media = true; // Dies zeigt allen Dateien (insbeondere .tmp.php) , das der User sich im Oefficenter befindet.