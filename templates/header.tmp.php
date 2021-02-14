<!DOCTYPE html>

<html id="myHtml" class="w3-light-grey" style="overflow-y: scroll;" lang="de">
<head>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= Config::BASE_LINK ?>/bilder/favicon/mstile-144x144.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="Einradhockey, Einrad, Einradfahren, Einradhockeyliga, Hockey, Sport, ungewöhnlich, kreativ">
    <meta name="description" content="<?= Config::$content ?>">
    <link rel="shortcut icon" href="<?= Config::BASE_LINK ?>/bilder/favicon/favicon.png">
    <link rel="icon" type="image/png" href="<?= Config::BASE_LINK ?>/bilder/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?= Config::BASE_LINK ?>/bilder/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?= Config::BASE_LINK ?>/bilder/favicon/android-icon-192x192.png" sizes="192x192">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= Config::BASE_LINK ?>/bilder/favicon/apple-icon-180x180.png">
    <link type="text/css" rel="stylesheet" href="<?= Config::BASE_LINK ?>/css/normalize.css">
    <link type="text/css" rel="stylesheet" href="<?= Config::BASE_LINK ?>/css/w3.css">
    <link type="text/css" rel="stylesheet" href="<?= Config::BASE_LINK ?>/css/style.css?v=20201126">
    <link type="text/css" rel="stylesheet" href="<?= Config::BASE_LINK ?>/css/icons/icons.css">
    <script src="<?= Config::BASE_LINK ?>/javascript/script.js?v=20201214"></script>
    <title><?= Config::$titel ?></title>
</head>

<body class="w3-white w3-auto w3-card-4">
<main id="main_body" class="content">
    <div class="w3-hide-large w3-hide-medium">
        <?php include "nav_mobil.tmp.php"; ?>
    </div>
    <div class="w3-hide-small">
        <?php include "nav_desktop.tmp.php"; ?>
    </div>

    <?php
    // Hiermit wird die Leiste angezeigt, wenn man eingeloggt ist
    if (isset($_SESSION['la_id'])) include 'nav_ligacenter.tmp.php';
    if (isset($_SESSION['team_id'])) include 'nav_teamcenter.tmp.php';
    ?>

    <!-- Zentrierung der Webseite und Breite mit welcher diese dargestellt werden soll -->
    <div class="w3-content" style="max-width:<?= Config::$page_width ?>;">
        <div class="w3-container">
<?php // Fehlermeldungen darstellen
Form::print_messages();
            
            
