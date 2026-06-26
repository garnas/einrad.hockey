<!DOCTYPE html>

<html id="myHtml" class="w3-light-grey" style="overflow-y: scroll;" lang="de">
<head>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= Env::BASE_URL ?>/bilder/favicon/mstile-144x144.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no;user-scalable=0">
    <meta name="keywords" content="Einradhockey, Einrad, Einradfahren, Einradhockeyliga, Hockey, Sport, ungewöhnlich, kreativ">
    <meta name="description" content="<?= Html::$content ?>">
    <link rel="shortcut icon" href="<?= Env::BASE_URL ?>/bilder/favicon/favicon.png">
    <link rel="icon" type="image/png" href="<?= Env::BASE_URL ?>/bilder/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?= Env::BASE_URL ?>/bilder/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?= Env::BASE_URL ?>/bilder/favicon/android-icon-192x192.png" sizes="192x192">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= Env::BASE_URL ?>/bilder/favicon/apple-icon-180x180.png">
    <link type="text/css" rel="stylesheet" href="<?= Env::BASE_URL ?>/css/normalize.css">
    <link type="text/css" rel="stylesheet" href="<?= Env::BASE_URL ?>/css/w3.css">
    <link type="text/css" rel="stylesheet" href="<?= Env::BASE_URL ?>/css/style.css?v=20251126">
    <link type="text/css" rel="stylesheet" href="<?= Env::BASE_URL ?>/css/icons/icons.css">
    <script src="<?= Env::BASE_URL ?>/javascript/script.js?v=20250212"></script>
    <script src="<?= Env::BASE_URL ?>/javascript/360search/360search.js?v=20210308" async></script>

    <title><?= e(Html::$titel) ?></title>
</head>

<body class="w3-white w3-auto w3-card-4">
<main id="main_body" class="content">
    <div class="w3-hide-large">
        <?php include 'navigation/nav_mobil.tmp.php'; ?>
    </div>
    <div class="w3-hide-small w3-hide-medium">
        <?php include 'navigation/nav_desktop.tmp.php'; ?>
    </div>

    <?php
    // Hiermit wird die Leiste angezeigt, wenn man eingeloggt ist
    if (LigaLeitung::is_logged_in("ligaausschuss")) {
        include 'navigation/nav_ligacenter.tmp.php';
    }
    if (LigaLeitung::is_logged_in("team_social_media")) {
        include 'navigation/nav_oefficenter.tmp.php';
    }
    if (isset($_SESSION['logins']['team'])) {
        include 'navigation/nav_teamcenter.tmp.php';
    }
    ?>
    <!-- Zentrierung der Webseite und Breite mit welcher diese dargestellt werden soll -->
    <div class="w3-content" style="max-width:<?= Html::$page_width ?>;">
        <div class="w3-container">
            <?php Html::print_messages(); ?>