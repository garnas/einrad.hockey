<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// Da die Challenge im Moment nicht stattfindet, wird man auf die Hauptseite weitergeleitet
header('Location: neues.php');
die();

require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/challenge.logic.php'; //Erstellt Challenge-Objekt nach der Validation

$color[0] = "background-color: rgb(189, 148, 107);";
$color[1] = "background-color: #a6b2d8;";
$color[2] = "background-color: #b8c1e0;";
$color[3] = "background-color: #cad0e8;";

// Fügt Confetti-Effekt hinzu
if ($akt_kilometerstand >= $challenge->ziel_kilometer) {
    Form::set_confetti(40,90,7000); 
    $color[0] = "background-color: rgb(189, 107, 153);";
    $ziel_text = 'Geschafft! Zusammen sind wir bis nach Sydney (16098,4&nbsp;km) geradelt. Zum am weitesten entfernten Einradhockeyteam.';
} else {
    $ziel_text = "Schaffen wir es zusammen bis nach Sydney? Zum am weitesten entfernten Einradhockeyteam.";
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "km-Challenge | Deutsche Einradhockeyliga";
$content = 'Hier sind die aktuellen Ergebnisse der Kilometer-Challenge zu sehen.';
include '../../templates/header.tmp.php';
?>

<!-- Überschrift -->
<h1 class="w3-text-primary">km-Challenge</h1>
<p class="w3-text-gray"><?=$challenge->challenge_start?> bis <?=$challenge->challenge_end?></p>

<!-- Button zum Teamcenter -->
<p class="w3-text-gray">
    Unglaubliche <b>22.771,6&nbsp;km sind wir gemeinsam geradelt</b>. Damit sind wir nicht nur von Berlin bis nach Sydney (16.098,4&nbsp;km) sondern wieder bis zurück nach Singapur gefahren. Aber nicht nur das Gesamtergebnis ist erstaunlich... 💪🏼
    <br><br>
    4.536,6&nbsp;km so viele km wie <b>FreiradFreiburg</b> hat kein anderes Team zusammen gesammelt. Auf Platz 2. mit ebenfalls spitzenmäßigen 2.661.7&nbsp;km erradelte das Team <b>Einradhockey Elmshorn</b>! Außerdem stark dabei die <b>Legionäre der MJC&nbsp;Trier</b> mit gemeinsamen 1.725,1&nbsp;km.
    <br><br>
    Was wir euch ebenfalls nicht vorenthalten wollen sind die klasse Einzelleistungen von <b>Tom&nbsp;(FreiradFreiburg)</b> in der Kategorie&nbsp;U16, <b>Uli&nbsp;(MJC Trier - Die Römer)</b> in der Kategorie&nbsp;50+ sowie <b>Luc&nbsp;(MJC Trier - Die Legionäre)</b> in der Kategorie&nbsp;Einradhockey-Rad.
    <br><br>
    Außerdem hervorheben möchten wir natürlich die km von <b>Tom</b> auch in der Einzelwertung aller Fahrer hat er sich den ersten Platz gesichert.
    <br><br>
    Wir sind immer noch erstaunt wie viele km ihr alle gefahren seid und hoffen dass ihr euch nun auch ohne die km-Challenge weiterhin aufs Rad schwingt und ein paar km zurücklegt. Vielen Dank an alle Teilnehmer*innen. Uns hat es wahnsinnig viel Spaß gemacht! Wir hoffen euch auch 😉
    <br><br>
    Und wie zu Beginn angekündigt werden alle genannten Fahrer*innen und Teams in den kommenden Wochen eine kleine Überraschung erhalten. ✨
    <br><br>
    Darüber hinaus kann sich <b>jedes Team, jede Spielerin und jeder Spieler die eigene Urkunde</b> aus dem Teamcenter herunterladen! 🥳
</p>
<a href='../teamcenter/tc_challenge.php' class="w3-button w3-secondary">Urkunde herunterladen!</a>

<h3 class="w3-text-secondary w3-margin-top">Gesamt-km</h3>

<!-- Buttons zum Ein/Ausblenden der Sonderauswertungen -->
<div class="w3-section w3-margin-top">
    <div id='button_da'>
        <button  class="w3-primary w3-block w3-button" onclick='modal("sonderauswertungen_panels");modal("button_da");modal("button_weg");'>
            <i class="material-icons">keyboard_arrow_down</i> Nennenswerte Fahrer*innen <i class="material-icons">keyboard_arrow_down</i>
        </button>
    </div>
    <div id='button_weg' style='display: none;'>     
        <button class="w3-primary w3-block w3-button" onclick='modal("sonderauswertungen_panels");modal("button_da");modal("button_weg");'>
            <i class="material-icons">keyboard_arrow_up</i> Nennenswerte Fahrer*innen <i class="material-icons">keyboard_arrow_up</i>
        </button>
    </div>
</div>

<!-- Kacheln für Sonderauswertungen -->
<div id='sonderauswertungen_panels' class="w3-row-padding w3-stretch" style='display: none;'>
    <!-- Alter ist U16 -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
        <p class="w3-center">
            U16
        </p>
        <p class="w3-center w3-xxlarge">
            <?=number_format($jung['kilometer'], 1, ',', '.');?> km
        </p>
        <p class="w3-center">
            <?=$jung['vorname']?><br /><?=$jung['teamname']?>
        </p>
        </div>
    </div>
    <!-- Alter ist 50+ -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
        <p class="w3-center">
            50+
        </p>
        <p class="w3-center w3-xxlarge">
            <?=number_format($alt['kilometer'], 1, ',', '.');?> km
        </p>
        <p class="w3-center">
            <?=$alt['vorname']?><br /><?=$alt['teamname']?>
        </p>
        </div>
    </div>
    <!-- Einradhockeyrad -->
    <div class="w3-third">
        <div class="w3-panel w3-primary w3-card-4">
        <p class="w3-center">
            Einradhockey-Rad
        </p>
        <p class="w3-center w3-xxlarge">
            <?=number_format($einradhockey['kilometer'], 1, ',', '.');?> km
        </p>
        <p class="w3-center">
            <?=$einradhockey['vorname']?><br /><?=$einradhockey['teamname']?>
        </p>
        </div>
    </div>
</div>

<h3 class="w3-text-secondary w3-margin-top">Teams</h3>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th class="w3-center">#</th>
            <th class="w3-left-align">Team</th>
            <th class="w3-center w3-hide-small">Mitglieder</th>
            <th class="w3-center w3-hide-small">Einträge</th>
            <th class="w3-right-align">Kilometer</th>               
        </tr>
        <?php 
            if (empty($teamliste)) {
                echo "<tr>";
                echo "<td colspan='5' class='w3-center'>Bisher keine Einträge vorhanden.</td>";
                echo "</tr>";
            } else {
                foreach ($teamliste as $team){?> 
                    <tr style="<?=$color[$team["platz"]] ?? ''?>">
                        <td class="w3-center"><?=$team["platz"]?></td>
                        <td style="white-space: nowrap;" class="w3-left-align"><?=$team["teamname"]?></td>
                        <td class="w3-center w3-hide-small"><?=$team["mitglieder"]?></td>
                        <td class="w3-center w3-hide-small"><?=$team["einträge"]?></td>
                        <td class="w3-right-align"><?=number_format($team["kilometer"], 1, ',', '.');?></td>
                    </tr>
                <?php } //end foreach 
            } //end if
            ?>
    </table>
</div>

<h3 class="w3-text-secondary w3-margin-top">Teilnehmer*innen</h3>

<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th class="w3-center">#</th>
            <th class="w3-left-align">Teilnehmer/in</th>
            <th class="w3-left-align">Team</th>
            <th class="w3-center w3-hide-small">Einträge</th>
            <th class="w3-right-align">Kilometer</th>
        </tr>
        <?php 
            if (empty($alle_spielerliste)) {
                echo "<tr>";
                echo "<td colspan='5' class='w3-center'>Bisher keine Einträge vorhanden.</td>";
                echo "</tr>";
            } else {            
                foreach ($alle_spielerliste as $spieler){?> 
                    <tr style="<?=$color[$spieler["platz"]] ?? ''?>">
                        <td class="w3-center"><?=$spieler["platz"]?></td>
                        <td class="w3-left-align"><?=$spieler['vorname']?></td>
                        <td style="white-space: nowrap;"  class="w3-left-align"><?=$spieler['teamname']?></td>
                        <td class="w3-center w3-hide-small"><?=$spieler['einträge']?></td>
                        <td class="w3-right-align"><?=number_format($spieler["kilometer"], 1, ',', '.');?></td>
                    </tr>
            <?php } //end foreach 
            } //end if
        ?>
    </table>
</div>

<?php include '../../templates/footer.tmp.php';