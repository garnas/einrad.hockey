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


<?php if ($now >= new DateTime('2025-12-16 10:00')): ?>
    
    <div class="w3-display-container w3-round-xlarge w3-padding-16" style="margin-top: 16px;">

        <div class="w3-section" style="line-height: 1.2;">
            <h1 class="advent-text-secondary">
                Wie läuft Einradhockey in ...
            </h1>
        </div>

        <div class="w3-section">
            <p style="font-style: italic; background-color: #edddd1; padding: 8px 16px;">
                Wir haben aus ganz unterschiedlichen Ländern nachgefragt: Wie läuft Einradhockey bei euch? 
                Alle Antworten auf unsere Fragen findet ihr hier! Dieses mal aus Dänemark und Österreich. Die Fragen sind für Dänemark
                auf Englisch gestellt und genauso beantwortet. Hier ist alles von uns übersetzt.
            </p>
        </div>

        <div class="w3-section" style="line-height: 1.2;">
            <h3 class="advent-text-secondary">
                ... Dänemark?
            </h3>
        </div>

        <div class="w3-section">
            <img src="../../../bilder/advent/GttRhE16GfRg/door.jpg" class="w3-image w3-round w3-right" style="width:25%; margin-left:16px;">
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>Einradhockey gibt es hier seit mindestens 20 Jahren.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie hat sich die Einradhockey-Community bei euch entwickelt?</b></p>
            <p>In meiner Region gibt es weniger Hockey und mehr Freestyle oder Flatland.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                Wenn die dänische Meisterschaft stattfindet, dann treten 6 bis 7 Teams an und 
                es ist eher zufällig, wer spielt und alle können mitmachen. Es ist also keine so 
                große Disziplin in Dänemark. :)
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Wir haben keine Liga und nicht genug Leute, um eine Liga zu gründen. [Anmerkung: Floorball ist in Dänemark sehr verbreitet, 
                daher haben die meisten Fahrer schon einmal gespielt. Das macht es ihnen leicht, bei Meisterschaften mit 
                Floorballschlägern Einradhockey zu spielen.]
            </p>
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis??</b>
            </p>
            <p>
                Wir haben einmal im Jahr ein Turnier bei der dänischen Meisterschaft. Diese ist auch der Höhepunkt.
            </p>
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Habt ihr besondere Rituale rund um eure Spiele?</b><br>
                (z. B. Torhymnen, Traditionen, …)
            </p>
            <p>
                Wir haben einen talentierten DJ, der mitreißende Songs und Torlieder spielt.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</b>
            </p>
            <p>
                Familie und andere Einradfahrer schauen sich die Spiele an. 
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei 
                unseren Spielerinnen und Spielern und Teams?</b>
            </p>
            <p>
                Wir kennen uns mit den deutschen Ligen nicht aus, daher denke ich nicht viel darüber nach :)
            </p>
        </div>

        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Sofus Agerskov erhalten. Auf Instagram findet ihr ihn unter <a href="https://www.instagram.com/sofusagerskov_uni/" class="no w3-text-primary w3-hover-text-secondary">@sofusagerskov_uni</a>.</i>
            </p>
        </div>

        <hr>

        <div class="w3-section" style="line-height: 1.2;">
            <h3 class="advent-text-secondary">
                ... Österreich?
            </h3>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>
                2011 wurde das erste österreichweite Einradhockeyturnier ausgerichtet und leitete 
                damit auch den Beginn des Einradhockeysports in Österreich ein. Vermutlich gibt es den 
                Einradhockeysport schon seit mindestens 2007, aber die historischen Aufzeichnungen sind uneindeutig.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie hat sich die Einradhockey-Community bei euch entwickelt?</b></p>
            <p>
                Nach dem ersten Hockeyturnier 2011 at sich ein Boom entwickelt, in den folgenden Jahren entwickelte sich sowohl 
                im Burgenland mit den One-Wheel-Dragons als auch in Marchtrenk mit den Flying Unis parallel zu Steyr zwei 
                weitere Einradhockey-Hotspots. 2014 wurde das Steyrer Einradhockeyturnier zum ersten Mal in der großen Dreifachhalle 
                ausgerichtet, mit internationaler Beteiligung. Auch die One-Wheel-Dragnos richteten Einradhockeyturniere 
                aus. Letztendlich hat sich Steyr als Einradhockey-Mekka in Österreich durchgesetzt.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                Aktuell sind die Teams des Steyrer Vereins die einzig verbliebenen Einradhockeyteams in der 
                allgemeinen Klasse. In Wien reifen jedoch Nachwuchsteams heran.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b>
                <br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Nein, dafür gibt es in Österreich zu wenig Mannschaften. Früher gab es intensive Derbys zwischen 
                den One-Wheel-Dragons und Steyr bei aufgeheizter Stimmung. Der Sieger war Österreichs 
                beste Einradhockeymannschaft.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</b></p>
            <p>
                Ideen gibt es viele, aber Einradhockey ist zu wenig verbreitet in Österreich.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft oder ein ähnliches Großereignis?</b></p>
            <p>
                Das größte Turnier ist das Int. Steyrer Einradhockeyturnier im Frühjahr mit, in der Vergangenheit, Beteiligungen 
                aus Deutschland, Tschechien, Schweiz, Italien und Österreich.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Was sind die Höhepunkte im Laufe einer Saison bei euch?</b>
                <br>(z. B. Turniere, Meisterschaften, Treffen, Conventions, …)</p>
            <p>
                Der Höhepunkt ist das Int. Steyrer Einradhockeyturnier als unser Saisonabschluss. 🙂
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</b></p>
            <p>
                Natürlich sind hier unsere deutschen Nachbarn zu erwähnen, die aus dem Turnier kaum noch wegzudenken sind. Auch sonst 
                hatten wir immer Spaß mit internationaler Beteiligung. Geografische Entfernungen außen vor gelassen, wäre eine Beteiligung aus Dänemark sicher einmal ein netter Impuls 🙂
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Habt ihr besondere Rituale rund um eure Spiele?</b>
                <br>(z. B. Torhymnen, Traditionen, …)</p>
            <p>
                Wir haben eines der besten selbst errichteten Buffets, Torhymnen, Stadionsprecher 
                und je nach Verfügbarkeit und Zeitplan auch Shows in den Pausen. 🙂
            </p>
        </div>        

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</b></p>
            <p>
                Früher gab es intensive Derbys zwischen den One-Wheel-Dragons und Steyr. Mittlerweile konzentriert 
                sich die Fankultur rund um den Steyrer Verein.
            </p>
        </div>
    
        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei unseren Spielerinnen und Spielern und Teams?</b></p>
            <p>
                Teams aus der Deutschen Einradhockeyliga nehmen regelmäßig an unsere Einradhockeyturnier teil, was uns immer viel Spaß macht. Des Weiteren 
                unterstützen vereinzelt Spielerinnen Teams in der Deutschen Liga. Die technische Fertigkeit der deutschen Spielerinnen und Spieler 
                durch gutes Training ist auf jeden Fall erwähnenswert, aber auch die Fairness und das Beachten von Spielregeln.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Stereotype über das österreichische Hockeygeschehen</b></p>
            <p>
                In Österreich ist ein SUP kein Foul sondern ein Stilmittel dessen Qualität an der Flugkurve des Gegners gemessen wird ;)
                [Anmerkung: Diese Frage haben wir zwar nicht gestellt, mussten aber dennoch schmunzeln.]
            </p>
        </div>

        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Theo Crazzolara erhalten. Er ist unter anderem Head of Social Media für die <a href="https://www.unicon22.at/" class="no w3-text-primary w3-hover-text-secondary">Unicon 22</a> in Österreich.</i>
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