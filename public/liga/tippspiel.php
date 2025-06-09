<?php

require_once '../../init.php';

Html::$titel = "Tippspiel | Deutsche Einradhockeyliga";
Html::$content = "Tippspiel für die Meisterschaften der Deutschen Einradhockeyliga";

include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Tippspiel für die Meisterschaften der Saison 2024/25</h1>
<p>
    In dieser Saison möchten wir ein Tippspiel für die Meisterschaften anbieten. Hier bekommst du alle Informationen für die Teilnahme.
</p>

<h3 id="teilnahme" class="w3-text-secondary">Teilnahme am Tippspiel</span></h3>
<p>
    Im Abschnitt <a class="no w3-text-primary w3-hover-text-secondary" href="#downloads">Downloads</a> findest Du eine Excel-Datei, mit der Du am Tippspiel für die jeweilige Meisterschaft teilnehmen kannst. Bitte lade die Datei 
    herunter, fülle sie aus und sende sie an <?=Html::mailto('oeffentlichkeitsausschuss@einrad.hockey')?>. Bitte teile uns dort auch deinen 
    Namen und ggfs. dein Team mit. Solltest Du nicht wollten, dass dein Name am Ende evtl. veröffentlicht wird, dann teile uns dies bitte auch mit.
</p>
<p>
    Wenn Du nicht die Excel-Datei nutzen möchtest, so steht auch eine PDF-Datei zur Verfügung, die Du stattdessen an den 
    Öffentlichkeitsausschuss schicken kannst.
</p>
<p>
    Nach der jeweiligen Meisterschaft werden die eingeschickten Tipps schnellstmöglich ausgewertet.
</p>

<p class="w3-leftbar w3-border-secondary w3-padding">
    Der <b>Teilnahmeschluss</b> für die Tipps ist jeweils der Freitag vor dem Turnierbeginn um 23:59 Uhr. Für die <b>B-Meisterschaft</b> ist 
    dies der <b>13.06.2025</b> um 23:59 Uhr. Für die <b>Deutsche Meisterschaft</b> ist dies der <b>20.06.2025</b> um 23:59 Uhr.
</p>

<h3 id="punkte" class="w3-text-secondary">Punktevergabe</h3>
<ul>
    <li>Wird ein Ergebnis in der Gruppenphase korrekt getippt, gibt es dafür 5 Punkte. Ist die richtige Tendenz gegeben, gibt es 
        dafür 1 Punkt. Ein Penaltyschießen wird dabei nicht berücksichtigt.</li>
    <li>Basierend auf den Tipps werden die Halbfinalpaarungen oder eine Anschlussgruppe gesetzt. Ist ein Team damit in ein Halbfinale oder 
        eine Anschlussgruppe getippt, in dem es am Ende auch spielt, so gibt es 1 Punkt. Liegt die exakte Paarung oder Anschlussgruppe vor, so gibt es 
        einen Bonuspunkt, sodass dann 3 Punkte vergeben werden. Das gleiche gilt für die Finalspiele.</li>
    <li>Nach den Finalspielen werden die Abschlussplatzierungen betrachtet. Ist der dritte Platz korrekt, so gibt es 
        2 Punkte, ist der zweite Platz korrekt, so gibt es 3 Punkte. Ist der Meister korrekt, so gibt es 5 Punkte. 
        Sind die Top 3 vollständig korrekt getippt, so gibt es einen weiteren Bonuspunkt.</li>
    <li>Eine Übersicht der Punkte findest du in der Excel-Datei.</li>
</ul>

<h3 id="hinweise" class="w3-text-secondary">Hinweise</h3>
<ul>
    <li>Verwendet ihr die PDF-Datei müsst ihr die Tabellen jeweils selbst berechnen. Die Excel-Datei unterstützt euch, funktioniert aber nicht 
    vollständig automatisch. Alle Hinweise dazu findet ihr in der Datei selbst.</li>
    <li>Sollte es Probleme in der Darstellung der Excel-Datei geben oder andere Probleme, dann melde Dich gerne bei uns. Wir werden versuchen, 
        diese zu lösen. In jedem Fall möchten wir Dir ermöglichen, am Tippspiel teilzunehmen.</li>
</ul>

<h3 id="downloads" class="w3-text-secondary">Downloads</h3>
<div>
    <p><a class="no w3-hover-text-secondary" href="<?= Nav::LINK_TIPPSPIEL_ANLEITUNG_BM ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Anleitung zum Tippspiel der B-Meisterschaft (PDF)</a></p>
    <p><a class="no w3-hover-text-secondary" href="<?= Nav::LINK_TIPPSPIEL_BM_XLSX ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Tippspiel B-Meisterschaft 2025 (Excel)</a></p>
    <p><a class="no w3-hover-text-secondary" href="<?= Nav::LINK_TIPPSPIEL_BM_PDF ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Tippspiel B-Meisterschaft 2025 (PDF)</a></p>
    <hr>
    <p><a class="no w3-hover-text-secondary" href="<?= Nav::LINK_TIPPSPIEL_ANLEITUNG_DM ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Anleitung zum Tippspiel der Deutschen Meisterschaft (PDF)</a></p>
    <p><a class="no w3-hover-text-secondary" href="<?= Nav::LINK_TIPPSPIEL_DM_XLSX ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Tippspiel Deutsche Meisterschaft 2025 (Excel)</a></p>
    <p><a class="no w3-hover-text-secondary" href="<?= Nav::LINK_TIPPSPIEL_DM_PDF ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Tippspiel Deutsche Meisterschaft 2025 (PDF)</a></p>
</div>

<?php include '../../templates/footer.tmp.php';
