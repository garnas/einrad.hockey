<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

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
<h2 class="w3-text-primary"><?= Html::icon("perm_identity", tag:"h2") ?>  Ligaausschuss</h2>
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

<p>
    Der Ligaausschuss besteht aus sechs Mitgliedern und dient als Ansprechpartner der Deutschen Einradhockeyliga. Er ist
    verantwortlich für die Weiterentwicklung der Liga und für den reibungslosen Saisonablauf. Wenn es mehr Bewerber als
    Ligaausschussplätze gibt, wird er jeweils gegen Ende der Saison von allen Ligateams gewählt.
</p>

<p class="w3-text-grey">Kontakt: <?= Html::mailto(Env::LAMAIL) ?></p>

<p class="w3-margin" style="max-width: 666px">
    <a href="../bilder/ligaausschuss2020.jpg">
        <img src="../bilder/ligaausschuss2020.jpg" alt="Ligaausschuss 2020" class="w3-image w3-card-4">
    </a>
    Das Bild des Ligaausschusses der Saison 2020.
</p>

<!-- Technik -->
<h2 class="w3-text-primary"><?= Html::icon("engineering", tag:"h2") ?> Technikausschuss</h2>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach (LigaLeitung::get_all('technikausschuss') as $tk){?>
        <tr>
            <td class="w3-padding-small"><?= $tk['vorname'] . ' ' . $tk['nachname'] ?></td>
            <td class="w3-padding-small"><i class="w3-text-grey"><?= $tk['teamname'] ?></i>
            </td>
        </tr>
        <?php } //end foreach?>
    </table>
</div>
<p>
    Der Technikausschuss ist verantwortlich für die Instandhaltung und Weiterentwicklung der IT der Deutschen
    Einradhockeyliga.
</p>
<p class="w3-text-grey">Kontakt: <?=Html::mailto(Env::TECHNIKMAIL)?></p>

<!-- Öffntlichkeits-Ausschuss -->
<h2 class="w3-text-primary"><?= Html::icon("public", tag:"h2") ?> Öffentlichkeitsausschuss</h2>
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
<p>
    Der Öffentlichkeitsausschuss ist relativ neu und nicht offiziell im Ligamodus vertreten. Er soll den
    Einradhockeysport nach außen hin präsentieren und sich um unsere Socialmedia-Accounts kümmern.
</p>
<p class="w3-text-grey">Kontakt: <?=Html::mailto(Env::OEFFIMAIL)?></p>

<!-- Schiri-Ausschuss -->
<h2 class="w3-text-primary"><?= Html::icon("sports", tag:"h2") ?> Schiedsrichterausschuss</h2>
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
<p>
    Der Schiedsrichterausschuss ist für die Organisation der Aus- und Weiterbildung der Schiedsrichter in der Deutschen
    Einradhockeyliga verantwortlich.
</p>
<p class="w3-text-grey">Kontakt: <?=Html::mailto(Env::SCHIRIMAIL)?></p>
<h3 class="w3-text-primary"><?= Html::icon("school", tag:"h3") ?> Schiedsrichterausbilder</h3>
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
<p>Dank unserer Ausbilder haben wir zurzeit <?=Spieler::get_schiris_anzahl()?> Schiedsrichter in der Deutschen Einradhockeyliga.</p>

<?php include '../../templates/footer.tmp.php';