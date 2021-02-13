<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$titel = "Impressum | Deutsche Einradhockeyliga";
Config::$page_width = "500px";
Config::$content = "Das Impressum der Deutschen Einradhockeyliga findet sich hier.";
include '../../templates/header.tmp.php';
?>

<div class="w3-panel w3-card-4">
    <h1 class="w3-text-grey">Impressum</h1>
    <h2 class="w3-text-primary">Deutsche Einradhockeyliga</h2>

    <p class="w3-text-grey">Postanschrift</p>
    <p>Ansgar Pölking<br>Karlstraße 1<br>64283 Darmstadt</p>

    <p class="w3-text-grey">Kontakt</p>
    <p><?=Form::mailto(Config::LAMAIL)?></p>
</div>

<?php include '../../templates/footer.tmp.php';