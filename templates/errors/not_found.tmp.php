<?php

include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <div class="w3-center">
        <h1 class="w3-text-primary">Diese Seite konnte nicht gefunden werden!</h1>

        <p class="w3-text-red">
            <b><?= $text ?? 'Ungültiger Link' ?></b>
        </p>

        <p class="w3-text-grey">
            Du kannst <?= Form::mailto(ENV::TECHNIKMAIL) ?> anschreiben, um diesen Fehler zu melden. Wir versuchen ihn
            dann zu beheben.
        </p>
    </div>

<?php include Env::BASE_PATH . '/templates/footer.tmp.php';