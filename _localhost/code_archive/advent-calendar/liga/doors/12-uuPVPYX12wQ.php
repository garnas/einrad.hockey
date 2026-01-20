<?php

// Logik
require_once '../../../../init.php';
$now = new DateTime();

$directory = '../../../bilder/advent/FniuzL9EeYj/';
$files = scandir($directory);

// Layout
Html::$titel = "Adventskalender | Deutsche Einradhockeyliga";
Html::$content = "Adventskalender der Deutschen Einradhockeyliga für das Jahr 2025.";

include '../../../../templates/header.tmp.php'; ?>

<link type="text/css" rel="stylesheet" href="../style.css">
<link type="text/css" rel="stylesheet" href="../colors.css">


<?php if ($now >= new DateTime('2025-12-12 10:00')): ?>
    
    <div class="w3-display-container w3-round-xlarge w3-padding-16" style="margin-top: 16px;">

        <div class="w3-section" style="line-height: 1.2;">
            <h1 class="advent-text-secondary">
                Wie läuft Einradhockey in ...
            </h1>
        </div>

        <div class="w3-section">
            <p style="font-style: italic; background-color: #edddd1; padding: 8px 16px;">
                Wir haben aus ganz unterschiedlichen Ländern nachgefragt: Wie läuft Einradhockey bei euch? 
                Alle Antworten auf unsere Fragen findet ihr hier! Dieses mal aus den USA und Hong Kong. Die Fragen sind 
                auf Englisch gestellt und genauso beantwortet. Hier ist alles von uns übersetzt.
            </p>
        </div>

        <div class="w3-section" style="line-height: 1.2;">
            <h3 class="advent-text-secondary">
                ... den USA?
            </h3>
        </div>

        <div class="w3-section">
            <img src="../../../bilder/advent/uuPVPYX12wQ/door.jpg" class="w3-image w3-round w3-right" style="width:25%; margin-left:16px;">
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>Seit Mitte der 1990er.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie hat sich die Einradhockey-Community bei euch entwickelt?</b></p>
            <p>
                Es sind hauptsächlich aktuelle oder ehemalige Freestyle-Fahrer vom TCUC [Twin Cities Unicycle Club], die zum Spaß spielen. Wir trainieren gelegentlich und nehmen oft an nationalen Turnieren oder Unicon teil, aber meistens als Pickup-Teams.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                Eins.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Es gibt keinen Ligabetrieb.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</b></p>
            <p>
                Ich versuche, mehr Spieler zu gewinnen und ein größeres Team aufzubauen, aber unsere größte 
                Herausforderung besteht darin, genügend Spieler für Trainingseinheiten und Spiele zusammenzubekommen. 
                Außerdem gibt es in der Nähe keine anderen Teams, gegen die wir spielen könnten. Meine beiden Söhne 
                spielen Eishockey und werden wahrscheinlich eines Tages sehr gute Spieler sein, aber das wird noch einige 
                Jahre dauern. Ich hoffe, dass ich in Zukunft einige neue Spieler aus dem Freundeskreis meiner 
                Söhne gewinnen kann!
            </p>
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis??</b>
            </p>
            <p>
                Wir veranstalten in der Regel mehrmals im Jahr ein Clubfest, bei dem wir verschiedene 
                Einrad-Aktivitäten anbieten, darunter ein Spaß-Hockey-Turnier. Außerdem findet jedes Jahr 
                im Rahmen der Convention ein Turnier statt, bei dem die Gewinner mit Medaillen ausgezeichnet werden.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Was sind die Höhepunkte im Laufe einer Saison bei euch?</b><br>
                (z. B. Turniere, Meisterschaften, Treffen, Conventions, …)
            </p>
            <p>
                Die nationale Convention und die Unicon, wenn wir es schaffen.
            </p>
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</b>
            </p>
            <p>
                Alle, die zu Besuch kommen möchten! Vor allem diejenigen, die über Teams 
                und Ligen verfügen, damit sie Ratschläge geben und zeigen können, wie sie trainieren.
            </p>
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Habt ihr besondere Rituale rund um eure Spiele?</b><br>
                (z. B. Torhymnen, Traditionen, …)
            </p>
            <p>
                Nicht wirklich.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</b>
            </p>
            <p>
                Nur andere Clubmitglieder die anfeuern.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei 
                unseren Spielerinnen und Spielern und Teams?</b>
            </p>
            <p>
                Nach dem, was wir bei der letzten Unicon gesehen haben, sind sie auf einem sehr hohen 
                Niveau und wir waren ihnen klar unterlegen.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Ryan Wood erhalten. Er ist Mitglied des <a href="https://www.tcuc.org/" class="no w3-text-primary w3-hover-text-secondary">Twin Cities Unicycle Club</a> aus Minnesota.</i>
            </p>
        </div>

        <hr>

        <div class="w3-section" style="line-height: 1.2;">
            <h3 class="advent-text-secondary">
                ... Hong Kong?
            </h3>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>Ungefähr 30 Jahre.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                Eins.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Wir hatten ein halbjährliches Turnier, die Asia Pacific Unicycle Championships, 
                zwischen Hongkong, Singapur, Australien, Südkorea und manchmal Taiwan und den Philippinen.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</b></p>
            <p>
                Im Moment nicht.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis??</b>
            </p>
            <p>
                Wir spielen jetzt wöchentlich im Verein.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Was sind die Höhepunkte im Laufe einer Saison bei euch?</b><br>
                (z. B. Turniere, Meisterschaften, Treffen, Conventions, …)
            </p>
            <p>
                Jede Woche spielen!
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</b>
            </p>
            <p>
                Alle üblichen Freunde aus dem asiatisch-pazifischen Raum.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Habt ihr besondere Rituale rund um eure Spiele?</b><br>
                (z. B. Torhymnen, Traditionen, …)
            </p>
            <p>
                Nein.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</b>
            </p>
            <p>
                Wir sind sehr offen für Einsteiger und neue Spieler. Wir haben das 
                Motto: "Es ist egal, wer du bist oder warum du hier bist, wir freuen uns, den 
                Spaß am Einradfahren (und vor allem am Einradhockey) mit dir zu teilen."
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei 
                unseren Spielerinnen und Spielern und Teams?</b>
            </p>
            <p>
                Großer Respekt - ihr seid die Besten!
            </p>
        </div>

        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Martin Turnier erhalten. Er administriert die <a href="https://www.facebook.com/groups/97367907315" class="no w3-text-primary w3-hover-text-secondary">Facebook Gruppe Unicycle Hong Kong UNIHK</a> für Einradhockey in Hong Kong.</i>
            </p>
        </div>

    </div>

<?php else: ?>
    
    <div class="w3-display-container advent-color w3-round-xlarge w3-padding-16" style="margin-top: 16px;">
        <div class="slide-container"><img class="slide" src="../../../bilder/advent/time1000.jpg" /></div>
    </div>

<?php endif; ?>

<script>
    
    function showDivs(n) {
        var i;
        var x = document.getElementsByClassName('slideshow');
        if (n > x.length) { slideIndex = 1 }
        if (n < 1) { slideIndex = x.length }
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";  
        }
        x[slideIndex - 1].style.display = "block";  
    }

    function plusDivs(n) {
        showDivs(slideIndex += n);
    }

    var slideIndex = 1;
    showDivs(slideIndex);
    
</script>

<?php include '../../../../templates/footer.tmp.php'; ?>