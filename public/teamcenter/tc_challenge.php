<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php';

$team_id = $_SESSION["team_id"];
$kader = Spieler::get_teamkader($team_id);

$platz = "-";
$kilometer = "-";

foreach($teamliste as $team) { 
    if ($team["team_id"] == $team_id) {
        $platz = "#" . strval($team["platz"]);
        $kilometer = strval(number_format($team["kilometer"], 1, ',', '.')) . " km";
    }
}

$challenge = new Challenge;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">km-Challenge</h1>
<!-- <h2 class="w3-text-grey">Eintragen von Ergebnissen</h2> -->

<div class="w3-panel w3-card-4">
    <p class="w3-text-primary w3-half w3-center w3-xxxlarge">
        <?=$platz?>
    </p>
    <p class="w3-text-primary w3-half w3-center w3-xxxlarge">
        <?=$kilometer?>
    </p>
    <p class="w3-text-grey w3-right w3-small"><i>Platz / Kilometer</i></p>
</div>

<form method="post" onsubmit="return confirm('Für ' + document.getElementById('spieler').options[document.getElementById('spieler').selectedIndex].text + ' werden ' + document.getElementById('kilometer').value + ' km hinzugefügt.\r\n\r\nDer Vorname des Spielers wird mit seinen insgesamt gefahrenen Kilometern und seiner Teamzugehörigkeit veröffentlicht.');">
    <div class="w3-panel w3-card-4">
        <p>
            <label class="w3-text-primary" for="spieler">Spieler/in</label>
            <select required class="w3-select w3-border w3-border-primary" id="spieler" name="spieler">
                <option value="0">--- Bitte wählen ---</option>
                <?php foreach($kader as $spieler){?>
                    <option value=<?=$spieler["spieler_id"]?>><?=$spieler["vorname"]?> <?=$spieler["nachname"]?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="kilometer">Strecke</label>
            <input required class="w3-input w3-border w3-border-primary" type="number" id="kilometer" name="kilometer" min="0" step="0.1"></input>
            <i class="w3-text-grey">Bitte gebt die Strecke in Kilometer auf eine Nachkommastelle genau an.</i>
        </p>
        <p>
            <label class="w3-text-primary" for="datum">Datum</label>
            <input required class="w3-input w3-border w3-border-primary" type="date" id="datum" min="<?=date("Y-m-d",strtotime($challenge->challenge_start))?>" max="<?=date("Y-m-d",strtotime($challenge->challenge_end))?>" value="<?=date("Y-m-d")?>" name="datum"></input>
            <i class="w3-text-grey">Das Datum muss zwischen dem <?=$challenge->challenge_start?> und <?=$challenge->challenge_end?> liegen.</i>
        </p>
            <input type="submit" name="put_challenge" value="Eintragen!" class="w3-secondary w3-button w3-block">
        </p>
    </div>
</form>

<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th class="w3-center">Platzierung</th>
            <th colspan="2" class="w3-center">Spieler/in</th>
            <th class="w3-center">Einträge</th>
            <th class="w3-center">Kilometer</th>
        <tr>
        <?php 
            $error = True;
            foreach ($spielerliste as $spieler) {
                if ($spieler["team_id"] == $team_id) {
                    $error = False;
        ?> 
                    <tr>
                        <td class="w3-center"><?=$spieler["platz"]?></td>
                        <td class="w3-center"><?=$spieler['vorname']?></td>
                        <td class="w3-center"><?=$spieler['nachname']?></td>
                        <td class="w3-center"><?=$spieler['einträge']?></td>
                        <td class="w3-right-align"><?=number_format($spieler['kilometer'], 1, ',', '.');?></td>
                    </tr>
        <?php 
                } //end if
            } //end foreach 

            if ($error) {
                echo "<tr>";
                echo "<td colspan='5' class='w3-center'>Bisher keine Einträge vorhanden.</td>";
                echo "<tr>";
            }
        ?>
    </table>
</div>

<p class="w3-text-grey">Schreibe <?=Form::mailto(Config::TECHNIKMAIL)?> an, um eine genaue Auflistung aller Fahrten deines Teams zu erhalten.</p>

<?php
include '../../templates/footer.tmp.php';