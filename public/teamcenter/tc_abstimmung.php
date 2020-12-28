<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/abstimmung.logic.php';


/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Abstimmung</h1>


<!-- VOR DER ABSTIMMUNG -->
<?php if ($uhrzeit < $beginn) { ?>
    <p>Die Abstimmung startet in Kürze</p>
<!-- WÄHREND DER ABSTIMMUNG -->
<?php } elseif ($uhrzeit < $abschluss) { ?>
    <?php if ($stimme_check) {?>
        <form class="" method="post">
            <input type="radio" name="abstimmung" value="sommerpause" class="w3-radio">
            <label for="sommerpause">Sommerpause</label><br>
            <input type="radio" name="abstimmung" value="winterpause" class="w3-radio">
            <label for="winterpause">Winterpause</label><br>
            <button style='cursor: pointer; border: 0px;' class="w3-btn w3-secondary"><i class="material-icons">how_to_vote</i> Stimme abgeben!</button>
        </form>
    <?php } else {?>
        <p>Das Team hat bereits abgestimmt</p>
    <?php } ?>
<!-- NACH DER ABSTIMMUNG -->
<?php } elseif ($uhrzeit > $abschluss) { ?>
    <p>Die Abstimmung ist beendet</p>
<?php } ?>






<?php
include '../../templates/footer.tmp.php';