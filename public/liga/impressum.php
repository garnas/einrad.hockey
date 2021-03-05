<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Impressum | Deutsche Einradhockeyliga";
Html::$content = "Das Impressum der Deutschen Einradhockeyliga findet sich hier.";
include '../../templates/header.tmp.php'; ?>

    <div class="w3-panel w3-center">
        <h1 class="w3-text-grey">Impressum</h1>
        <h2 class="w3-text-primary">Deutsche Einradhockeyliga</h2>

        <p class="w3-text-grey">Postanschrift</p>
        <p>Ansgar Pölking<br>Karlstraße 1<br>64283 Darmstadt</p>

        <p class="w3-text-grey">Kontakt</p>
        <p><?= Html::mailto(Env::LAMAIL) ?></p>

        <h3>Du hast Lust an der Webseite mitzuwirken?</h3>
        <p><?= Html::link(Nav::LINK_GIT, 'Github-Account', true, 'launch') ?></p>
        <p><?= Html::mailto(Env::TECHNIKMAIL) ?></p>
    </div>

<?php include '../../templates/footer.tmp.php';