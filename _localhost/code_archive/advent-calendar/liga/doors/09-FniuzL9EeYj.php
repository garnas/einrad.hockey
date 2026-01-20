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


<?php if ($now >= new DateTime('2025-12-09 10:00')): ?>
    <div class="w3-display-container w3-round-xlarge w3-padding-16" style="margin-top: 16px;">

        <div class="w3-section" style="line-height: 1.2;">
            <h1 class="advent-text-secondary">
                Wie läuft Einradhockey in der Schweiz?
            </h1>
        </div>

        <div class="w3-section" style="">
            <p style="font-style: italic; background-color: #edddd1; padding: 8px 16px;">
                Wir haben aus ganz unterschiedlichen Ländern nachgefragt: Wie läuft Einradhockey bei euch? 
                Alle Antworten auf unsere Fragen findet ihr hier! Dieses mal aus der Schweiz.
            </p>
        </div>

        <div class="w3-section">
            <img src="../../../bilder/advent/FniuzL9EeYj/door.jpg" class="w3-image w3-round w3-right" style="width:25%; margin-left:16px;">
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>Einen regelmäßigen Ligabetrieb gibt es in der Schweiz seit 2003.</p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie hat sich die Einradhockey-Community bei euch entwickelt?</b></p>
            <p>
                Vor Corona gab es ligaübergreifend seit einigen Jahren immer 20 oder 21 Teams in der Schweiz. 
                Corona hat dann aber für einen rechten Einschnitt gesorgt, so dass wir über mehrere Saisons weniger Teams 
                hatten. Zuerst hatten wir sehr wenig Nachwuchs, dann fehlte es vor allem in der A-Liga an Mannschaften. Auf die 
                aktuelle Saison hin haben wir die A-Liga mit den besten B-Teams aufgestockt und auch in den unteren Ligen hat 
                es viele Teams. Diese Saison haben wir erstmals eine Mannschaft aus Frankreich bei uns dabei, 
                ein Schweizer Verein hat das erste Mal seit vielen Jahren wieder ein Team stellen können. Mit 23 Teams 
                ist die Schweizer Einradhockeyliga so groß wie noch nie
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                23 Mannschaften:<br>
                A-Liga: 9 Teams<br>
                B-Liga: 6 Teams<br>
                C-Liga: 8 Teams
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Bei uns gibt es einen Ligabetrieb. Gespielt wird in drei Ligen, der Liga A, B und C. Vor Beginn der Saison meldet 
                sich jedes Team in einer Liga an, und in dieser Liga spielt man dann die ganze Saison. Jedes Team der Liga organisiert 
                ein Heimturnier und am Ende gibt es ein zusätzliches Finalturnier. Es kommen immer alle Mannschaften der Liga an das Turnier.
            </p>
            <p>
                Je nach Anzahl der Mannschaften wird ein anderer Turniermodus gespielt, aber grundsätzlich spielen immer alle einmal 
                gegen alle, dann gibt es eine Finalrunde, in der 1vs2, 3vs4 usw. spielt. Je nach Turnierrang gibt es dann eine bestimmte 
                Anzahl Punkte, die wird von jedem Turnier zusammengezählt, was am Schluss die Gesamtrangliste ergibt.
            </p>
            <p>
                In der B- und C-Liga kann man sich als Team einfach anmelden. Für die Liga A ist man nur spielberechtigt, 
                wenn man das Auf- und Abstiegsspiel zwischen dem ersten der Liga B und dem letzten der Liga A am 
                Ende jeder Saison gewinnt.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis?</b>
            </p>
            <p>
                Jedes Team organisiert ein Heimturnier, das heisst, nicht jede Liga hat gleich viele Turniere. Die Saison 
                dauert immer von Anfang November bis Ende April. In dieser Zeit finden pro Liga zwischen 5-10 Turniere statt.
            </p>
            <p>
                Jedes der Turniere wird in der Schweizermeisterschaft gewertet. Wer am Ende der Saison Tabellenerster 
                ist, darf den Schweizermeistertitel Einradhockey der jeweiligen Liga für sich beanspruchen.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Was sind die Höhepunkte im Laufe einer Saison bei euch?</b><br>
                (z. B. Turniere, Meisterschaften, Treffen, Conventions, …)
            </p>
            <p>
                Jedes Turnier ist ein Highlight 😊. Das Finalturnier als letztes Turnier der Saison ist aber schon 
                immer etwas Besonderes. Es wird jedes Jahr von einem anderen Verein organisiert. Es dauert immer 2 Tage 
                und alle Ligen sind gleichzeitig anwesend und spielen abwechslungsweise. Normalerweise gibt es auch noch 
                andere Aktivitäten oder Showeinlagen. Das Wochenende wird immer mit der Vergabe der Schweizermeistertitel 
                und der Übergabe der Pokale beendet.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Lilo Eltz erhalten. Sie ist <a href="https://www.swiss-iuc.ch/Organisation/Einrad/Einradhockey-Liga" class="no w3-text-primary w3-hover-text-secondary">Ressortleiterin Einradhockey</a> von <a href="https://www.swiss-iuc.ch/Home" class="no w3-text-primary w3-hover-text-secondary">Swiss Indoor- & Unicycling.</a></i>
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