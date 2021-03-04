<?php
/*
 * PHP ist hat einen Fehler oder es liegt ein anderer Server-Fehler vor.
 */
require_once __DIR__ . "/../../system/ini_set.php";
require_once __DIR__ . "/../../env.php";
session_start();

$text = $_SESSION['error']['text'] ?? 'Es ist ein interner Fehler aufgetreten.';
$link = $_SESSION['error']['url'] ?? Env::BASE_URL;

unset ($_SESSION['error']);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
?>

<div style="text-align: center;">
    <h1>Hier hat etwas nicht funktioniert</h1>

    <p style="color: red;">
        <b><?= $text ?></b>
    </p>

    <p>
        <a href="<?= $link ?>">Erneut versuchen</a>
    </p>

    <p>
        Technikausschuss
        <br>
        <?= ENV::TECHNIKMAIL ?>
    </p>
    <p>
        Ligaausschuss
        <br>
        <?= ENV::LAMAIL ?>
    </p>
</div>