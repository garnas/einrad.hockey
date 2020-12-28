<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/abstimmung.logic.php';


/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Abstimmungsergebnisse</h1>

<p>Start: <?= date("d.m.Y", $beginn)?> <?=date("H:i", $beginn)?> Uhr</p>
<p>Ende: <?=date("d.m.Y", $abschluss)?> <?=date("H:i", $abschluss)?> Uhr</p>

<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th>Antwort</th>    
            <th class="w3-right-align">Stimmen</th>
        </tr>
    <?php foreach($tabelle as $zeile => $wert) { ?>
        <tr>
            <td><?=$zeile?></td>
            <td class="w3-right-align"><?=$wert?></td>
        </tr>
    <?php } ?>
    </table>
</div>

<?php include '../../templates/footer.tmp.php';