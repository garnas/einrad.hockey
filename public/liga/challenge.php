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

<h1 class="w3-text-primary">km-Challenge</h1>
<h3 class="w3-text-gray">vom <?=$challenge->challenge_start?> bis zum <?=$challenge->challenge_end?></h3>
<p class="w3-text-gray">
    Hier habt ihr die Möglichkeit die geradelten Kilometer einzutragen. Gewertet werden eure Einträge als Einzelperson, aber immer auch im Team.
    Also ran ans Rad und viel Spaß beim Sammeln.
</p>

<a href='../teamcenter/tc_challenge.php' class="w3-button w3-secondary">Kilometer eintragen!</a>

<h3 class="w3-text-secondary w3-margin-top">Teams</h3>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th class="w3-center">Platzierung</th>
            <th class="w3-left-align">Team</th>
            <th class="w3-center">Mitglieder</th>
            <th class="w3-center">Einträge</th>
            <th class="w3-center">Kilometer</th>               
        <tr>
        <?php 
            if ($teamliste == NULL) {
                echo "<tr>";
                echo "<td colspan='5' class='w3-center'>Bisher keine Einträge vorhanden.</td>";
                echo "<tr>";
            } else {
                foreach ($teamliste as $team){?> 
                    <tr style="<?=$color[$team["platz"] - 1] ?? ''?>">
                        <td class="w3-center"><?=$team["platz"]?></td>
                        <td style="white-space: nowrap;" class="w3-left-align"><?=$team["teamname"]?></td>
                        <td class="w3-center"><?=$team["mitglieder"]?></td>
                        <td class="w3-center"><?=$team["einträge"]?></td>
                        <td class="w3-right-align"><?=number_format($team["kilometer"], 1, ',', '.');?></td>
                    </tr>
                <?php } //end foreach 
            } //end if
            ?>
    </table>
</div>


<h3 class="w3-text-secondary w3-margin-top">Spieler/innen</h3>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th class="w3-center">Platzierung</th>
            <th class="w3-left-align">Spieler/in</th>
            <th class="w3-left-align">Team</th>
            <th class="w3-center">Einträge</th>
            <th class="w3-center">Kilometer</th>
        <tr>
        <?php 
            if ($spielerliste == NULL) {
                echo "<tr>";
                echo "<td colspan='5' class='w3-center'>Bisher keine Einträge vorhanden.</td>";
                echo "<tr>";
            } else {            
                foreach ($spielerliste as $spieler){?> 
                    <tr style="<?=$color[$spieler["platz"] - 1] ?? ''?>">
                        <td class="w3-center"><?=$spieler["platz"]?></td>
                        <td class="w3-left-align"><?=$spieler['vorname']?></td>
                        <td class="w3-left-align"><?=$spieler['teamname']?></td>
                        <td class="w3-center"><?=$spieler['einträge']?></td>
                        <td class="w3-right-align"><?=number_format($spieler["kilometer"], 1, ',', '.');?></td>
                    </tr>
            <?php } //end foreach 
            } //end if
        ?>
    </table>
</div>

<?php include '../../templates/footer.tmp.php';