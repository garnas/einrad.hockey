<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$text = $_SESSION['error']['text'] ?? 'Es ist ein interner Fehler aufgetreten.';
$link = $_SESSION['error']['url'] ?? Env::BASE_URL;

unset ($_SESSION['error']);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

include_once Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <div class="w3-center" style="display:block!important;">
        <h1 class="w3-text-primary">Hier hat etwas nicht funktioniert!</h1>

        <p class="w3-text-red">
            <b><?= $text ?></b>
        </p>

        <p>
            <?= Form::link($link, 'Erneut versuchen', icon:'settings_backup_restore') ?>
        </p>

        <p class="w3-text-grey">
            E-Mail-Adresse Technikausschuss
            <br>
            <?= Form::mailto(ENV::TECHNIKMAIL) ?>
        </p>
    </div>

<?php include_once Env::BASE_PATH . '/templates/footer.tmp.php';
