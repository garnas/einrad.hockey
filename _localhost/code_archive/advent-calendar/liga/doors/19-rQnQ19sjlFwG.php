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


<?php if ($now >= new DateTime('2025-12-19 10:00')): ?>
    
    <div class="w3-display-container w3-round-xlarge w3-padding-16" style="margin-top: 16px;">

        <div class="w3-section" style="line-height: 1.2;">
            <h1 class="advent-text-secondary">
                Wie läuft Einradhockey in ...
            </h1>
        </div>

        <div class="w3-section">
            <p style="font-style: italic; background-color: #edddd1; padding: 8px 16px;">
                Wir haben aus ganz unterschiedlichen Ländern nachgefragt: Wie läuft Einradhockey bei euch? 
                Alle Antworten auf unsere Fragen findet ihr hier! Dieses mal aus Australien, Tschechien und Frankreich. Die Fragen sind 
                auf Englisch gestellt und genauso beantwortet. Hier ist alles von uns übersetzt.
            </p>
        </div>

        <div class="w3-section">
            <img src="../../../bilder/advent/rQnQ19sjlFwG/door.jpg" class="w3-image w3-round w3-right" style="width:25%; margin-left:16px;">
        </div>

        <!-- Australien -->
        <div class="w3-section" style="line-height: 1.2;">
            <h3 class="advent-text-secondary">
                ... Australien?
            </h3>
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>Erstmals gespielt um 1994.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie hat sich die Einradhockey-Community bei euch entwickelt?</b></p>
            <p>
                Sydney hat die größte Community, aber auch in Melbourne und Canberra gibt es eine 
                Gemeinschaft von Hockeyspielern, die sich regelmäßig trifft. Das bedeutet zwar, 
                dass wir in Australien mehrere Communities haben, aber Melbourne ist 877 km von Sydney 
                entfernt, sodass man nicht einfach am Wochenende zu einem Spiel nach Melbourne fahren kann.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                Es gibt etwa vier Teams, die regelmäßig zusammen spielen, und zwei oder mehr weitere Mix-Teams, die bei Turnieren aus 
                zusätzlichen Spielern gebildet werden können, die weniger regelmäßig spielen und daher nicht in Teams sind.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Es gibt eine Liga, die 2014 gegründet wurde. Anfangs gab es etwa 9 Teams zwischen Sydney und Canberra, aber der Wettbewerb 
                war nicht für alle attraktiv, und mit Covid sind die Zahlen zurückgegangen. Es gibt immer noch einen Wettbewerb, aber 
                statt 6 Turnieren pro Jahr veranstalten wir jetzt drei, eines in jeder der Städte: Canberra, Sydney, Melbourne.
            </p>
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis?</b>
            </p>
            <p>
                Alle zwei Monate veranstalten wir ein Turnier, jeweils eines in jeder der Städte Canberra, Sydney und 
                Melbourne. Für jedes Turnier mieten wir die Halle für Samstag/Sonntag. Am Samstag findet das Turnier statt, mit 
                einem Abendessen am Samstagabend an einem Ort in der Nähe, wo sich die Teilnehmer austauschen können. Am Sonntag 
                spielen wir Sticks-in Spiele, bei denen die Teilnehmer mit neuen Spielern spielen können. [Anmerkung: Sticks-in 
                ist ein Zufallsprinzip. Alle werfen ihre Schläger in die Mitte und daraus werden die Teams gelost.]
            </p>
            <p>
                Das dritte Turnier ist das Finalturnier, bei dem die Meisterschaft vergeben wird.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Was sind die Höhepunkte im Laufe einer Saison bei euch?</b><br>(z. B. Turniere, Meisterschaften, Treffen, Conventions, …)</p>
            <p>
                Die gemeinsamen Abendessen nach dem Turniertag sind immer sehr gemütlich, und der zweite Tag, an dem die 
                Teilnehmer mit anderen Spielern zusammenspielen können, kommt in der Regel sehr gut an. Schwächere Spieler 
                können mit stärkeren Spielern zusammenspielen und so das Zusammenspiel mit anderen Spielern üben.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</b>
            </p>
            <p>
                Die Möglichkeit für australische Spieler, von internationalen Spielern zu lernen, ist von unschätzbarem Wert. 
                Ich nehme regelmäßig deutsche Hockeyspieler bei mir auf, wenn sie nach Sydney kommen, mit der einfachen Regel, 
                dass sie mit uns Hockey spielen müssen.
            </p>
            <p>
                Wir hatten bereits einen stetigen Strom von Deutschen zu Gast, aber es wäre schön, wenn auch einige 
                schweizer Spieler den Weg nach Australien finden würden!
            </p>
            <p>
                Seit 2014 bin ich fast jedes Jahr international unterwegs und habe auf jeder Reise mein Einrad mitgenommen. Ich besuche 
                gerne Teams oder Vereine in anderen Ländern und treffe mich mit anderen Einradfahrern, die sich für Hockey interessieren.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</b>
            </p>
            <p>
                Unsere Community ist klein, sodass man alle gut kennt. Fans sind in der Regel nur Familienmitglieder. Es wäre schön, wenn 
                sie größer wäre wie die deutsche und die schweizer Liga.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei unseren Spielerinnen und Spielern und Teams?</b>
            </p>
            <p>
                Was die bloßen Zahlen angeht, hat die deutsche Liga wahrscheinlich die weltweit größte Konzentration an Spitzenspielern.
            </p>
            <p>
                Da wir so weit entfernt sind, können wir dieses Niveau (höchstens) einmal im Jahr erleben, und jedes Mal, wenn man es sieht, 
                merkt man, dass die Fahrer schneller Fortschritte gemacht haben als man selbst ... und dass man immer noch nicht gut genug 
                ist, um damit mithalten zu können!
            </p>
        </div>
        
        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Steven Hughes erhalten. Seit einigen Austragungen der Unicon ist er dort der Hockey Director - so auch in Österreich.</i>
            </p>
        </div>

        <hr>
        
        <!-- Tschechien -->
        <div class="w3-section" style="line-height: 1.2;">
            <h3 class="advent-text-secondary">
                ... Tschechien?
            </h3>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>Ich kann sagen, seit 2014 - fast 12 Jahre.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie hat sich die Einradhockey-Community bei euch entwickelt?</b></p>
            <p>
                2009 habe ich angefangen die Idee zu entwicklen, Floorball auf dem Einrad zu spielen. Das lag daran, dass meine damalige 
                Freundin Electric Wheelchair Hockey gespielt hat, was im Wesentlichen Floorball auf einem elektrischen Rollstuhl 
                ist. Die erste Idee hatte ich damals während ihres ersten Trainings. 2011 habe ich 
                dann <a href="https://www.youtube.com/watch?v=AJKA5PY8dj0" class="no w3-text-primary w3-hover-text-secondary">das erste Event</a> organisiert.
            </p>
            <p>
                Ungefähr 2012 wurde eine Vorführung von Einradhockey im tschechischen Fernsehen gezeigt. Ole Jaekel 
                [aus Dresden] hat mich daraufhin kontaktiert, dass ihm der Sport sehr bekannt vorkommen würde und in 
                Deutschland bereits gespielt wird.
            </p>
            <p>
                Seitem habe ich regelmäßge Trainings organisiert und seit 2014 nehmen wir an der Deutschen Einradhockeyliga teil. Da 
                haben wir auch unser <a href="https://www.youtube.com/watch?v=pVsNFfRDMBQ" class="no w3-text-primary w3-hover-text-secondary">erstes Turnier</a> 
                gespielt - immernoch mit Floorball-Schlägern. Und ich habe unser erstes (und 
                letztes) <a href="https://www.youtube.com/watch?v=XAu4OCNUS7E" class="no w3-text-primary w3-hover-text-secondary">internationales Einradhockey-Turnier in Prag</a> organisiert.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                Ich kenne nur zwei: Prague Unicycle Hockey Team und Uners Litoměřice.
            </p>
            <p>
                Manchmal organisiert TryOne, eine von Ade Gerža geleitete Einradschule, zum Spaß ein Einradhockey-Turnier. Aber die Schule ist nicht auf Einradhockey ausgerichtet.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Wir nehmen an der deutschen Liga teil, weil wir nicht viele Teams für unsere eigene Liga haben. Ich habe eine 
                Idee im Kopf mit kleinen Turnhallen und 3 x 3 Spielern … aber das ist derzeit nur eine Idee.
            </p>
            <p>
                Am Anfang hatten wir hauptsächlich Spieler aus Trial oder Leute, die nur Einrad fahren konnten. Jetzt haben wir 
                mehr Zirkusleute. Vielleicht weil ich seit kurzem im Zirkus unterrichte. :D
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Habt ihr besondere Rituale rund um eure Spiele?</b><br>(z. B. Torhymnen, Traditionen, …)</p>
            <p>
                Ich glaube, wir haben keine Traditionen oder Rituale, die uns betreffen. Ich werde versuchen, mir etwas auszudenken. :D
            </p>
        </div>

        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Matěj Koudelka erhalten. Er ist Ligavertreter des Prague Unicycle Hockey Team. Auf Instagram findet ihr sie unter <a href="https://www.instagram.com/unicycle_hockey.cz/" class="no w3-text-primary w3-hover-text-secondary">@unicycle_hockey.cz</a>.</i>
            </p>
        </div>

        <hr>
        
        <!-- Frankfreich -->
        <div class="w3-section" style="line-height: 1.2;">
            <h3 class="advent-text-secondary">
                ... Frankreich?
            </h3>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>
                Einradhockey wird in Frankreich seit mindestens 2005 gespielt. An der Unicon 2006 
                nahmen mehrere Teams teil. Die französische Einradmeisterschaft (<q>Coupe de France de Monocycle<q>; CFM) 2010 
                war das erste Turnier. Die CFM ist jedes Jahr Ende Oktober.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie hat sich die Einradhockey-Community bei euch entwickelt?</b></p>
            <p>
                Ziemlich ungleichmäßig. CFM 2013 (13 Teams), CFM 2015 (5 Teams), CFM 2019 (20 Teams). Es gibt 
                Phasen mit großem Interesse, auf die ein Rückgang folgt. Eine Entwicklung scheitert unter 
                anderem an den Distanzen zwischen den Vereinen.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                Es gibt hauptsächlich 5 Vereine, die regelmäßig Trainings anbieten:
                <ul>
                    <li>Mon'Ogre (Grenoble)</li>
                    <li>Cycl'Hop (Nizza)</li>
                    <li>Anim'aFond (Orléans)</li>
                    <li>Cycl'One (Cluses)</li>
                    <li>Troub (Brumath)</li>
                </ul>
                Nur einer der Vereine verfügt über ein richtiges Indoor-Hockeyfeld für Rollerhockey.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>Wir haben keine Liga.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</b></p>
            <p>
                Es hängt davon ab, ob jeder Verein Einradhockey als eine seiner vorrangigen 
                Sportarten auswählt. Wir haben nicht viele Hockeyplätze. Wir 
                müssten uns an Rollhockeyvereine wenden. Außerdem braucht es mehr Vereine 
                die bereit wären, Einradhockey-Turniere auszurichten.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis?</b>
            </p>
            <p>
                Derzeit wird jedes Jahr im April nur ein Turnier in Orléans organisiert. Außerdem 
                findet ein Turnier während des französischen Pokals statt.
            </p>
        </div>
    
        <div class="w3-section">
            <p class="advent-text-secondary"><b>Was sind die Höhepunkte im Laufe einer Saison bei euch?</b><br>(z. B. Turniere, Meisterschaften, Treffen, Conventions, …)</p>
            <p>Die beiden zuvor erwähnten Turniere: Orléans und CFM.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</b>
            </p>
            <p>
                Wir sind offen gegenüber jeder Einladung. Wir wissen, dass die Schweiz, 
                Deutschland, Großbritannien und auch Österreich auf einem höheren Niveau sind 
                als wir. Oder vielleicht auch Belgien, da sie am mit am nächsten für uns sind.
            </p>    
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Habt ihr besondere Rituale rund um eure Spiele?</b><br>(z. B. Torhymnen, Traditionen, …)</p>
            <p>
                Das ist nicht ausschließlich für Einradhockey: Wenn eine Mannschaft ein Teil eines großen 
                Vereins ist, gibt es viel Unterstützung. Die meisten Mannschaften haben 
                ihren eigenen Schlachtruf, um sich selbst zu motivieren.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</b>
            </p>
            <p>
                Das Zugehörigkeitsgefühl zu einem Verein ist derzeit sehr stark. Bei uns ist der Sport 
                noch nicht weit genug entwickelt, um eine Nationalmannschaft in Betracht zu ziehen.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei unseren Spielerinnen und Spielern und Teams?</b>
            </p>
            <p>
                Wir wissen, dass die deutsche Liga eine sehr umkämpfte Meisterschaft ist, die dem Niveau der Schweizer Liga sehr nahe kommt.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Yann Henry und Sebastien Golliet erhalten. Hier haben wir sie zu einer Antwort zusammengefasst.</i>
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