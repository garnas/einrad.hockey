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


<?php if ($now >= new DateTime('2025-12-23 10:00')): ?>
    
    <div class="w3-display-container w3-round-xlarge w3-padding-16" style="margin-top: 16px;">

        <div class="w3-section" style="line-height: 1.2;">
            <h1 class="advent-text-secondary">
                Wie läuft Einradhockey in Großbritannien?
            </h1>
        </div>

        <div class="w3-section">
            <p style="font-style: italic; background-color: #edddd1; padding: 8px 16px;">
                Wir haben aus ganz unterschiedlichen Ländern nachgefragt: Wie läuft Einradhockey bei euch? 
                Alle Antworten auf unsere Fragen findet ihr hier! Dieses mal aus Großbritannien. Die Fragen sind 
                auf Englisch gestellt und genauso beantwortet. Hier ist alles von uns übersetzt.
            </p>
        </div>

        <div class="w3-section">
            <img src="../../../bilder/advent/NGslUlNh23/door.jpg" class="w3-image w3-round w3-right" style="width:25%; margin-left:16px;">
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary"><b>Seit wann gibt es Einradhockey in eurem Land?</b></p>
            <p>
                Einradhockey wird in Großbritannien seit Anfang der 1990er Jahre gespielt. Ich habe 1993 
                angefangen und bis 1996 für das Team aus Hastings gespielt. Dann habe ich etwa 14 Jahre lang 
                pausiert, bis ich 2010 das Team in Cardiff gefunden habe. Seit etwa 16 Jahren spiele ich nun 
                für das Team aus Cardiff, nehme an Turnieren teil und richte sie aus.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie hat sich die Einradhockey-Community bei euch entwickelt?</b></p>
            <p>
                Die Zahl der Einradhockey-Clubs in Großbritannien ist seit den 90er Jahren leider zurückgegangen, 
                aber wir bemühen uns weiterhin, die Beteiligung und Sichtbarkeit dieses Sports zu 
                erhöhen, und das mit einigem Erfolg.
            </p>
            <p>
                Besonders freut mich, wie sich der Cardiff Unicycle Hockey Club im Laufe der Jahre entwickelt hat 
                und nach wie vor eine treibende Kraft für Einradhockey in Großbritannien ist. Wir treffen uns zweimal 
                pro Woche zum Training. Bei einer der Trainingseinheiten konzentrieren wir uns darauf, Anfängern das 
                Einradfahren und Einradhockey beizubringen. Die andere Trainingseinheit ist für 
                fortgeschrittenere Spieler gedacht und eher wettkampforientiert, bleibt aber dennoch inklusiv.
            </p>
            <p>
                Ich habe 2010 angefangen, im Verein zu spielen, leite den Verein aber seit etwa 12 Jahren. Wir haben 
                dreimal an den Europameisterschaften teilgenommen (2013, 2017, 2019) und bei der ECU 2017 
                den zweiten Platz in der B-Liga belegt.
            </p>
            <p>
                Wir freuen uns auf die Unicon 2026 in Steyr und hoffen, dass wir mit mindestens drei Teams antreten können.
            </p>
        </div>
        
        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</b></p>
            <p>
                Ich glaube, dass es derzeit vier aktive Einradhockey-Clubs in Großbritannien gibt:
                <ul>
                    <li>Cardiff Unicycle Hockey</li>
                    <li>London (The Lunis)</li>
                    <li>East Midlands Unicyclists (The EMUs)</li>
                    <li>Southampton</li>
                </ul>
            </p>
            <p>
                Von diesen hat Cardiff in der Regel die meisten Spieler, die regelmäßig teilnehmen, 
                sodass wir wahrscheinlich 3 oder 4 Teams mit jeweils 5 Spielern für eine 
                wichtige Events aufstellen könnten. Wir haben auch die meisten Anfänger.
            </p>
            <p>
                Früher gab es Vereine in:
                <ul>
                    <li>Bristol</li>
                    <li>Cambridge</li>
                    <li>...</li>
                </ul>
                aber diese treffen sich derzeit nicht mehr. Wir wissen von mehreren Spielern, 
                die nach wie vor an Turnieren teilnehmen, wenn es ihnen möglich ist, sowie von einigen 
                anderen Spielern, die nicht in der Nähe eines aktiven Vereins wohnen, um 
                an den wöchentlichen Trainingseinheiten teilzunehmen
            </p>
            <p>
                Es besteht eine große Wahrscheinlichkeit, dass im Südwesten (Cornwall, Devon, Dorset, Somerset, Bristol) 
                ein neuer Einradhockey-Club gegründet wird, da es dort genügend Spieler gibt, die aus Cardiff dorthin 
                gezogen sind, um dies zu ermöglichen. Wir drücken diesem Club die Daumen und halten den Kontakt zu 
                anderen Spielern im ganzen Land aufrecht, in der Hoffnung, dass weitere Clubs regelmäßig spielen können.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</b></p>
            <p>
                Leider gibt es derzeit keine Liga. Das letzte Mal, dass wir in Großbritannien einen Ligabetrieb hatten, 
                war 2012. Es gab und gibt auch weiterhin kein A-B-C-Ranking-System, da wir nicht genügend 
                Teams hatten, um dies praktikabel zu machen.
            </p>
            <p>
                Seit 2013 wird bei jedem Turnier, das wir in Großbritannien veranstalten, ein "Scratch-Team"-Format verwendet. 
                Im Wesentlichen melden sich die Spieler einzeln an, anstatt sich als Teams anzumelden.
            </p>
            <p>
                Am Tag der Veranstaltung wird das Können jedes Spielers auf einer Skala von 1 bis 5 eingestuft. Manchmal 
                erfolgt dies durch den Spieler selbst, manchmal durch den Veranstalter. Dann versuchen wir, 
                Teams mit ausgeglichenem Leistungsniveau zu bilden. Wir spielen eine Round-Robin-Runde, in der jedes Team gegen jedes andere Team antritt.
            </p>
            <p>
                Wenn es die Zeit erlaubt, spielen wir auch einige Anfänger-/Kinder-Spiele (1 gegen 1 und 2 gegen 2) und 
                einige Elite-Spiele (4 gegen 4 und 5 gegen 5). Bei Turnieren in Cardiff wird in der Regel eine maximale Teilnehmerzahl von 
                bis zu 8 Teams angestrebt. Bei Turnieren, die von der EMUs ausgerichtet werden, ist die Teilnehmerzahl 
                manchmal auf 25 begrenzt, sodass fünf Teams jeweils zweimal gegeneinander spielen.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</b></p>
            <p>
                Die Pläne für eine britische Liga sind derzeit noch vage, da eine Erweiterung der Anzahl der Vereine als Voraussetzung angesehen wird.
            </p>
            <p>
                Wir haben überlegt, eine "Welsh League" zu gründen. Allein im Cardiff Club können wir manchmal 
                bis zu 20 Spieler aufstellen, daher haben wir darüber diskutiert, einmal pro Woche nach unserem 
                normalen Training ein Ligaspiel zu veranstalten, an dem festgelegte Spieler aus jedem Team teilnehmen.
            </p>
            <p>
                Einer der Gründe, warum wir dies tun möchten, wäre, "den Engländern zu zeigen, wie es gemacht wird" und die Wiederaufnahme 
                einer britischen Liga anzuregen.
            </p>
            <p>
                In der Praxis ist es jedoch schon schwer genug
                <ul>
                    <li>unsere eigenen Clubtreffen zweimal pro Woche aufrechtzuerhalten,</li>
                    <li>mindestens ein, möglicherweise zwei Turniere pro Jahr zu veranstalten,</li>
                    <li>weiterhin an internationalen Veranstaltungen teilzunehmen,</li>
                    <li>neue Einradfahrer auszubilden und neue Spieler zu gewinnen.</li>
                </ul>
            </p>
            <p>
                Daher ist es uns noch nicht gelungen, uns zu organisieren, um entweder eine walisische oder eine britische 
                Liga für Einradhockey wieder ins Leben zu rufen.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary">
                <b>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale 
                Meisterschaft oder ein ähnliches Großereignis?</b>
            </p>
            <p>
                Der Cardiff Unicycle Hockey Club veranstaltet in der Regel ein bis zwei Turniere pro Jahr, 
                wie oben beschrieben. Das EMUs-Team veranstaltet ebenfalls gelegentlich Turniere, 
                jedoch höchstens einmal pro Jahr. Das Londoner Team und das Southampton-Team haben seit einiger Zeit 
                keine Turniere mehr veranstaltet, aber wir hoffen weiterhin, dass sie wieder damit beginnen werden.
            </p>
            <p>
                Es gibt einige Events für andere Einrad-Disziplinen in Großbritannien, aber 
                selbst diese haben mit Teilnehmerzahlen und Rentabilität zu kämpfen. Manchmal gibt es 
                Events im Zusammenhang mit Jonglier-Conventions, aber diese haben selten Einradhockey im Programm.
            </p>
            <p>
                Wir haben so oft wie möglich an internationalen Events teilgenommen, aber die Organisation ist 
                schwierig.<br>Ich weiß, dass die Lunis vor etwa 8 bis 10 Jahren in der deutschen Liga gespielt haben, 
                aber ich persönlich habe es nie geschafft, daran teilzunehmen. Ich war bei drei Europameisterschaften 
                dabei, außerdem haben wir eine Reise nach Detmold unternommen, um ein Freundschafts-Miniturnier 
                gegen die Hockey Hawks zu spielen, die wir bei der EUC 2017 zum ersten Mal getroffen (und besiegt) haben.
            </p>
            <p>
                Ich bin sehr zuversichtlich, dass wir in den nächsten Jahren die Teilnahme am Einradhockey in 
                Großbritannien steigern, neue Vereine gründen und ehemalige Vereine wiederbeleben können.
            </p>
            <p>
                Vielleicht wäre eine Anerkennung durch die deutsche Liga und eine Zusammenarbeit 
                mit ihr ein guter Startschuss für diese Bemühungen.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Was sind die Höhepunkte im Laufe einer Saison bei euch?</b><br>(z. B. Turniere, Meisterschaften, Treffen, Conventions, …)</p>
            <p>
                <ul>
                    <li>Cardiff Einrad-Hockey-Turniere (ein- bis zweimal pro Jahr)</li>
                    <li>EMU-Turniere (etwa einmal pro Jahr)</li>
                    <li>Jeden Sonntagabend (20:00 - 21:30 Uhr) in Cardiff (Training + Einradfahren und -spielen lernen)</li>
                    <li>Jeden Mittwochabend (20:00 - 21:00 Uhr) in Cardiff (Training für fortgeschrittene Spieler)</li>
                    <li>Jeden Donnerstagabend (20:00 - 22:00 Uhr) in Hackney (London Lunis)</li>
                    <li>Jeden Dienstagabend (20:00 - 22:00 Uhr) in Long Eaton (EMUs)</li>
                    <li>Jeden Sonntagmorgen (11:30 - 12:30 Uhr) in Southampton </li>
                </ul>
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</b></p>
            <p>
                Jeder und alle. Deutschland, natürlich. Die Schweiz, natürlich. Australien, weil „Straya“.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Habt ihr besondere Rituale rund um eure Spiele?</b><br>(z. B. Torhymnen, Traditionen, …)</p>
            <p>
                Cardiff hat einen gewissen Ruf als Partyhochburg, aber dazu kann ich mich unmöglich äußern.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</b></p>
            <p>
                Unser Gemeinschaftsgefühl ist sehr stark. Das Schöne am Cardiff Club ist, dass er sehr generationsübergreifend 
                ist. Familien kommen zusammen, und sowohl Eltern als auch Kinder spielen Einradhockey, was selbst in einem kommunalen Sportverein ungewöhnlich ist.
            </p>
        </div>

        <div class="w3-section">
            <p class="advent-text-secondary"><b>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei unseren Spielerinnen und Spielern und Teams?</b></p>
            <p>
                Ich kenne die deutsche Einradhockey-Szene seit den Tagen von LaHiMo und für mich ist sie der Inbegriff dafür, 
                wie ein Ligasystem für Einradhockey funktionieren sollte. Ich träume davon, dass eines Tages sechzig Vereine an einer britischen Liga teilnehmen.
            </p>
            <p>
                Die Qualität Ihrer Spieler ist außergewöhnlich, und ich bin beeindruckt, wie Sie so viele Menschen für diesen Sport begeistern können. 
                Ich glaube, dass die Geschlechterverteilung in den deutschen Teams auch etwas ist, das wir anstreben sollten, 
                da wir immer noch, aus unerklärlichen Gründen, eine Tendenz zu männlichen Spielern haben. Wir arbeiten hart daran, 
                aber es ist eine ständige Herausforderung.
            </p>
            <p>
                Mein Lieblingsverein in der deutschen Liga sind die Baukau Boogaloos. Ich habe sie zum ersten Mal bei diesem Spiel im Finale 
                der Europameisterschaft 2013 gesehen und war sofort begeistert.
            </p>
        </div>
        
        <div class="w3-section">
            <p>
                <i>Die Antworten haben wir von Ben Tullis erhalten. Er spielt für das Einradhockeyteam aus Cardiff, Wales. Auf Instagram findet ihr das Team unter <a href="https://www.instagram.com/unicyclecardiff/" class="no w3-text-primary w3-hover-text-secondary">@unicyclecardiff</a>.</i>
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