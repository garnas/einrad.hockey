<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

//Turnierdaten für Select
$turniere = Turnier::get_turniere('alle', false, false);
$strafen = Team::get_strafen();

//Formularauswertung
foreach ($strafen as $strafe){
    if(isset($_POST['delete' . $strafe['strafe_id']])){
        Team::unset_strafe((int) $strafe['strafe_id']);
        Form::info("Strafe wurde gelöscht.");
        header ("Location: lc_teamstrafe.php");
        die();
    }
}

if (isset($_POST['strafe_eintragen'])){
    $error = false;
    if (empty($_POST['teamname']) or empty($_POST['grund'])){
        $error = true;
        Form::error("Bitte Team auswählen und Begründung eintragen.");
    }
    if ($_POST['verwarnung'] == 'Ja' && !empty($_POST['prozent'])){
        $error = true;
        Form::error("Prozentstrafen sind bei Vewarnungen nicht möglich.");
    }
    $team_id = Team::name_to_id($_POST['teamname']);
    if (!Team::is_ligateam($team_id)){
        $error = true;
        Form::error("Teamname gehört zu keinem Ligateam");
    }
    if (!$error){
        Team::set_strafe($team_id,
            $_POST['verwarnung'] ?? 'Nein',
            (int) $_POST['turnier'] ?? 0,
            $_POST['grund'],
            (int) $_POST['prozent'] ?? 0);
        Form::info("Strafe wurde eingetragen.");
        header ("Location: ../liga/tabelle.php#pranger");
        die();
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<!-- Vergangene Strafen -->
<h2 class="w3-bottombar w3-border-primary">Vergebene Strafen/Verwarnungen der Saison <?=Form::get_saison_string()?></h2>
<div class="w3-responsive">
    <table class="w3-table w3-striped">
        <thead>
            <tr class="w3-primary">
                <th>ID</th>
                <th>Verwarnung</th>
                <th>Team</th>
                <th>Grund</th>
                <th>Turnier</th>
                <th></th>
            </tr>
        </thead>
            <?php foreach ($strafen as $strafe){?>
                <tr>
                    <td style="vertical-align: middle"><?=$strafe['strafe_id']?></td>
                    <td style="vertical-align: middle"><?=$strafe['verwarnung']?></td>
                    <td style="white-space: nowrap; vertical-align: middle;"><?=$strafe['teamname']?></td>
                    <td style="vertical-align: middle">
                        <?=$strafe['grund']?>
                        <?php if (!empty($strafe['prozentsatz'])){?>(<?=$strafe['prozentsatz']?>&nbsp;%)<?php } //endif?>
                    </td>
                    <td style="vertical-align: middle"><?=($strafe['datum'] ?? '') . ' ' . ($strafe['ort'] ?? '')?></td>
                    <td  style="vertical-align: middle">
                        <form method="POST" onsubmit="return confirm('Soll die Strafe/Verwarnung für das Team <?=$strafe['teamname']?> wirklich gelöscht werden?')">
                            <input type="hidden" name="delete<?=$strafe['strafe_id']?>" value='delete'>
                            <input class="w3-button w3-text-primary" type="submit" name="delete<?=$strafe['strafe_id']?>" value="Löschen">
                        </form>
                    </td>
                </tr>
            <?php } //end foreach?>
    </table>
</div>

<!-- Neue Strafe -->
<h2 class="w3-bottombar w3-border-primary">Teamstrafe / -verwarnung eintragen</h2>
<form method="POST">
    <p>
        <input class="w3-check" type="checkbox" value="Ja" name="verwarnung" id="verwarnung" checked="checked">
        <label style="cursor: pointer" class="w3-text-primary w3-hover-text-secondary" for="verwarnung">Es handelt sich um eine Verwarnung</label>
    </p>
    <p>
        <label class="w3-text-primary" for="teamname">Team</label>
        <input required type="text" class="w3-input w3-border w3-border-primary" placeholder="Team eingeben" list="teams" id="teamname" name="teamname">
            <?=Form::datalist_teams();?>
    </p>
    <p>
        <label class="w3-text-primary" for="turnier">Turnier (optional)</label>
        <select placeholder="Optional" class="w3-select w3-border w3-border-primary" name="turnier" id="turnier">
            <option value="" selected></option>
            <?php foreach ($turniere as $turnier_id => $turnier){?>
                <option value="<?=$turnier_id?>"><?=$turnier['datum'] .' '.$turnier['ort']. ' (' . $turnier['tblock'] . ')'?></option>
            <?php } //end foreach?>
        </select>
    <p>
    <p>
        <label class="w3-text-primary" for="teamname">Grund</label>
        <input required class="w3-input w3-border w3-border-primary" type="text" name="grund" id="grund">
    <p>
    <p>
        <label class="w3-text-primary" for="prozent">Prozentstrafe in % (ganze Zahlen | leer lassen, wenn keine Prozentstrafe)</label>
        <input class="w3-input w3-border w3-border-primary" type="number" step="1" min="1" max="100" name="prozent" id="prozent">
    <p>
    <p>
        <input class="w3-button w3-tertiary" type="submit" name="strafe_eintragen" value="Strafe/Verwarnung eintragen">
    <p>
</form>

<?php include '../../templates/footer.tmp.php';