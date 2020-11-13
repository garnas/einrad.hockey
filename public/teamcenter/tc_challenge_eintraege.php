<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php';

$team_id = $_SESSION["team_id"];

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<div class="w3-responsive w3-panel">
        <?php
            foreach ($eintraege as $eintrag) {
                if ($eintrag["team_id"] == $team_id) {
        ?> 
                    <div class="w3-panel w3-card-4 w3-text-grey w3-padding-16">
                        <div class="w3-quarter w3-center"><?=date("d.m.Y", strtotime($eintrag['datum']))?></div>
                        <div class="w3-quarter w3-center"><?=$eintrag['vorname']?> <?=$eintrag['nachname']?></div>
                        <div class="w3-quarter w3-center"><?=number_format($eintrag['kilometer'], 1, ',', '.');?></div>
                        <div class="w3-quarter w3-center"><i class="material-icons">delete</i></div>
                    </div>
        <?php
                }
            }
        ?>  
</div>

<?php
include '../../templates/footer.tmp.php';