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


<?php if ($now >= new DateTime('2025-12-24 10:00')): ?>
    
    <div class="w3-display-container w3-round-xlarge w3-padding-16" style="margin-top: 16px;">

       
        <!-- Text -->
        <div style="text-align: center;">
            <p class="advent-text-secondary" style="font-style: italic;">
                Frohe Weihnachten wünschen wir,<br>
                der Adventskalender endet hier.<br>
                Alle Türchen sind nun offen gewesen,<br>
                die letzten Zeilen könnt ihr lesen.<br>
                Zum Schluss gibt&apos;s noch ein kleines Spiel,<br>
                ein Rätsel wartet - macht doch mit, ganz viel.<br>
                Mit etwas Glück und klarem Verstand<br>
                winkt ein Gewinn zum Weihnachtsabend.<br>
            </p>
            <p>Mit diesem Weihnachtsgruß und einer weiteren Überraschung beenden wir den ersten Einradhockey-Adventskalender!</p>
            <p>Habt ihr aufmerksam zugeschaut?<br>Damit euch über die kommenden Tage nicht langweilig wird, gibt es hier unser Rätsel zum Gewinnspiel.</p>
            <p>Rätselt fleißig, findet das Lösungswort und schickt es uns bis zum 13. Januar entweder per Mail an <?=Html::mailto(Env::OEFFIMAIL)?> oder über Instagram (als Nachricht oder in den Fragensticker). Ausgelost werden fünf Gewinner*innen, die wir im Anschluss kontaktieren.</p>
            <p>Frohe Weihnachten wünscht das Team Social Media!</p>
        </div>
        

        <!-- Tabelle -->
        <div class="w3-section">
            <table class="w3-table w3-striped w3-border w3-bordered">
                <thead class="w3-primary">
                    <tr>
                        <th>Türchen</th>
                        <th>Hinweis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>23</td>
                        <td>Erster Buchstabe des vorgestellten Landes</td>
                    </tr>
                    <tr>
                        <td>17</td>
                        <td>Erster Buchstabe der richtigen Antwort</td>
                    </tr>
                    <tr>
                        <td>18</td>
                        <td>Zweiter Buchstabe des gesuchten Teams</td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Dritter Buchstabe des Teams, das in der Saison 2024/2025 die meisten Turniere gespielt hat</td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Erster Buchstabe des Ortes der Meisterschaft</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Buchstabe, der am häufigsten in dem Überbegriff des Regel-Checks vorkommt</td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Vorletzter Buchstabe des vorgestellten Landes</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>Letzter Buchstabe des Überbegriffes des Regel-Checks</td>
                    </tr>
                    <tr>
                        <td>14</td>
                        <td>Erster Buchstabe des Begriffes, unter dem die Meisterschaft in Erinnerung geblieben ist</td>
                    </tr>
                    <tr>
                        <td>19</td>
                        <td>Erster Buchstabe des zweiten vorgestellten Landes</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Erster Buchstabe der richtigen Antwort</td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td>Letzter Buchstabe der richtigen Antwort</td>
                    </tr>
                    <tr>
                        <td>16</td>
                        <td>Erster Buchstabe des ersten vorgestellten Landes</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Erster Buchstabe der richtigen Antwort</td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Der Vokal, der in dem Wort des besonderen Tages nicht vorkommt</td>
                    </tr>
                    <tr>
                        <td>21</td>
                        <td>Erster Buchstabe dessen, was 1996 in unserer Sportart gegründet wurde (Einradhockey-…)</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Buchstabe, der am häufigsten im gesuchten Teamnamen vorkommt</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>Erster Buchstabe des ersten vorgestellten Landes</td>
                    </tr>
                    <tr>
                        <td>20</td>
                        <td>Buchstabe der falschen Antwort</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Erster Buchstabe der richtigen Antwort</td>
                    </tr>
                    <tr>
                        <td>22</td>
                        <td>Erster Buchstabe der Stadt, aus der das gesuchte Team kommt</td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td>Zweiter Buchstabe der richtigen Halle</td>
                    </tr>
                    <tr>
                        <td>24</td>
                        <td>Letzter Buchstabe des Wunsches zum Feiertag</td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Fünfter bzw. letzter Buchstabe der Faktenkategorie</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bild -->
        <div class="w3-section">
            <div>
                <img src="../../../bilder/advent/UGGa24aDpCbs/door.jpg" class="w3-image w3-round" style="max-width: 350px; display: block; margin: auto;">
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