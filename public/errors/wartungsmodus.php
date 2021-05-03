<?php

    if(!Env::WARTUNGSMODUS){
        header("Location: " . Env::BASE_URL . "/liga/neues.php");
        die();
    }

?>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Wartungsmodus</title>
    </head>
    <body>
        <div style="text-align: center;">
            <h1>Die Seite befindet sich zurzeit im Wartungsmodus.</h1>

            <p style="color: grey;">
                Bitte versuche es später erneut.
            </p>

            <p>
                <a href="<?= Env::BASE_URL ?>/liga/neues.php">Jetzt erneut versuchen.</a>
            </p>

            <p>
                Technikausschuss
                <br>
                <?= Env::TECHNIKMAIL ?>
            </p>
            <p>
                Ligaausschuss
                <br>
                <?= Env::LAMAIL ?>
            </p>
        </div>
    </body>
</html>