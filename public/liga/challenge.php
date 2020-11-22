<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/challenge.logic.php'; //Erstellt Challenge-Objekt nach der Validation

$color[0] = "background-color: #a6b2d8;";
$color[1] = "background-color: #b8c1e0;";
$color[2] = "background-color: #cad0e8;";

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

<!-- Countdown -->
<?=Form::countdown($challenge->challenge_end)?>

<!-- Button zum Teamcenter -->
<p class="w3-text-gray">
    Hier habt ihr die Möglichkeit die geradelten Kilometer einzutragen. Gewertet werden eure Einträge als Einzelperson, aber immer auch im Team.
    Also ran ans Rad und viel Spaß beim Sammeln.
</p>
<a href='../teamcenter/tc_challenge.php' class="w3-button w3-secondary">Kilometer eintragen!</a>

<h3 class="w3-text-secondary w3-margin-top">Ergebnisse</h3>
    
<!-- ProgressBar -->
<div class='w3-col w3-light-grey w3-card-2'>
    <div class='<?=$class[0]?>' style='width:<?=$width_1?>%; padding-right: 10px; border-right: 1px solid black; float: left;'><p class='w3-right-align'><?=$text[0]?></p></div>
    <div class='<?=$class[1]?>' style='width:<?=$width_2?>%; padding-right: 10px; border-right: 1px solid black; float: left;'><p class='w3-right-align'><?=$text[1]?></p></div>
    <div class='<?=$class[2]?>' style='width:<?=$width_3?>%; padding-right: 10px; border-right: 1px solid black; float: left;'><p class='w3-right-align'><?=$text[2]?></p></div>
    <div class='<?=$class[3]?>' style='width:<?=$width_4?>%; padding-right: 10px; border-right: 1px solid black; float: left;'><p class='w3-right-align'><?=$text[3]?></p></div>
    <div class='<?=$class[4]?>' style='width:<?=$width_5?>%; padding-right: 10px; float: left;'><p class='w3-right-align'><?=$text[4]?></p></div>
</div>

<!-- Kacheln für Sonderauswertungen -->
<div class="w3-col">
    <!-- Alter ist 50+ -->
    <div class="w3-row-padding w3-third">
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
    <!-- Alter ist U16 -->
    <div class="w3-row-padding w3-third">
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
    <!-- Einradhockeyrad -->
    <div class="w3-row-padding w3-third">
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
        <tr>
        <?php 
            if (empty($teamliste)) {
                echo "<tr>";
                echo "<td colspan='5' class='w3-center'>Bisher keine Einträge vorhanden.</td>";
                echo "<tr>";
            } else {
                foreach ($teamliste as $team){?> 
                    <tr style="<?=$color[$team["platz"] - 1] ?? ''?>">
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


<h3 class="w3-text-secondary w3-margin-top">Teilnehmer/innen</h3>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th class="w3-center">#</th>
            <th class="w3-left-align">Teilnehmer/in</th>
            <th style="white-space: nowrap;" class="w3-left-align">Team</th>
            <th class="w3-center w3-hide-small">Einträge</th>
            <th class="w3-right-align">Kilometer</th>
        <tr>
        <?php 
            if (empty($alle_spielerliste)) {
                echo "<tr>";
                echo "<td colspan='5' class='w3-center'>Bisher keine Einträge vorhanden.</td>";
                echo "<tr>";
            } else {            
                foreach ($alle_spielerliste as $spieler){?> 
                    <tr style="<?=$color[$spieler["platz"] - 1] ?? ''?>">
                        <td class="w3-center"><?=$spieler["platz"]?></td>
                        <td class="w3-left-align"><?=$spieler['vorname']?></td>
                        <td class="w3-left-align"><?=$spieler['teamname']?></td>
                        <td class="w3-center w3-hide-small"><?=$spieler['einträge']?></td>
                        <td class="w3-right-align"><?=number_format($spieler["kilometer"], 1, ',', '.');?></td>
                    </tr>
            <?php } //end foreach 
            } //end if
        ?>
    </table>
</div>

<?php include '../../templates/footer.tmp.php';