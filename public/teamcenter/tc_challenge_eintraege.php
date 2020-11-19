<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php';

if (count($team_eintraege) == 0) {
    $eintrag_text = "-";
} else {
    $eintrag_text = count($team_eintraege);
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<style>
button {
    border: 0px;
    cursor: pointer;
    background-color: transparent;
    color: gray;
}

button:hover {
    transition: 0.3s;
    color: #e3776f;
}
</style>

<div class="w3-col">
    <div class="w3-row-padding w3-twothird">
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
                <label class="w3-text-primary" for="radgroesse">Radgröße</label>
                <input required class="w3-input w3-border w3-border-primary" type="number" id="radgroesse" name="radgroesse" min="0" step="0.5"></input>
                <i class="w3-text-grey">Angabe in Zoll.</i>
            </p>
            <p>
                <label class="w3-text-primary" for="datum">Datum</label>
                <input required class="w3-input w3-border w3-border-primary" type="date" id="datum" min="<?=date("Y-m-d",strtotime($challenge->challenge_start))?>" max="<?=$max?>" value="<?=date("Y-m-d")?>" name="datum"></input>
                <i class="w3-text-grey">Das Datum muss zwischen dem <?=$challenge->challenge_start?> und <?=$challenge->challenge_end?> liegen.</i>
            </p>
                <input type="submit" name="put_challenge" value="Eintragen!" class="w3-secondary w3-button w3-block">
            </p>
        </div>
    </form>
</div>
<div class="w3-row-padding w3-third ">
    <div class="w3-panel w3-card-4 w3-primary">
        <p class="w3-center">
            Teameinträge
        </p>
        <p class="w3-center w3-xxxlarge">
            <?=$eintrag_text?>
        </p>
    </div>
</div>
<div class="w3-row-padding">
    <?php
        foreach ($team_eintraege as $eintrag) {
            if ($eintrag["team_id"] == $team_id) {
    ?> 
                <div class="w3-panel w3-card-4 w3-text-grey w3-padding-16">
                    <div class="w3-row">
                        <div class="w3-col w3-center" style="width: 23%"><?=date("d.m.Y", strtotime($eintrag['datum']))?></div>
                        <div class="w3-col w3-center" style="width: 23%"><?=$eintrag['vorname']?> <?=$eintrag['nachname']?></div>
                        <div class="w3-col w3-center" style="width: 23%"><?=number_format($eintrag['kilometer'], 1, ',', '.');?> km</div>
                        <div class="w3-col w3-center" style="width: 23%"><?=$eintrag['radgröße']?> Zoll</div>
                        <div class="w3-col w3-right-align" style="width: 8%">
                            <form method="post">
                                <input type="hidden" name="eintrag_id" value="<?=$eintrag['id']?>"></input>
                                <button type="submit" name="update_challenge"><i class="material-icons">delete</i></button>
                            </form>
                        </div>
                    </div>
                </div>
    <?php
            }
        }
    ?>  
</div>

<?php
include '../../templates/footer.tmp.php';