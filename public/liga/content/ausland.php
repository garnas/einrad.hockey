<?php

// Logik
require_once '../../../init.php';
$now = new DateTime();

// Layout
Html::$titel = "Einradhockey im Ausland | Deutsche Einradhockeyliga";
Html::$content = "Berichte über die Art und Weise, wie Einradhockey in anderen Ländern stattfindet.";

include '../../../templates/header.tmp.php'; ?>

<style>
    .flag {
        width: 40px;          /* Größe der Flagge */
        height: 40px;
        border-radius: 50%;   /* Kreisform */
        border: 3px solid #f1f1f1; /* Graue Border */
        overflow: hidden;     /* Alles außerhalb des Kreises ausblenden */
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 10px;
    }

    .flag img {
        width: auto;
        height: 100%;
        object-fit: cover;    /* Bild wird zugeschnitten, falls nötig */
        object-position: center; /* Zentrum der Flagge bleibt sichtbar */
    }

    ul.flag-list {
    display: block; /* zwingt die Liste, blockmäßig zu bleiben */
    padding: 0;
    margin: 0;
    list-style: none; /* optional, wenn du keine Standard-Punkte willst */
    }

    ul.flag-list li {
        display: flex;       /* Flagge + Text nebeneinander */
        align-items: center; /* Vertikal mittig */
        margin-bottom: 8px;  /* Abstand zwischen den Items */
    }
</style>


<div class="w3-display-container w3-padding-16" style="margin-top: 16px;">
    <div class="w3-section" style="line-height: 1.2;">
        <h1 class="w3-text-primary">
            Wie läuft Einradhockey im Ausland?
        </h1>
    </div>
    
    <div class="w3-section">
        <p style="font-style: italic; background-color: lightgrey; padding: 8px 16px;">
            Wir haben aus ganz unterschiedlichen Ländern nachgefragt: Wie läuft Einradhockey bei euch? Alle Antworten auf unsere Fragen findet ihr hier!
        </p>
        
        <ul class="flag-list">
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/ch.png" alt="Schweiz"></span>
                <a href="#switzerland" class="no w3-text-primary w3-hover-text-secondary">
                    <span>Schweiz</span>
                </a>
            </li>
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/us.png" alt="USA"></span>
                <a href="#usa" class="no w3-text-primary w3-hover-text-secondary">
                    <span>USA</span>
                </a>
            </li>
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/hk.png" alt="Hongkong"></span>
                <a href="#hongkong" class="no w3-text-primary w3-hover-text-secondary">
                    <span>Hongkong</span>
                </a>
            </li>
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/dk.png" alt="Dänemark"></span>
                <a href="#denmark" class="no w3-text-primary w3-hover-text-secondary">
                    <span>Dänemark</span>
                </a>
            </li>
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/at.png" alt="Österreich"></span>
                <a href="#austria" class="no w3-text-primary w3-hover-text-secondary">
                    <span>Österreich</span>
                </a>
            </li>
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/au.png" alt="Australien"></span>
                <a href="#australia" class="no w3-text-primary w3-hover-text-secondary">
                    <span>Australien</span>
                </a>
            </li>
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/cz.png" alt="Tschechien"></span>
                <a href="#czech" class="no w3-text-primary w3-hover-text-secondary">
                    <span>Tschechien</span>
                </a>
            </li>
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/fr.png" alt="Frankreich"></span>
                <a href="#france" class="no w3-text-primary w3-hover-text-secondary">
                    <span>Frankreich</span>
                </a>
            </li>
            <li>
                <span class="flag"><img src="https://flagcdn.com/w40/gb.png" alt="Vereinigtes Königreich"></span>
                <a href="#greatbritain" class="no w3-text-primary w3-hover-text-secondary">
                    <span>Vereinigtes Königreich</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Switzerland -->
    <div class="w3-section">
        <h2 id="switzerland" class="w3-text-secondary">
            Schweiz
        </h2>        
        
        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>Einen regelmäßigen Ligabetrieb gibt es in der Schweiz seit 2003.</p>
        </div>
        
        <div class="w3-section">
            <p><strong>Wie hat sich die Einradhockey-Community bei euch entwickelt?</strong></p>
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
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
            <p>
                23 Mannschaften:<br>
                A-Liga: 9 Teams<br>
                B-Liga: 6 Teams<br>
                C-Liga: 8 Teams
            </p>
        </div>
        
        <div class="w3-section">
            <p>
                <strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong><br>
                (z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)
            </p>
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
            <p>
                <strong>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis?</strong>
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
            <p>
                <strong>Was sind die Höhepunkte im Laufe einer Saison bei euch?</strong><br>
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

        <p>
            <i>Die Antworten haben wir von Lilo Eltz erhalten. Sie ist <a href="https://www.swiss-iuc.ch/Organisation/Einrad/Einradhockey-Liga" class="no w3-text-primary w3-hover-text-secondary">Ressortleiterin Einradhockey</a> von <a href="https://www.swiss-iuc.ch/Home" class="no w3-text-primary w3-hover-text-secondary">Swiss Indoor- & Unicycling.</a></i>
        </p>
    </div>

    <!-- USA -->
    <div class="w3-section">
        <h2 id="usa" class="w3-text-secondary">
            USA
        </h2>
        
        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>Seit Mitte der 1990er.</p>
        </div>
        
        <div class="w3-section">
            <p><strong>Wie hat sich die Einradhockey-Community bei euch entwickelt?</strong></p>
            <p>
                Es sind hauptsächlich aktuelle oder ehemalige Freestyle-Fahrer vom 
                TCUC [Twin Cities Unicycle Club], die zum Spaß spielen. Wir trainieren gelegentlich 
                und nehmen oft an nationalen Turnieren oder Unicon teil, aber meistens als Pickup-Teams.
            </p>
        </div>
        
        <div class="w3-section">
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
            <p>Eins.</p>
        </div>
        
        <div class="w3-section">
            <p><strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>Es gibt keinen Ligabetrieb.</p>
        </div>
        
        <div class="w3-section">
            <p><strong>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</strong></p>
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
            <p>
                <strong>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale 
                Meisterschaft oder ein ähnliches Großereignis??</strong>
            </p>
            <p>
                Wir veranstalten in der Regel mehrmals im Jahr ein Clubfest, bei dem wir verschiedene 
                Einrad-Aktivitäten anbieten, darunter ein Spaß-Hockey-Turnier. Außerdem findet jedes Jahr 
                im Rahmen der Convention ein Turnier statt, bei dem die Gewinner mit Medaillen ausgezeichnet werden.
            </p>
        </div>
            
        <div class="w3-section">
            <p>
                <strong>Was sind die Höhepunkte im Laufe einer Saison bei euch?</strong><br>
                (z. B. Turniere, Meisterschaften, Treffen, Conventions, …)
            </p>
            <p>
                Die nationale Convention und die Unicon, wenn wir es schaffen.
            </p>
        </div>
        
        <div class="w3-section">
            <p>
                <strong>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</strong>
            </p>
            <p>
                Alle, die zu Besuch kommen möchten! Vor allem diejenigen, die über Teams 
                und Ligen verfügen, damit sie Ratschläge geben und zeigen können, wie sie trainieren.
            </p>
        </div>
        
        <div class="w3-section">            
            <p>
                <strong>Habt ihr besondere Rituale rund um eure Spiele?</strong><br>
                (z. B. Torhymnen, Traditionen, …)
            </p>
            <p>Nicht wirklich.</p>
        </div>
        
        <div class="w3-section">
            <p>
                <strong>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</strong>
            </p>
            <p>Nur andere Clubmitglieder die anfeuern.</p>
        </div>
        
        <div class="w3-section">
            <p>
                <strong>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei 
                unseren Spielerinnen und Spielern und Teams?</strong>
            </p>
            <p>
                Nach dem, was wir bei der letzten Unicon gesehen haben, sind sie auf einem sehr hohen 
                Niveau und wir waren ihnen klar unterlegen.
            </p>
        </div>
        
        <p>
            <i>Die Antworten haben wir von Ryan Wood erhalten. Er ist Mitglied des <a href="https://www.tcuc.org/" class="no w3-text-primary w3-hover-text-secondary">Twin Cities Unicycle Club</a> aus Minnesota.</i>
        </p>
    </div>

    <!-- Hongkong -->
    <div class="w3-section">
        <h2 id="hongkong" class="w3-text-secondary">
            Honk Kong
        </h2>

        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>Ungefähr 30 Jahre.</p>
        </div>

        <div class="w3-section">
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
            <p>Eins.</p>
        </div>

        <div class="w3-section">
            <p><strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Wir hatten ein halbjährliches Turnier, die Asia Pacific Unicycle Championships, 
                zwischen Hongkong, Singapur, Australien, Südkorea und manchmal Taiwan und den Philippinen.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</strong></p>
            <p>
                Im Moment nicht.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis??</strong>
            </p>
            <p>Wir spielen jetzt wöchentlich im Verein.</p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Was sind die Höhepunkte im Laufe einer Saison bei euch?</strong><br>
                (z. B. Turniere, Meisterschaften, Treffen, Conventions, …)
            </p>
            <p>Jede Woche spielen!</p>
        </div>

        <div class="w3-section">
            <p><strong>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</strong></p>
            <p>Alle üblichen Freunde aus dem asiatisch-pazifischen Raum.</p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Habt ihr besondere Rituale rund um eure Spiele?</strong><br>
                (z. B. Torhymnen, Traditionen, …)
            </p>
            <p>Nein.</p>
        </div>

        <div class="w3-section">
            <p><strong>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</strong></p>
            <p>
                Wir sind sehr offen für Einsteiger und neue Spieler. Wir haben das 
                Motto: "Es ist egal, wer du bist oder warum du hier bist, wir freuen uns, den 
                Spaß am Einradfahren (und vor allem am Einradhockey) mit dir zu teilen."
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei 
                unseren Spielerinnen und Spielern und Teams?</strong>
            </p>
            <p>Großer Respekt - ihr seid die Besten!</p>
        </div>

        <p>
            <i>Die Antworten haben wir von Martin Turner erhalten. Er administriert die <a href="https://www.facebook.com/groups/97367907315" class="no w3-text-primary w3-hover-text-secondary">Facebook Gruppe Unicycle Hongkong UNIHK</a> für Einradhockey in Hongkong.</i>
        </p>
    </div>

    <!-- Denmark -->
    <div class="w3-section">
        <h2 id="denmark" class="w3-text-secondary">
            Dänemark
        </h2>

        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>Einradhockey gibt es hier seit mindestens 20 Jahren.</p>
        </div>

        <div class="w3-section">
            <p><strong>Wie hat sich die Einradhockey-Community bei euch entwickelt?</strong></p>
            <p>In meiner Region gibt es weniger Hockey und mehr Freestyle oder Flatland.</p>
        </div>

        <div class="w3-section">
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
            <p>
                Wenn die dänische Meisterschaft stattfindet, dann treten 6 bis 7 Teams an und 
                es ist eher zufällig, wer spielt und alle können mitmachen. Es ist also keine so 
                große Disziplin in Dänemark. :)
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Wir haben keine Liga und nicht genug Leute, um eine Liga zu gründen. [Anmerkung: Floorball ist in Dänemark sehr verbreitet, 
                daher haben die meisten Fahrer schon einmal gespielt. Das macht es ihnen leicht, bei Meisterschaften mit 
                Floorballschlägern Einradhockey zu spielen.]
            </p>
        </div>
    
        <div class="w3-section">
            <p>
                <strong>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis??</strong>
            </p>
            <p>
                Wir haben einmal im Jahr ein Turnier bei der dänischen Meisterschaft. Diese ist auch der Höhepunkt.
            </p>
        </div>
    
        <div class="w3-section">
            <p>
                <strong>Habt ihr besondere Rituale rund um eure Spiele?</strong><br>
                (z. B. Torhymnen, Traditionen, …)
            </p>
            <p>
                Wir haben einen talentierten DJ, der mitreißende Songs und Torlieder spielt.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</strong>
            </p>
            <p>
                Familie und andere Einradfahrer schauen sich die Spiele an. 
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei 
                unseren Spielerinnen und Spielern und Teams?</strong>
            </p>
            <p>
                Wir kennen uns mit den deutschen Ligen nicht aus, daher denke ich nicht viel darüber nach :)
            </p>
        </div>

        <p>
            <i>Die Antworten haben wir von Sofus Agerskov erhalten. Auf Instagram findet ihr ihn unter <a href="https://www.instagram.com/sofusagerskov_uni/" class="no w3-text-primary w3-hover-text-secondary">@sofusagerskov_uni</a>.</i>
        </p>
    </div>

    <!-- Austria -->
    <div class="w3-section">
        <h2 id="austria" class="w3-text-secondary">
            Österreich
        </h2>

        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>
                2011 wurde das erste österreichweite Einradhockeyturnier ausgerichtet und leitete 
                damit auch den Beginn des Einradhockeysports in Österreich ein. Vermutlich gibt es den 
                Einradhockeysport schon seit mindestens 2007, aber die historischen Aufzeichnungen sind uneindeutig.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie hat sich die Einradhockey-Community bei euch entwickelt?</strong></p>
            <p>
                Nach dem ersten Hockeyturnier 2011 at sich ein Boom entwickelt, in den folgenden Jahren entwickelte sich sowohl 
                im Burgenland mit den One-Wheel-Dragons als auch in Marchtrenk mit den Flying Unis parallel zu Steyr zwei 
                weitere Einradhockey-Hotspots. 2014 wurde das Steyrer Einradhockeyturnier zum ersten Mal in der großen Dreifachhalle 
                ausgerichtet, mit internationaler Beteiligung. Auch die One-Wheel-Dragnos richteten Einradhockeyturniere 
                aus. Letztendlich hat sich Steyr als Einradhockey-Mekka in Österreich durchgesetzt.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
            <p>
                Aktuell sind die Teams des Steyrer Vereins die einzig verbliebenen Einradhockeyteams in der 
                allgemeinen Klasse. In Wien reifen jedoch Nachwuchsteams heran.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong>
                <br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Nein, dafür gibt es in Österreich zu wenig Mannschaften. Früher gab es intensive Derbys zwischen 
                den One-Wheel-Dragons und Steyr bei aufgeheizter Stimmung. Der Sieger war Österreichs 
                beste Einradhockeymannschaft.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</strong></p>
            <p>
                Ideen gibt es viele, aber Einradhockey ist zu wenig verbreitet in Österreich.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft oder ein ähnliches Großereignis?</strong></p>
            <p>
                Das größte Turnier ist das Int. Steyrer Einradhockeyturnier im Frühjahr mit, in der Vergangenheit, Beteiligungen 
                aus Deutschland, Tschechien, Schweiz, Italien und Österreich.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Was sind die Höhepunkte im Laufe einer Saison bei euch?</strong>
                <br>(z. B. Turniere, Meisterschaften, Treffen, Conventions, …)</p>
            <p>
                Der Höhepunkt ist das Int. Steyrer Einradhockeyturnier als unser Saisonabschluss. 🙂
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</strong></p>
            <p>
                Natürlich sind hier unsere deutschen Nachbarn zu erwähnen, die aus dem Turnier kaum noch wegzudenken sind. Auch sonst 
                hatten wir immer Spaß mit internationaler Beteiligung. Geografische Entfernungen außen vor gelassen, wäre eine Beteiligung aus Dänemark sicher einmal ein netter Impuls 🙂
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Habt ihr besondere Rituale rund um eure Spiele?</strong>
                <br>(z. B. Torhymnen, Traditionen, …)</p>
            <p>
                Wir haben eines der besten selbst errichteten Buffets, Torhymnen, Stadionsprecher 
                und je nach Verfügbarkeit und Zeitplan auch Shows in den Pausen. 🙂
            </p>
        </div>        

        <div class="w3-section">
            <p><strong>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</strong></p>
            <p>
                Früher gab es intensive Derbys zwischen den One-Wheel-Dragons und Steyr. Mittlerweile konzentriert 
                sich die Fankultur rund um den Steyrer Verein.
            </p>
        </div>
    
        <div class="w3-section">
            <p><strong>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei unseren Spielerinnen und Spielern und Teams?</strong></p>
            <p>
                Teams aus der Deutschen Einradhockeyliga nehmen regelmäßig an unsere Einradhockeyturnier teil, was uns immer viel Spaß macht. Des Weiteren 
                unterstützen vereinzelt Spielerinnen Teams in der Deutschen Liga. Die technische Fertigkeit der deutschen Spielerinnen und Spieler 
                durch gutes Training ist auf jeden Fall erwähnenswert, aber auch die Fairness und das Beachten von Spielregeln.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Stereotype über das österreichische Hockeygeschehen</strong></p>
            <p>
                In Österreich ist ein SUP kein Foul sondern ein Stilmittel dessen Qualität an der Flugkurve des Gegners gemessen wird ;)
                [Anmerkung: Diese Frage haben wir zwar nicht gestellt, mussten aber dennoch schmunzeln.]
            </p>
        </div>

        <p>
            <i>Die Antworten haben wir von Theo Crazzolara erhalten. Er ist unter anderem Head of Social Media für die <a href="https://www.unicon22.at/" class="no w3-text-primary w3-hover-text-secondary">Unicon 22</a> in Österreich.</i>
        </p>
    </div>

    <!-- Australia -->
    <div class="w3-section">
        <h2 id="australia" class="w3-text-secondary">
            Australien
        </h2>
        
        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>Erstmals gespielt um 1994.</p>
        </div>

        <div class="w3-section">
            <p><strong>Wie hat sich die Einradhockey-Community bei euch entwickelt?</strong></p>
            <p>
                Sydney hat die größte Community, aber auch in Melbourne und Canberra gibt es eine 
                Gemeinschaft von Hockeyspielern, die sich regelmäßig trifft. Das bedeutet zwar, 
                dass wir in Australien mehrere Communities haben, aber Melbourne ist 877 km von Sydney 
                entfernt, sodass man nicht einfach am Wochenende zu einem Spiel nach Melbourne fahren kann.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
            <p>
                Es gibt etwa vier Teams, die regelmäßig zusammen spielen, und zwei oder mehr weitere Mix-Teams, die bei Turnieren aus 
                zusätzlichen Spielern gebildet werden können, die weniger regelmäßig spielen und daher nicht in Teams sind.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>
                Es gibt eine Liga, die 2014 gegründet wurde. Anfangs gab es etwa 9 Teams zwischen Sydney und Canberra, aber der Wettbewerb 
                war nicht für alle attraktiv, und mit Covid sind die Zahlen zurückgegangen. Es gibt immer noch einen Wettbewerb, aber 
                statt 6 Turnieren pro Jahr veranstalten wir jetzt drei, eines in jeder der Städte: Canberra, Sydney, Melbourne.
            </p>
        </div>
        
        <div class="w3-section">
            <p>
                <strong>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis?</strong>
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
            <p><strong>Was sind die Höhepunkte im Laufe einer Saison bei euch?</strong><br>(z. B. Turniere, Meisterschaften, Treffen, Conventions, …)</p>
            <p>
                Die gemeinsamen Abendessen nach dem Turniertag sind immer sehr gemütlich, und der zweite Tag, an dem die 
                Teilnehmer mit anderen Spielern zusammenspielen können, kommt in der Regel sehr gut an. Schwächere Spieler 
                können mit stärkeren Spielern zusammenspielen und so das Zusammenspiel mit anderen Spielern üben.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</strong>
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
            <p>
                <strong>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</strong>
            </p>
            <p>
                Unsere Community ist klein, sodass man alle gut kennt. Fans sind in der Regel nur Familienmitglieder. Es wäre schön, wenn 
                sie größer wäre wie die deutsche und die schweizer Liga.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei unseren Spielerinnen und Spielern und Teams?</strong>
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
        
        <p>
            <i>Die Antworten haben wir von Steven Hughes erhalten. Seit einigen Austragungen der Unicon ist er dort der Hockey Director - so auch in Österreich.</i>
        </p>
    </div>

    <!-- Czech Republic -->
    <div class="w3-section">
        <h2 id="czech" class="w3-text-secondary">
            Tschechien
        </h2>

        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>Ich kann sagen, seit 2014 - fast 12 Jahre.</p>
        </div>

        <div class="w3-section">
            <p><strong>Wie hat sich die Einradhockey-Community bei euch entwickelt?</strong></p>
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
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
            <p>
                Ich kenne nur zwei: Prague Unicycle Hockey Team und Uners Litoměřice.
            </p>
            <p>
                Manchmal organisiert TryOne, eine von Ade Gerža geleitete Einradschule, zum Spaß ein Einradhockey-Turnier. Aber die Schule ist nicht auf Einradhockey ausgerichtet.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
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
            <p><strong>Habt ihr besondere Rituale rund um eure Spiele?</strong><br>(z. B. Torhymnen, Traditionen, …)</p>
            <p>
                Ich glaube, wir haben keine Traditionen oder Rituale, die uns betreffen. Ich werde versuchen, mir etwas auszudenken. :D
            </p>
        </div>

        <p>
            <i>Die Antworten haben wir von Matěj Koudelka erhalten. Er ist Ligavertreter des Prague Unicycle Hockey Team. Auf Instagram findet ihr sie unter <a href="https://www.instagram.com/unicycle_hockey.cz/" class="no w3-text-primary w3-hover-text-secondary">@unicycle_hockey.cz</a>.</i>
        </p>
    </div>

    <!-- France -->
    <div class="w3-section">
        <h2 id="france" class="w3-text-secondary">
            Frankreich
        </h2>

        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>
                Einradhockey wird in Frankreich seit mindestens 2005 gespielt. An der Unicon 2006 
                nahmen mehrere Teams teil. Die französische Einradmeisterschaft (<q>Coupe de France de Monocycle<q>; CFM) 2010 
                war das erste Turnier. Die CFM ist jedes Jahr Ende Oktober.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie hat sich die Einradhockey-Community bei euch entwickelt?</strong></p>
            <p>
                Ziemlich ungleichmäßig. CFM 2013 (13 Teams), CFM 2015 (5 Teams), CFM 2019 (20 Teams). Es gibt 
                Phasen mit großem Interesse, auf die ein Rückgang folgt. Eine Entwicklung scheitert unter 
                anderem an den Distanzen zwischen den Vereinen.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
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
            <p><strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong><br>(z. B. Aufbau der Liga oder Ligen, Punktewertung, Auf- und Abstieg, …)</p>
            <p>Wir haben keine Liga.</p>
        </div>

        <div class="w3-section">
            <p><strong>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</strong></p>
            <p>
                Es hängt davon ab, ob jeder Verein Einradhockey als eine seiner vorrangigen 
                Sportarten auswählt. Wir haben nicht viele Hockeyplätze. Wir 
                müssten uns an Rollhockeyvereine wenden. Außerdem braucht es mehr Vereine 
                die bereit wären, Einradhockey-Turniere auszurichten.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale Meisterschaft 
                oder ein ähnliches Großereignis?</strong>
            </p>
            <p>
                Derzeit wird jedes Jahr im April nur ein Turnier in Orléans organisiert. Außerdem 
                findet ein Turnier während des französischen Pokals statt.
            </p>
        </div>
    
        <div class="w3-section">
            <p><strong>Was sind die Höhepunkte im Laufe einer Saison bei euch?</strong><br>(z. B. Turniere, Meisterschaften, Treffen, Conventions, …)</p>
            <p>Die beiden zuvor erwähnten Turniere: Orléans und CFM.</p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</strong>
            </p>
            <p>
                Wir sind offen gegenüber jeder Einladung. Wir wissen, dass die Schweiz, 
                Deutschland, Großbritannien und auch Österreich auf einem höheren Niveau sind 
                als wir. Oder vielleicht auch Belgien, da sie am mit am nächsten für uns sind.
            </p>    
        </div>

        <div class="w3-section">
            <p><strong>Habt ihr besondere Rituale rund um eure Spiele?</strong><br>(z. B. Torhymnen, Traditionen, …)</p>
            <p>
                Das ist nicht ausschließlich für Einradhockey: Wenn eine Mannschaft ein Teil eines großen 
                Vereins ist, gibt es viel Unterstützung. Die meisten Mannschaften haben 
                ihren eigenen Schlachtruf, um sich selbst zu motivieren.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</strong>
            </p>
            <p>
                Das Zugehörigkeitsgefühl zu einem Verein ist derzeit sehr stark. Bei uns ist der Sport 
                noch nicht weit genug entwickelt, um eine Nationalmannschaft in Betracht zu ziehen.
            </p>
        </div>

        <div class="w3-section">
            <p>
                <strong>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei unseren Spielerinnen und Spielern und Teams?</strong>
            </p>
            <p>
                Wir wissen, dass die deutsche Liga eine sehr umkämpfte Meisterschaft ist, die dem Niveau der Schweizer Liga sehr nahe kommt.
            </p>
        </div>

        <p>
            <i>Die Antworten haben wir von Yann Henry und Sebastien Golliet erhalten. Hier haben wir sie zu einer Antwort zusammengefasst.</i>
        </p>
    </div>

    <!-- Great Britain -->
    <div class="w3-section">
        <h2 id="greatbritain" class="w3-text-secondary">
            Großbritannien
        </h2>
        
        <div class="w3-section">
            <p><strong>Seit wann gibt es Einradhockey in eurem Land?</strong></p>
            <p>
                Einradhockey wird in Großbritannien seit Anfang der 1990er Jahre gespielt. Ich habe 1993 
                angefangen und bis 1996 für das Team aus Hastings gespielt. Dann habe ich etwa 14 Jahre lang 
                pausiert, bis ich 2010 das Team in Cardiff gefunden habe. Seit etwa 16 Jahren spiele ich nun 
                für das Team aus Cardiff, nehme an Turnieren teil und richte sie aus.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie hat sich die Einradhockey-Community bei euch entwickelt?</strong></p>
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
            <p><strong>Wie viele Mannschaften oder Teams gibt es aktuell bei euch?</strong></p>
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
            <p><strong>Gibt es einen Ligabetrieb? Wenn ja, wie ist dieser organisiert?</strong></p>
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
            <p><strong>Wenn nein, gibt es Ideen, eine Liga einzuführen? Wie könnte sie aussehen?</strong></p>
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
            <p>
                <strong>Wie häufig finden bei euch Turniere oder Spieltage statt? Gibt es eine nationale 
                Meisterschaft oder ein ähnliches Großereignis?</strong>
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
            <p><strong>Was sind die Höhepunkte im Laufe einer Saison bei euch?</strong><br>(z. B. Turniere, Meisterschaften, Treffen, Conventions, …)</p>
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
            <p><strong>Welche Länder würdet ihr gerne mal zu einem Turnier einladen und warum?</strong></p>
            <p>
                Jeder und alle. Deutschland, natürlich. Die Schweiz, natürlich. Australien, weil „Straya“.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Habt ihr besondere Rituale rund um eure Spiele?</strong><br>(z. B. Torhymnen, Traditionen, …)</p>
            <p>
                Cardiff hat einen gewissen Ruf als Partyhochburg, aber dazu kann ich mich unmöglich äußern.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie ist eure Fankultur oder euer Gemeinschaftsgefühl rund um Einradhockey?</strong></p>
            <p>
                Unser Gemeinschaftsgefühl ist sehr stark. Das Schöne am Cardiff Club ist, dass er sehr generationsübergreifend 
                ist. Familien kommen zusammen, und sowohl Eltern als auch Kinder spielen Einradhockey, was selbst in einem kommunalen Sportverein ungewöhnlich ist.
            </p>
        </div>

        <div class="w3-section">
            <p><strong>Wie nehmt ihr die Deutsche Einradhockeyliga war? Welche Spielstärken seht ihr bei unseren Spielerinnen und Spielern und Teams?</strong></p>
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
        
        <p>
            <i>Die Antworten haben wir von Ben Tullis erhalten. Er spielt für das Einradhockeyteam aus Cardiff, Wales. Auf Instagram findet ihr das Team unter <a href="https://www.instagram.com/unicyclecardiff/" class="no w3-text-primary w3-hover-text-secondary">@unicyclecardiff</a>.</i>
        </p>
    </div>
    
</div>

<?php include '../../../templates/footer.tmp.php'; ?>