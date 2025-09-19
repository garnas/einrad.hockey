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

<h1 class="w3-text-primary">Ligaleitung</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string()?></p>

<div class="w3-section">
    <p>
        Die Liga wird von den hier aufgelisteten Gremien geleitet. Wenn du auch in einem der Teams die Deutsche Einradhockeyliga
        weiterentwickeln möchtest, kannst du dich jederzeit beim Ligaausschuss melden.
    </p>
</div>

<!-- Liga -->
<h2 class="w3-text-secondary w3-xlarge">Ligaausschuss</h2>
<div class="w3-section">
    <p>
        Der Ligaausschuss besteht aus bis zu sechs Mitgliedern und dient als Ansprechpartner der Deutschen Einradhockeyliga. Er ist
        verantwortlich für die Weiterentwicklung der Liga und für den reibungslosen Saisonablauf. Er wird alle zwei Saisons von den Ligateams gewählt,
        wenn es mehr Bewerber als Ligaausschussplätze gibt.
    </p>
</div>

<div class="w3-section w3-center">
    <div>
        <img src="<?= Env::BASE_URL ?>/bilder/ligaausschuss.jpg" alt="Ligaausschuss" class="w3-image">
    </div>
    <div>
        <i class="w3-text-grey">Ligaausschuss der Saison <?= Html::get_saison_string() ?>: Larissa, Matthias (ausgeschieden), Max, Malte, Janina, Fin (von links nach rechts)</i>
    </div>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('ligaausschuss') as $la): ?>
        <div class="w3-row">
            <div class="w3-quarter">
                <?= Html::mailto($la['email'], $la ['vorname'] . ' ' . $la['nachname']) ?>
            </div>
            <div class="w3-quarter">
                <span class="w3-padding-small"><?= $la['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::LAMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> E-Mail an den Ligaausschuss
    </a>
</div>

<hr>

<!-- Technikteam -->
<h2 class="w3-text-secondary w3-xlarge">Technikteam</h2>
<div class="w3-section">
    <p>
        Das Technikteam ist verantwortlich für die Instandhaltung und Weiterentwicklung der IT der Deutschen
        Einradhockeyliga. Dazu gehört unter anderem die Aktualisierung der technischen Werkzeuge entsprechend des Ligamodus.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('technikausschuss') as $tk): ?>
        <div class="w3-row">
            <div class="w3-quarter w3-text-primary">
                <?= $tk['email'], $tk['vorname'] . ' ' . $tk['nachname'] ?>
            </div>
            <div class="w3-threequarter">
                <span class="w3-padding-small"><?= $tk['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::TECHNIKMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> E-Mail an das Technikteam
    </a>
</div>

<hr>

<!-- Öffentlichkeitsteam -->
<h2 class="w3-text-secondary w3-xlarge">Öffentlichkeitsteam</h2>
<div class="w3-section">
    <p>
        Das Öffentlichkeitsteam ist zuständig für die redaktionellen Inhalte der Internetseite. Darüber hinaus organisiert es Veranstaltungen und informiert über die Social-Media-Kanäle der Deutschen Einradhockeyliga.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('oeffentlichkeitsausschuss') as $oa): ?>
        <div class="w3-row">
            <div class="w3-quarter w3-text-primary">
                <?= $oa['email'], $oa['vorname'] . ' ' . $oa['nachname'] ?>
            </div>
            <div class="w3-threequarter">
                <span class="w3-padding-small"><?= $oa['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::OEFFIMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> E-Mail an das Öffentlichkeitsteam
    </a>
</div>

<hr>

<!-- Team Praktische Schiriprüfer -->
<h2 class="w3-text-secondary w3-xlarge">Team Praktische Schiriprüfer</h2>
<div class="w3-section">
    <p>
        Das Team für die praktische Schiriprüfung ist verantwortlich für die Organisation und Durchführung der praktischen Prüfungen für Schiedsrichter in der Deutschen Einradhockeyliga.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('schiripruefer') as $sp): ?>
        <div class="w3-row">
            <div class="w3-quarter w3-text-primary">
                <?= $sp['email'], $sp['vorname'] . ' ' . $sp['nachname'] ?>
            </div>
            <div class="w3-threequarter">
                <span class="w3-padding-small"><?= $sp['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::LAMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> E-Mail an das Team Praktische Schiriprüfer
    </a>
</div>

<?php include '../../templates/footer.tmp.php';