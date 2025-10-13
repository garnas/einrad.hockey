<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Schiedsrichter-Infos | Deutsche Einradhockeyliga";
Html::$content = "Infos für Schiedsrichter und alle, die es werden wollen.";
include '../../templates/header.tmp.php';
?>

<?php if (Env::DEBUGMODUS) { # Start Debug Modus ?>
    <P><div class="w3-ul w3-pale-red w3-bordered">
        DEBUGGING:
        <?=Html::link("../schiricenter/uebungstest_l.php", "Einzelne Fragen",
                      false, false)?> /
        <?=Html::link("../schiricenter/uebungstest_j.php", "Übungstest (Junior)",
                      false, false)?> /
        <?=Html::link("../schiricenter/uebungstest_b.php", "Übungstest (Basis)",
                      false, false)?> /
        <?=Html::link("../schiricenter/uebungstest_f.php", "Übungstest (Fortgeschrittene)",
                      false, false)?>
    </div>
<?php } # Ende Debug Modus ?> 

<h1 class="w3-text-primary">Schiedsrichterprüfungen der Deutschen Einradhockeyliga</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string()?></p>

<div class="w3-section">
    <h2 class="w3-text-secondary w3-xlarge">Prüfungen</h2>
    <p>Die Schiedsrichterprüfung besteht aus einem theoretischen und einem praktischen Teil:</p>
</div>

<div class="w3-section">
    <div class="w3-panel w3-light-grey">
        <p>
            <b>Theoretische Prüfung: Online Multiple Choice Test</b>
        </p>
        <p>
            Die theoretische Prüfung wird online in Form eines Multiple Choice Tests abgelegt. 
            Der Prüfling hat 30&nbsp;Minuten Zeit, um 20 Fragen zu beantworten. Es können mehrere Antworten korrekt sein. 
            Die Frage ist nur korrekt beantwortet, wenn alle richtigen Antworten angekreuzt wurden. 
            Es müssen mindestens 15 Fragen korrekt beantwortet werden, um den Test zu bestehen.
        </p>
        <p>
            Die bestandene theoretische Prüfung berechtigt für die folgenden sechs Monate zur Teilnahme an der praktischen Prüfung.
        </p>
    </div>
</div>

<div class="w3-section">
    <div class="w3-panel w3-light-grey">
        <p>
            <b>Praktische Prüfung: Pfeifen eines Ligaspiels </b>
        </p>
        <p>
            Mit bestandener theoretischer Prüfung kann der Prüfling an der praktischen Prüfung teilnehmen. 
            Dafür pfeift der Prüfling ein Spiel eines Ligaturniers unter Beobachtung des Prüfers. 
            Das Spiel sollte dem Spielniveau des Teams des Prüflings entsprechen. Die Entscheidung darüber obliegt dem Prüfer. 
            Im Anschluss erfolgt ein kurzes Gespräch zwischen Prüfer und Prüfling.
        </p>
        <p>
            Der Prüfer achtet neben dem Erkennen von Regelwidrigkeiten insbesondere auf das Auftreten des Prüflings 
            (Aufmerksamkeit, Kommunikation, Umgang, klare Linie, etc.). Zur Bewertung der praktischen Prüfung wird 
            ein Kriterienkatalog bereitgestellt.
        </p>
    </div>
</div>

<div class="w3-section">
    <h2 class="w3-text-secondary w3-xlarge">Anmeldung</h2>
    <p>
        Die Anmeldung zur Prüfung erfolgt per Mail an <?=Html::mailto(ENV::SCHIRIMAIL)?>. Die theoretische Prüfung kann innerhalb 
        eines Monats maximal drei Mal durchgeführt werden. Eine Anmeldung zur praktischen Prüfung ist nur mit bestandener 
        theoretischer Prüfung möglich.
    </p>
</div>

<div class="w3-section">
    <h2 class="w3-text-secondary w3-xlarge">Junior Schiedsrichter</h2>
    <p>
        Anwärter unter 16 Jahren können eine vereinfachte Prüfung ablegen. Sie bekommen einfachere Fragen im theoretischen Teil. 
        Das Alter des Prüflings wird während der praktischen Prüfung berücksichtigt. 
    </p>
</div>

<h2 class="w3-text-secondary w3-xlarge">Gültigkeit</h2>
<div class="w3-section">
    <p>
        Durch die bestandene theoretische und praktische Prüfung erhält der Spieler den Schiedsrichterstatus 
        für die aktuelle und folgende Saison. 
    </p>
</div>

<div class="w3-section">
    <h2 class="w3-text-secondary w3-xlarge">Vorbereitung auf die theoretische Prüfung</h2>
    <ul class="w3-ul">
        <li>Einzelne Fragen zur Vorbereitung auf die Schiedsrichterprüfung finden sich hier:<br><?=Html::link("uebungstest_l.php", "Einzelne Fragen der Basis-Prüfung", false, false)?></li>
        <li>Eine Simulation des kompletten Tests (<?=array_sum(SchiriTest::anzahl_J)?> Fragen) als Vorbereitung auf die <b>Junior-Schiriprüfung</b> findet sich hier:<br><?=Html::link("uebungstest_j.php", "Übungstest für die Junior-Prüfung", false, false)?></li>
        <li>Eine Simulation des kompletten Tests (<?=array_sum(SchiriTest::anzahl_B)?> Fragen) als Vorbereitung auf die <b>Basis-Schiriprüfung</b> findet sich hier:<br><?=Html::link("uebungstest_b.php", "Übungstest für die Basis-Prüfung", false, false)?></li>
    </ul>
</div>

<div class="w3-section">
    <h2 class="w3-text-secondary w3-xlarge">Weitere Infos</h2>
    <ul class="w3-ul">
        <li>Hier gibt es die kompletten Regeln der Deutschen Einradhockeyliga als pdf:<br><?=Html::link(Nav::LINK_REGELN, 'Regelwerk', false, false)?></li>
        <li>Schiri-Leitlinie für Spiele auf Fortgeschrittenem Niveau:<br><?=Html::link(Nav::LINK_SCHIRI_LEITLINIE, 'Schiri-Leitlinie', false, false)?></li>
        <li>Checkliste für die praktische Prüfung:<br><?=Html::link(Nav::LINK_SCHIRI_CHECKLIST, 'Checkliste', false, false)?></li>
    </ul>
</div>

<div class="w3-section">
    <div class="w3-panel w3-primary w3-text-white">
        <h2 class="w3-xlarge">Kontakt</h2>
        <p>Wer Einradhockey-Schiedsrichter werden möchte oder Fragen hat, kann sich jederzeit an <a href="mailto:<?=Env::SCHIRIMAIL?>" class="no w3-text-tertiary w3-hover-text-secondary"> <?=Env::SCHIRIMAIL?></a> wenden.</p>
    </div>
</div>

<?php include '../../templates/footer.tmp.php';
