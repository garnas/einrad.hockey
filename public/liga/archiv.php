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

    <h1 class="w3-text-primary">Saison-Archiv der Deutschen Einradhockeyliga</h1>
    <p><?= Html::link('ergebnisse.php?saison=29', 'Turniere der Saison 2023/2024') ?></p>
    <p><?= HTML::link('tabelle.php?saison=29', 'Tabelle der Saison 2023/2024') ?></p>
    <hr>
    <p><?= Html::link('ergebnisse.php?saison=28', 'Turniere der Saison 2022/2023') ?></p>
    <p><?= HTML::link('tabelle.php?saison=28', 'Tabelle der Saison 2022/2023') ?></p>
    <hr>
    <p><?= Html::link('ergebnisse.php?saison=27', 'Turniere der Saison 2021/2022') ?></p>
    <p><?= HTML::link('tabelle.php?saison=27', 'Tabelle der Saison 2021/2022') ?></p>
    <hr>
    <p><?= HTML::link('tabelle.php?saison=26', 'Tabelle der Saison 2020/2021') ?></p>
    <p><?= Html::link('ergebnisse.php?saison=26', 'Turniere der Saison 2020/2021') ?></p>
    <hr>
    <iframe src="<?= Nav::LINK_ARCHIV ?>" style="width:100%;height:800px;" class="archiv w3-border-0"
            title="Archiv der Deutschen Einradhockeyliga"></iframe>

    <h1 class="w3-text-primary">Weitere archivierte Ergebnisse</h1>
    
    <table class="w3-border w3-border-light-grey w3-table w3-striped">
    <tr class="w3-primary">
        <th>Datum</th>
        <th>Veranstaltung</th>
        <th>Spielplan / Ergebnisse</th>
    </tr>
    <tr>
        <td>31.05. + 01.06.2025</td>
        <td>2. Lilienthaler 24 Stunden Einradhockeyturnier</td>
        <td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/spielplaene/2025_Lilienthal_24h-Turnier.pdf'>Ergebnisse</a></td>
    </tr>
</table>

<?php include '../../templates/footer.tmp.php';