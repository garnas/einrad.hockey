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
    <div><img src="<?= Env::BASE_URL ?>/bilder/ligaausschuss.jpg" alt="Ligaausschuss" style="width: 100%; max-width: 600px"></div>
    <div><i class="w3-text-grey" style="display: block; margin: auto; width: 100%; max-width:600px">Ligaausschuss der Saison <?= Html::get_saison_string() ?>: Larissa, Matthias (ausgeschieden), Max, Malte, Janina, Fin (von links nach rechts)</i></div>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('ligaausschuss') as $la): ?>
        <div class="w3-row">
            <div class="w3-quarter">
                <?= Html::mailto($la['email'], $la ['vorname'] . ' ' . $la['nachname']) ?>
            </div>
            <div class="w3-quarter w3-text-grey">
                <span class="w3-padding-small"><?= $la['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::LAMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> Ligaausschuss
    </a>
</div>

<hr>

<!-- Team Technik -->
<h2 class="w3-text-secondary w3-xlarge">Team Technik</h2>

<div class="w3-section">
    <p>
        Das Team Technik kümmert sich um die technische Infrastruktur der Deutschen Einradhockeyliga.
        Hierzu gehört vor allem die Webseite mit den umfassenden Funktionen.
        Diese Arbeit wird stetig fortgesetzt, um den Ligabetrieb reibungslos zu ermöglichen.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('team_technik') as $tk): ?>
        <div class="w3-row">
            <div class="w3-quarter">
                <?= $tk['email'], $tk['vorname'] . ' ' . $tk['nachname'] ?>
            </div>
            <div class="w3-threequarter w3-text-grey">
                <span class="w3-padding-small"><?= $tk['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::TECHNIKMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> Team Technik
    </a>
</div>

<hr>

<!-- Team Social Media -->
<h2 class="w3-text-secondary w3-xlarge">Team Social Media</h2>

<div class="w3-section">
    <p>
        Das Team Social Media kümmert sich um die Präsenz der Deutschen Einradhockeyliga in den sozialen Medien.
        Hier wird vor allem über die Turniere und Meisterschaften berichtet, aber auch über die wichtigsten Neuigkeiten.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('team_social_media') as $oa): ?>
        <div class="w3-row">
            <div class="w3-quarter">
                <?= $oa['email'], $oa['vorname'] . ' ' . $oa['nachname'] ?>
            </div>
            <div class="w3-threequarter w3-text-grey">
                <span class="w3-padding-small"><?= $oa['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::OEFFIMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> Team Social Media
    </a>
</div>

<hr>

<!-- Team Praktische Schiriprüfer -->
<h2 class="w3-text-secondary w3-xlarge">Team Praktische Schiriprüfer</h2>

<div class="w3-section">
    <p>
        In einem Kernteam werden die praktischen Schiedsrichterprüfungen organisiert.
        Neben der Entwicklung einer Schiedsrichterleitlinie für fortgeschrittenes Niveau     liegt der Fokus auf der Vereinheitkung der Prüfungsdurchführung.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('team_schiripruefer') as $sp): ?>
        <div class="w3-row">
            <div class="w3-quarter">
                <?= $sp['email'], $sp['vorname'] . ' ' . $sp['nachname'] ?>
            </div>
            <div class="w3-threequarter w3-text-grey">
                <span class="w3-padding-small"><?= $sp['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::LAMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> Team Praktische Schiriprüfer
    </a>
</div>

<hr>

<!-- Team Jugendarbeit -->
<h2 class="w3-text-secondary w3-xlarge">Team Jugendarbeit</h2>

<div class="w3-section">
    <p>
        Es geht darum, über die Vereine hinaus, Angebote für Jugendliche zu schaffen.
        Beispielsweise können gemeinsame Trainingseinheiten mit Übernachtungen organisiert werden.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('team_jugendarbeit') as $sp): ?>
        <div class="w3-row">
            <div class="w3-quarter">
                <?= $sp['email'], $sp['vorname'] . ' ' . $sp['nachname'] ?>
            </div>
            <div class="w3-threequarter w3-text-grey">
                <span class="w3-padding-small"><?= $sp['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::LAMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> Team Jugendarbeit
    </a>
</div>

<hr>

<!-- Team Branding und Merch -->
<h2 class="w3-text-secondary w3-xlarge">Team Branding und Merch</h2>

<div class="w3-section">
    <p>
        Ziel ist es, für eine einheitliche Präsenz der Deutschen Einradhockeyliga nach innen und nach außen zu sorgen.
        Dazu gehört die Neu- oder Weiterentwicklung eines Ligalogos, aber auch Werbemittel.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('team_branding_merch') as $sp): ?>
        <div class="w3-row">
            <div class="w3-quarter">
                <?= $sp['email'], $sp['vorname'] . ' ' . $sp['nachname'] ?>
            </div>
            <div class="w3-threequarter w3-text-grey">
                <span class="w3-padding-small"><?= $sp['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
    
    <div class="w3-row">
        <div class="w3-quarter">Denis Rätzel</div>
    </div>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::LAMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> Team Branding und Merch
    </a>
</div>

<hr>

<!-- Team Schirileitfaden -->
<h2 class="w3-text-secondary w3-xlarge">Team Schirileitfaden</h2>

<div class="w3-section">
    <p>
        Hier wird eine Leitfaden entwickelt, der als Vorbereitung zur theoretischen Prüfung dienen soll. 
        Dieser ist ergänzend zum eigentlichen Regelwerk und soll einige Regeln genauer erörtern. 
        Dieser Leitfaden soll auch in angemessener oder leicht verständlicher Sprache ausgearbeitet werden.
    </p>
</div>

<div class="w3-section">
    <?php foreach (LigaLeitung::get_all('team_schirileitfaden') as $sp): ?>
        <div class="w3-row">
            <div class="w3-quarter">
                <?= $sp['email'], $sp['vorname'] . ' ' . $sp['nachname'] ?>
            </div>
            <div class="w3-threequarter w3-text-grey">
                <span class="w3-padding-small"><?= $sp['teamname'] ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="w3-section">
    <a href="mailto:<?= Env::LAMAIL ?>" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
        <?= Html::icon("mail") ?> Team Schirileitfaden
    </a>
</div>

<hr>

<!-- Team Juniortests -->
<h2 class="w3-text-secondary w3-xlarge">Team Juniortests (Abgeschlossen)</h2>
Das Team überarbeitete die insgesamt 241 Fragen des Juniorschiritests.
Ziel war es, die Inhalte zu aktualisieren und sprachlich zu verbessern.
Siehe auch: <a href="/schiricenter/schiri-infos.php" class="no w3-text-primary w3-hover-text-secondary">Schiedsrichter-Infos</a>.

<hr>

<!-- Team Traineraustausch -->
<h2 class="w3-text-secondary w3-xlarge">Team Traineraustausch (Abgeschlossen)</h2>
Das Team erstellte ein Wiki-Dokument mit verschiedenen Übungen für das eigene Training.
Ziel war es, eine zentrale und übersichtliche Sammlung von Trainingsmaterialien bereitzustellen.
Siehe auch: <a href="https://tinyurl.com/bddm2yd6" target="_blank" class="no w3-text-primary w3-hover-text-secondary">Wiki-Dokument</a>.


<hr>

<!-- Team IUF -->
<h2 class="w3-text-secondary w3-xlarge">Team IUF (Abgeschlossen)</h2>
Das Team war im IUF Rulebook Committee an der Überarbeitung des internationalen Regelwerks beteiligt.
Ziel war es, die Regeln im Hinblick auf die Weltmeisterschaft 2026 zu aktualisieren und zu präzisieren.
Siehe auch: <a href="https://unicycling.org/publications/" target="_blank" class="no w3-text-primary w3-hover-text-secondary">IUF Rulebook</a>.

<?php include '../../templates/footer.tmp.php';