<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php';

$team_id = $_SESSION["team_id"];
$kader = Spieler::get_teamkader($team_id);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Kilometer-Challenge</h1>
<!-- <h2 class="w3-text-grey">Eintragen von Ergebnissen</h2> -->

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
            <i class="w3-text-grey">Das frühstmögliche Datum ist der XX.XX.2020.</i>
        </p>
            <input type="submit" name="put_challenge" value="Eintragen!">
        </p>
    </div>
</form>

<?php
include '../../templates/footer.tmp.php';