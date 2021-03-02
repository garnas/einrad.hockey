<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// Da die Challenge im Moment nicht stattfindet, wird man auf die Hauptseite weitergeleitet
header('Location: tc_start.php');
die();

require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php';

$team_id = $_SESSION["team_id"];
$kader = Spieler::get_teamkader($team_id);

// Feststellung des aktuellen Teamplatzes
$platz = "-";
$kilometer = "-";

foreach($teamliste as $team) { 
    if ($team["team_id"] == $team_id) {
        $platz = "#" . strval($team["platz"]);
        $kilometer = strval(number_format($team["kilometer"], 1, ',', '.')) . " km";
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">km-Challenge</h1>
<div class="w3-row-padding w3-stretch">
    <div class="w3-twothird">
        <p>
            <a href='../teamcenter/tc_challenge_team_urkunde.php?team_id=<?=$team_id?>' target="_blank" class="w3-button w3-secondary w3-block w3-card-2"><i class="material-icons">description</i> Teamurkunde herunterladen</a>
        </p>
        <div class="w3-panel w3-responsive w3-card-4" style="padding:0;">
            <table class="w3-table w3-striped">
                <tr class="w3-primary">
                    <th class="w3-center">#</th>
                    <th class="w3-center">Teilnehmer/in</th>
                    <th class="w3-center">Kilometer</th>
                    <th class="w3-center">Urkunde</th>
                </tr>
                <?php 
                    $error = True;
                    foreach ($team_spielerliste as $spieler) {
                            $error = False;
                ?> 
                            <tr>
                                <td class="w3-center"><?=$spieler["platz"]?></td>
                                <td class="w3-center"><?=$spieler['vorname']?></td>
                                <td class="w3-center"><?=number_format($spieler['kilometer'], 1, ',', '.');?></td>
                                <td class="w3-center">
                                    <a href='../teamcenter/tc_challenge_spieler_urkunde.php?spieler_id=<?=$spieler["spieler_id"]?>' target="_blank" class="no w3-hover-text-secondary"><i class="material-icons">description</i></a>
                                </td>
                            </tr>
                <?php 
                    } //end foreach 

                    if ($error) {
                        echo "<tr>";
                        echo "<td colspan='5' class='w3-center'>Bisher keine Einträge vorhanden.</td>";
                        echo "</tr>";
                    }
                ?>
            </table>
        </div>
    </div>
    <div class="w3-third">  
        <div class="w3-panel w3-card-4 w3-primary">
            <p class="w3-center">
                Gesamtplatzierung
            </p>
            <p class="w3-center w3-xxxlarge">
                <?=$platz?>
            </p>
        </div> 
        <div class="w3-panel w3-card-4 w3-primary">
            <p class="w3-center">
                Teamkilometer
            </p>
            <p class="w3-center w3-xxxlarge">
                <?=$kilometer?>
            </p>
        </div>
    </div>
</div>
    
<?php Html::message('notice',
    'Gibt es Probleme beim Eintrag? Dann schickt uns eine Mail an <br>'
    . Html::mailto(Env::TECHNIKMAIL) . ' oder '
    . Html::mailto(Env::Env), '', false) ?>

<?php
include '../../templates/footer.tmp.php';