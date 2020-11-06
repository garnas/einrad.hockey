<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/challenge.logic.php'; //Erstellt Challenge-Objekt nach der Validation


/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Kilometer-Challenge | Deutsche Einradhockeyliga";
$content = 'Hier sind die aktuellen Ergebnisse der Kilometer-Challenge zu sehen.';
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Kilometer-Challenge 2020</h1>
<p class="w3-text-gray">
    Die Kilometer-Challenge findet vom XX.XX.2020 bis XX.XX.2020 statt. 
    Die Teams können in diesem Zeitraum so viele Kilometer wie möglich sammeln und am Ende gewinnen!
</p>

<div class="w3-hide-small">
    <h3 class="w3-text-secondary w3-margin-top">Teams</h3>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th>Platzierung</th>
                <th>Team</th>
                <th>Kilometer</th>
                <th>Mitglieder</th>
                <th>Einträge</th>
            <tr>
            <?php foreach ($teamliste as $index => $team){?> 
                <tr>
                    <td>x</td>
                    <td><?=$team["teamname"]?></td>
                    <td><?=$team["kilometer"]?></td>
                    <td><?=$team["mitglieder"]?></td>
                    <td><?=$team["einträge"]?></td>
                </tr>
            <?php } //end foreach ?>
        </table>
    </div>
</div>

<div class="w3-hide-small">
    <h3 class="w3-text-secondary w3-margin-top">Spieler/innen</h3>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th>Platzierung</th>
                <th>Spieler/in</th>
                <th>Team</th>
                <th>Einträge</th>
                <th>Kilometer</th>
            <tr>
            <?php foreach ($spielerliste as $spieler){?> 
                <tr>
                    <td>x</td>
                    <td><?=$spieler['vorname']?></td>
                    <td><?=$spieler['teamname']?></td>
                    <td><?=$spieler['einträge']?></td>
                    <td><?=$spieler['kilometer']?></td>
                </tr>
            <?php } //end foreach ?>
        </table>
    </div>
</div>

<?php include '../../templates/footer.tmp.php';