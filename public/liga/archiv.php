<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = 'Archiv | Deutschen Einradhockeyliga';
Html::$content = 'Hier kann man die Ergebnisse und Tabellen seit der ersten Saison im Jahr 1995 sehen.';
include '../../templates/header.tmp.php';
?>

    <!-- Archiv -->
    <h1 class="w3-text-primary">Archiv</h1>
    <p><?= HTML::link('tabelle.php?saison=26', 'Tabelle der Saison 2020/21') ?></p>
    <p><?= Html::link('ergebnisse.php?saison=26', 'Turniere der Saison 2020/21') ?></p>

    <!-- iframes sind ein sonderfall, html5 depreciated -->
    <iframe src="<?= Nav::LINK_ARCHIV ?>" style="width:100%;height:800px;" class="archiv w3-border-0"
            title="Archiv der Deutschen Einradhockeyliga"></iframe>

<?php include '../../templates/footer.tmp.php';