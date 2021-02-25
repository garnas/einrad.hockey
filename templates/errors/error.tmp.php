<?php include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <div class="w3-center">
        <h1 class="w3-text-primary">Hier hat etwas nicht funktioniert!</h1>

        <p class="w3-text-red">
            <b><?= $text ?? 'Es ist ein interner Fehler aufgetreten.' ?></b>
        </p>

        <p class="w3-text-grey">
            Du kannst <?= Form::mailto(ENV::TECHNIKMAIL) ?> anschreiben, um diesen Fehler zu melden.
        </p>
    </div>

<?php include Env::BASE_PATH . '/templates/footer.tmp.php';