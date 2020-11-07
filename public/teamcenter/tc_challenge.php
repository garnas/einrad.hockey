<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php';

$team_id = $_SESSION["team_id"];
$kader = Spieler::get_teamkader($team_id);

foreach($teamliste as $team) { 
    if ($team["team_id"] == $team_id) {
        $platz = $team["platz"];
        $kilometer = $team["kilometer"];
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Kilometer-Challenge</h1>
<!-- <h2 class="w3-text-grey">Eintragen von Ergebnissen</h2> -->

<div class="w3-panel w3-card-4">
    <p class="w3-text-primary w3-half w3-center w3-xxxlarge">
        #<?=$platz ?>
    </p>
    <p class="w3-text-primary w3-half w3-center w3-xxxlarge">
        <?=number_format($kilometer, 1, ',', '.'); ?> km
    </p>
</div>

<form method="post">
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
            <input required class="w3-input w3-border w3-border-primary" type="date" id="datum" min="<?php date("Y-m-d") ?>" name="datum"></input>
            <i class="w3-text-grey">Das Datum muss zwischen dem <?=$start ?> und <?=$end ?> liegen.</i>
        </p>
            <input type="submit" name="put_challenge" value="Eintragen!" class="w3-secondary w3-button w3-block">
        </p>
    </div>
</form>

<div class="w3-hide-small">
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th class="w3-center">Platzierung</th>
                <th colspan="2" class="w3-center">Spieler/in</th>
                <th class="w3-center">Einträge</th>
                <th class="w3-center">Kilometer</th>
            <tr>
            <?php foreach ($spielerliste as $spieler) {
                if ($spieler["team_id"] == $team_id) {
            ?> 
                <tr>
                    <td class="w3-center"><?=$spieler["platz"]?></td>
                    <td class="w3-center"><?=$spieler['vorname']?></td>
                    <td class="w3-center"><?=$spieler['nachname']?></td>
                    <td class="w3-center"><?=$spieler['einträge']?></td>
                    <td class="w3-center"><?=number_format($spieler['kilometer'], 1, ',', '.');?></td>
                </tr>
            <?php 
                } //end if
            } //end foreach 
            ?>
        </table>
    </div>
</div>
<?php
include '../../templates/footer.tmp.php';