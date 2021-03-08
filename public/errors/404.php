<?php
/*
 * Etwas wurde nicht gefunden.
 */
require_once '../../init.php';

$text = $_SESSION['error']['text'] ?? 'Es ist ein interner Fehler aufgetreten.';
$link = $_SESSION['error']['url'] ?? '';

unset ($_SESSION['error']);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <div class="w3-center">
        <h1 class="w3-text-primary">Diese Seite konnte nicht gefunden werden.</h1>

        <?php if (!empty($link)) { ?>
            <p class="grey">
                <?= $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $link ?>
            </p>
            <p>
                <?= Html::link($link, 'Erneut versuchen', icon:'settings_backup_restore') ?>
            </p>
        <?php } // end if ?>

        <p class="w3-text-grey">
            E-Mail-Adresse Technikausschuss
            <br>
            <?= Html::mailto(ENV::TECHNIKMAIL) ?>
        </p>
    </div>

<?php include Env::BASE_PATH . '/templates/footer.tmp.php';
