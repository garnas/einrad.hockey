<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Ligaleitung | Deutsche Einradhockeyliga";
Html::$content = "Übersicht über die Ausschüsse der Deutschen Einradhockeyliga, welche die Liga leiten.";
include '../../templates/header.tmp.php'; ?>

<h1 class="w3-text-grey">Ligaleitung der Saison <?=Html::get_saison_string()?></h1>
<p class="w3-text-grey">
    Die Liga wird von den hier aufgelisteten Gremien geleitet. Wenn du auch in einem der Ausschüsse die Einradhockeyliga
    weiterentwickeln möchtest, kannst du dich jederzeit beim Ligaauschuss melden.
</p>
<!-- Liga -->
<h2 class="w3-text-primary"><?= Html::icon("perm_identity", tag:"h2") ?>Ligaausschuss</h2>
<p>
    Der Ligaausschuss besteht aus sechs Mitgliedern und dient als Ansprechpartner der Deutschen Einradhockeyliga. Er ist
    verantwortlich für die Weiterentwicklung der Liga und für den reibungslosen Saisonablauf. Er wird alle zwei Saisons von den Ligateams gewählt,
    wenn es mehr Bewerber als Ligaausschussplätze gibt.
</p>
<p class="w3-margin" style="max-width: 666px">
    <a href="<?= Env::BASE_URL ?>/bilder/ligaausschuss.jpg">
        <img src="<?= Env::BASE_URL ?>/bilder/ligaausschuss.jpg" alt="Ligaausschuss" class="w3-image w3-card-4">
    </a>
    Das Bild des Ligaausschusses der Saison <?= Html::get_saison_string() ?>.
</p>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach (LigaLeitung::get_all('ligaausschuss') as $la){?>
            <tr>
                <td class="w3-padding-small">
                    <?= Html::mailto($la['email'], $la ['vorname'] . ' ' . $la['nachname']) ?>
                </td>
                <td class="w3-padding-small"><i class="w3-text-grey"><?= $la['teamname'] ?></i></td>
            </tr>
        <?php } //end foreach?>
    </table>
</div>
<a href="mailto:<?= Env::LAMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
    <?= Html::icon("mail") ?> <?= Env::LAMAIL ?>
</a>

<!-- Technik -->
<h2 class="w3-text-primary"><?= Html::icon("engineering", tag:"h2") ?> Technikausschuss</h2>
<p>
    Der Technikausschuss ist verantwortlich für die Instandhaltung und Weiterentwicklung der IT der Deutschen
    Einradhockeyliga. Dazu gehört unter anderem die Aktualisierung der technischen Werkzeuge entsprechend des Ligamodus.
</p>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach (LigaLeitung::get_all('technikausschuss') as $tk){?>
            <tr>
                <td class="w3-padding-small"><?= $tk['vorname'] . ' ' . $tk['nachname'] ?></td>
                <td class="w3-padding-small"><i class="w3-text-grey"><?= $tk['teamname'] ?></i></td>
            </tr>
        <?php } //end foreach?>
    </table>
</div>
    <a href="mailto:<?= Env::TECHNIKMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> <?= Env::TECHNIKMAIL ?>
    </a>

<!-- Öffntlichkeits-Ausschuss -->
<h2 class="w3-text-primary"><?= Html::icon("public", tag:"h2") ?> Öffentlichkeitsausschuss</h2>
<p>
    Der Öffentlichkeitsausschuss ist zuständig für die redaktionellen Inhalte der Internetseite. Darüber hinaus organisiert er Veranstaltungen und informiert über die Social-Media-Kanäle der Deutschen Einradhockeyliga.
</p>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach (LigaLeitung::get_all('oeffentlichkeitsausschuss') as $oa){?>
            <tr>
                <td class="w3-padding-small"><?= $oa['vorname'] . ' ' . $oa['nachname']?></td>
                <td class="w3-padding-small"><i class="w3-text-grey"><?= $oa['teamname'] ?></i></td>
            </tr>
        <?php } //end foreach?>
    </table>
</div>
<a href="mailto:<?= Env::OEFFIMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
    <?= Html::icon("mail") ?> <?= Env::OEFFIMAIL ?>
</a>

<!-- Schiri-Ausschuss -->
<h2 class="w3-text-primary"><?= Html::icon("sports", tag:"h2") ?> Schiedsrichterausschuss</h2>
<p>
    Um die Aus- und Weiterbildung von Schiedsrichtern in der Deutschen Einradhockeyliga zu fördern und zu organisieren, 
    wird durch den Ligaausschuss ein Schiedsrichterausschuss gebildet. Allgemeine Anfragen oder Anregungen zu dem Thema 
    Schiedsrichterausbildung oder Fragen zum Regelwerk können an ihn gerichtet werden.
</p>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach (LigaLeitung::get_all('schiriausschuss') as $sa){?>
            <tr>
                <td class="w3-padding-small"><?=$sa['vorname'] . ' ' . $sa['nachname']?></td>
                <td class="w3-padding-small"><i class="w3-text-grey"><?= $sa['teamname'] ?></i></td>
            </tr>
        <?php } //end foreach?>
    </table>
</div>
    <a href="mailto:<?= Env::SCHIRIMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> <?= Env::SCHIRIMAIL ?>
    </a>
<h3 class="w3-text-primary"><?= Html::icon("school", tag:"h3") ?> Schiedsrichterausbilder</h3>
<p>
    Der Schiedsrichterausschuss ernennt in seiner Funktion Schiedsrichterprüfer. Diese dürfen Spielerinnen und Spielen 
    die theoretische und auch praktische Prüfung abnehmen. Folgende Personen sind momentan als Prüferinnen und Prüfer ernannt.
</p>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach (Ligaleitung::get_all('schiriausbilder') as $ausbilder){?>
            <tr>
                <td class="w3-padding-small"><?=$ausbilder['vorname'] . ' ' . $ausbilder['nachname']?></td>
                <td class="w3-padding-small"><i class="w3-text-grey"><?= $ausbilder['teamname'] ?></i></td>
            </tr>
        <?php } //end foreach?>
    </table>
</div>
<p>Dank unserer Ausbilder haben wir zurzeit <?=Stats::get_schiris_anzahl()?> Schiedsrichter in der Deutschen Einradhockeyliga.</p>

<?php include '../../templates/footer.tmp.php';