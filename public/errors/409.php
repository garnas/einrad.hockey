<?php
/*
 * Es ist ein Konflikt aufgetreten. Also: Irgendwas passt nicht zusammen.
 * ==> trigger_error(..., E_USER_ERROR)
 */
require_once '../../init.php';

$text = $_SESSION['error']['text'] ?? 'Es ist ein Konflikt aufgetreten.';
$link = $_SESSION['error']['url'] ?? '';

unset ($_SESSION['error']);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <div class="w3-center">
        <h1 class="w3-text-primary">Hier passte etwas nicht</h1>
        <p class="w3-text-red">
            <?= $text ?>
        </p>

        <?php if (!empty($link)) { ?>
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
