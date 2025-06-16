<?php

require_once '../../init.php';

Html::$titel = "Tippspiel | Deutsche Einradhockeyliga";
Html::$content = "Tippspiel für die Meisterschaften der Deutschen Einradhockeyliga";

include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Tippspiel für die Meisterschaften der Saison 2024/25</h1>

<p class="w3-leftbar w3-border-secondary w3-padding">
   Die Ergebnisse für das Tippspiel der B-Meisterschaft liegen vor. Ihr könnt sie hier einsehen:<br><a class="no w3-text-primary w3-hover-text-secondary" href="#bergebnisse">Tippspiel B-Meisterschaft 2024/25</a>
</p>

<p>
    In dieser Saison möchten wir ein Tippspiel für die Meisterschaften anbieten. Hier bekommst du alle Informationen für die Teilnahme.
</p>

<h3 id="teilnahme" class="w3-text-secondary">Teilnahme am Tippspiel</span></h3>
<p>
    Im Abschnitt <a class="no w3-text-primary w3-hover-text-secondary" href="#downloads">Downloads</a> findest Du eine Excel-Datei, mit der Du am Tippspiel für die jeweilige 
    Meisterschaft teilnehmen kannst. Bitte lade die Datei herunter, fülle sie aus und sende sie an <?=Html::mailto('oeffentlichkeitsausschuss@einrad.hockey')?>. Bitte teile uns dort 
    auch deinen Namen und ggfs. dein Team mit. Die im Rahmen des Tippspiels übermittelten personenbezogenen Daten (z. B. Name, Teamzugehörigkeit) werden 
    ausschließlich zur Durchführung und Auswertung des Tippspiels verwendet.
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
    <p><a class="no w3-hover-text-secondary" target="_blank" href="<?= Nav::LINK_TIPPSPIEL_ANLEITUNG_DM ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Anleitung zum Tippspiel der Deutschen Meisterschaft (PDF)</a></p>
    <p><a class="no w3-hover-text-secondary" target="_blank" href="<?= Nav::LINK_TIPPSPIEL_DM_XLSX ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Tippspiel Deutsche Meisterschaft 2025 (Excel)</a></p>
    <p><a class="no w3-hover-text-secondary" target="_blank" href="<?= Nav::LINK_TIPPSPIEL_DM_PDF ?>"><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Tippspiel Deutsche Meisterschaft 2025 (PDF)</a></p>
</div>

<hr>

<h1 id="bergebnisse" class="w3-text-primary">Ergebnisse B-Meisterschaft 2024/25</h1>

<table class="w3-border w3-border-light-grey w3-table w3-striped">
    <tr class="w3-primary">
        <th>Platz</th>
        <th>Name</th>
        <th>Team</th>
        <th>Punkte</th>
        <th>Details</th>
    </tr>
    <tr><td>1</td><td>Jakob</td><td>MUH-Bande Jena</td><td>59</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_MUH_Jakob.pdf'>PDF</a></td></tr>
    <tr><td>2</td><td>Nele</td><td>BTC Heisse Reifen</td><td>56</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_HSR_Nele.pdf'>PDF</a></td></tr>
    <tr><td>3</td><td>Jessica</td><td>SKV Mörfelden Phönix</td><td>51</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_PHX_Jessi.pdf'>PDF</a></td></tr>
    <tr><td>4</td><td>Malte</td><td>TV Lilienthal Moorteufel</td><td>46</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_MRT_Malte.pdf'>PDF</a></td></tr>
    <tr><td>5</td><td>Matteo</td><td>Dresdner Einradtiger</td><td>40</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_EIT_Matteo.pdf'>PDF</a></td></tr>
    <tr><td>5</td><td>Philipp</td><td>SKV Mörfelden Titans</td><td>40</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_TNS_PeKA.pdf'>PDF</a></td></tr>
    <tr><td>7</td><td>Clemens</td><td>Dresdner Einradtiger</td><td>39</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_EIT_Clemens.pdf'>PDF</a></td></tr>
    <tr><td>8</td><td>Carla</td><td>Hockey Hawks</td><td>38</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_HAW_Carla.pdf'>PDF</a></td></tr>
    <tr><td>9</td><td>Lea</td><td>MUH-Bande Jena</td><td>37</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_MUH_Lea.pdf'>PDF</a></td></tr>
    <tr><td>10</td><td>Larissa</td><td>MUH-Bande Jena</td><td>36</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_MUH_Larissa.pdf'>PDF</a></td></tr>
    <tr><td>11</td><td>Lukas</td><td>Hockey Hawks</td><td>35</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_HAW_Lukas.pdf'>PDF</a></td></tr>
    <tr><td>12</td><td>Simon</td><td>Querrad</td><td>32</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_QRD_Simon.pdf'>PDF</a></td></tr>
    <tr><td>13</td><td>Linus</td><td>Einradhockey Elmshorn</td><td>31</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_ELM_Linus.pdf'>PDF</a></td></tr>
    <tr><td>14</td><td>Ole</td><td>Dresdner Einradtiger</td><td>30</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_EIT_Ole.pdf'>PDF</a></td></tr>
    <tr><td>15</td><td>Lukas</td><td>MUH-Bande Jena</td><td>28</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_MUH_Lukas.pdf'>PDF</a></td></tr>
    <tr><td>16</td><td>Dustin</td><td>SKV Mörfelden UniThunder</td><td>24</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_UTH_Dustin.pdf'>PDF</a></td></tr>
    <tr><td>17</td><td>Fin</td><td>MUH-Bande Jena</td><td>23</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_MUH_Fin.pdf'>PDF</a></td></tr>
    <tr><td>18</td><td>Carina</td><td>SKV Mörfelden UniThunder</td><td>18</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_UTH_Carina.pdf'>PDF</a></td></tr>
    <tr><td>19</td><td>Kristin</td><td>MUH-Bande Jena</td><td>13</td><td><a class='no w3-text-primary w3-hover-text-secondary' href='/dokumente/tippspiel/bm_ergebnisse/BM_MUH_Kristin.pdf'>PDF</a></td></tr>
</table>

<?php include '../../templates/footer.tmp.php';
