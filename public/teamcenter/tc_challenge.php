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
        <h3>Eintragen</h3>
        <p>
            <label for="spieler">Spieler/in</label>
            <select required id="spieler" name="spieler">
                <?php foreach($kader as $spieler){?>
                    <option value=<?=$spieler["spieler_id"]?>><?=$spieler["vorname"]?> <?=$spieler["nachname"]?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="kilometer">Strecke</label>
            <input required type="number" id="kilometer" name="kilometer" min="0" step="0.1"></input>
        </p>
        <p>
            <label for="datum">Datum</label>
            <input required type="date" id="datum" min="<?php date("Y-m-d") ?>" name="datum"></input>
        </p>
            <input type="submit" name="put_challenge" value="Eintragen!">
        </p>
    </div>
</form>

<?php
include '../../templates/footer.tmp.php';