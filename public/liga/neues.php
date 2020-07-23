<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$fortschritt = round(100*(Config::time_offset()-strtotime(Config::SAISON_ANFANG))/(strtotime(Config::SAISON_ENDE)-strtotime(Config::SAISON_ANFANG)),0);
$tage = round((strtotime(Config::SAISON_ANFANG) - Config::time_offset())/(24*60*60),0);

$neuigkeiten = Neuigkeit::get_neuigkeiten(); //Alle Neuigkeiten werden übergeben, da kein Argument überliefert
                                            //Es werden die 10 letzten Neuigkeiten angzeigt
//db::debug($neuigkeiten);
$turniere = Turnier::get_all_turniere("WHERE SAISON = '" . Config::SAISON . "'");

$statistik = Neuigkeit::get_statistik();
//Zuordnen der Farben für 1. 2. 3. Platz
$colors = array("w3-text-tertiary", "w3-text-grey", "w3-text-brown");
$i = 0; foreach ($statistik['max_turniere'] as $key => $team){
    $statistik['max_turniere'][$key]["color"] = $colors[$i] ?? '';
    $i++;
}

//Die nächsten und letzten fünf Turniere:
$last_turniere = $next_turniere = array();
foreach ($turniere as $turnier){
    if ($turnier['phase'] != 'ergebnis'){  
        array_push($next_turniere, $turnier);
    }else{
        array_push($last_turniere, $turnier);
    }
}
$last_turniere = array_reverse($last_turniere);

//Anzahl gespielte Turniere
$anz_last_turniere = count($last_turniere);
//Anzahl ausstehender Turniere
$anz_next_turniere = count($next_turniere);

//Zeitanzeige der Neuigkeiteneinträge verschönern
foreach ($neuigkeiten as $neuigkeiten_id => $neuigkeit){
    $zeit_differenz = (Config::time_offset() - strtotime($neuigkeiten[$neuigkeiten_id]['zeit']))/(60*60); //in Stunden
    if ($zeit_differenz < 24){
        if ($zeit_differenz <= 1.5){
            $zeit = "gerade eben";
        }else{
            $zeit = "vor " . round($zeit_differenz, 0) ." Stunden";
        }
    }elseif ($zeit_differenz < 7*24){
        if ($zeit_differenz <= 1.5*24){
            $zeit = "vor einem Tag";
        }else{
            $zeit = "vor " . round($zeit_differenz/24, 0) ." Tagen";
        }
    }else{
        $zeit = date("d.m.Y", strtotime($neuigkeiten[$neuigkeiten_id]['zeit']));
    }
    $neuigkeiten[$neuigkeiten_id]['zeit'] = $zeit;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Neuigkeiten | Deutsche Einradhockeyliga";
$content = "Hier findet man die Neuigkeiteneinträge des Ligaausschusses und der Teams der Deutschen Einradhockeyliga.";
include '../../templates/header.tmp.php';
?>

<!-- Links (u. a zum Ein- und Ausblenden der Infobar bei Mobils) -->
<a class="w3-tiny w3-hide-large w3-hide-medium w3-button w3-text-blue" href="ueber_uns.php">Erfahre mehr über uns</a>
<button id="einblenden" class="w3-right w3-tiny w3-hide-large w3-hide-medium w3-button w3-text-blue" onclick="einblenden()">Infobar anzeigen</button>
<button id="ausblenden" class="w3-right w3-tiny w3-hide w3-hide-large w3-hide-medium w3-button w3-text-blue" onclick="ausblenden()">Infobar ausblenden</button>

<!-- Responsive Container -->
<div class="w3-row-padding w3-stretch">
    <!-- Infobar -->
    <div class="w3-col l4 m5 w3-hide-small" id="infobar">
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
            <h1 class="w3-text-primary">Interesse?</h1>
            <p class="w3-text-grey w3-small w3-border-top w3-border-grey"></p>
            <p>Die Einradhockeyliga steht jedem Einradhockeybegeisterten offen!</p>
            <p>
            <a href="ueber_uns.php" class="no w3-text-blue w3-hover-text-secondary">Mehr Infos</a></p> 
        </div>

        <!-- Anstehende Turniere -->
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
            <h1 class="w3-text-primary">Turniere</h1>
            <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <?php $i=0; foreach ($next_turniere as $turnier){  $i++;?>
                <p>
                    <?=date("d.m", strtotime($turnier['datum']))?> 
                    <a class="no w3-text-primary w3-hover-text-secondary" href='turnier_details.php?turnier_id=<?=$turnier['turnier_id']?>'>
                        <?=$turnier['tname'] .' '. $turnier['ort']?>
                    </a> 
                    <i>(<?=$turnier['tblock']?>)</i>
                </p>
                <?php if ($i > 4){ break; }?>
            <?php } //end foreach?>
        </div>
        
        <!-- Ergebnisse -->
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
            <h1 class="w3-text-primary">Ergebnisse</h1>
            <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <?php if (empty($last_turniere)){?><p class="w3-text-grey">Es liegen keine Ergebnisse vor</p><?php } //end if?>
            <?php $i=0; foreach ($last_turniere as $turnier){ $i++;?>
                <p>
                    <?=date("d.m", strtotime($turnier['datum']))?> 
                    <a class="no w3-text-primary w3-hover-text-secondary" href='ergebnisse.php#<?=$turnier['turnier_id']?>'>
                        <?=$turnier['tname'] .' '. $turnier['ort']?>
                    </a> 
                    <i>(<?=$turnier['tblock']?>)</i>
                </p>
                <?php if ($i > 4){ break; }?>
            <?php } //end foreach?>
        </div>
        
        <!-- Statistik -->
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
        <h1 class="w3-text-primary">Statistik</h1>
        <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <p><a class="w3-text-blue no w3-hover-text-secondary" href="ergebnisse.php"><?=$anz_last_turniere ?: '0'?> Turniere</a> gespielt</p>
            <p><a class="w3-text-blue no w3-hover-text-secondary" href="turniere.php"><?=$anz_next_turniere ?: '0'?> Turniere</a> ausstehend</p>
            <!--
            <p>Die meisten Tore: <br> Nicht implementiert</p>
            -->
            <?php if (!empty($statistik['max_turniere'])) {?>
                <span>Die meisten gespielte Turniere:
                <table class="w3-table">
                    <?php foreach ($statistik['max_turniere'] as $team){?>
                        <tr class="<?=$team['color']?>">
                            <td><?=$team['gespielt']?></td>
                            <td><?=$team['teamname']?></td>
                        </tr>
                    <?php } //end foreach?>
                </table>
            <?php }else{?> <p class="w3-text-grey w3-center">Keine gespielten Turniere</p> <?php } //end if?>
            </span>
            <div class="w3-border">
                <div class="w3-primary w3-center" style="height:24px;width:<?=max($fortschritt,0)?>%"></div>
            </div>
            <?php if ($fortschritt < 0){?>
                <span>Die Saison fängt in <?=$tage?> Tagen an</span>
            <?php }else{?>
                <span>Saison zu <?=$fortschritt?>% abgeschlossen</span>
            <?php } //end if?>
            <p>
            </p>
        </div>
    </div>

    <!-- Neuigkeiten-Einträge -->
    <div class="w3-col l8 m7">
        <?php foreach($neuigkeiten as $neuigkeit){ //Schleife für jede Neuigkeit?>
            <div class='w3-card w3-panel w3-responsive'>
                <!-- Überschrift -->
                <h1 class="w3-text-primary"><?=$neuigkeit['titel']?></h1>
                <!-- Autor -->
                <p class="w3-text-grey w3-border-top w3-border-grey"><?=($neuigkeit['eingetragen_von'])?></p>
                <!-- Bild -->
                <?php if ($neuigkeit['link_jpg'] != ''){?>
                    <div class='w3-center w3-card'> 
                        <a href='<?=$neuigkeit['link_jpg']?>'>
                            <img class='w3-image w3-hover-opacity' style="max-height: 800px" alt="<?=$neuigkeit['titel']?>" src=<?=$neuigkeit['link_jpg']?>>
                        </a>
                    </div>
                <?php } //end if?>
                    
                <!-- Text -->
                <div class="">
                    <p style="" class=""><?=nl2br($neuigkeit['inhalt']) //nl2br --> new line to <br>?></p> 
                </div>

                <!-- PDF -->
                <?php if ($neuigkeit['link_pdf'] != ''){?>
                    <a class='no w3-hover-text-secondary w3-text-primary' href='<?=$neuigkeit['link_pdf'];?>'>
                        <p class=""><i class='w3-xxlarge material-icons'>insert_drive_file</i>Mehr Infos</p>
                    </a>
                <?php } //end if?>

                <!--Zeitstempel -->
                <p class='w3-text-grey w3-border-bottom w3-border-grey' style="text-align: right;"><?=$neuigkeit['zeit']?></p>

                <!-- Link zum Bearbeiten falls man im Ligacenter oder Teamcenter eingeloggt ist -->
                <?php if (isset($_SESSION['la_id'])) {?>
                    <p>
                        <a class="w3-button w3-hover-primary w3-tertiary" href='../ligacenter/lc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?=$neuigkeit['neuigkeiten_id']?>'>bearbeiten</a>
                    </p>
                <?php }elseif (!empty($_SESSION['teamname']) && $_SESSION['teamname'] == $neuigkeit['eingetragen_von']) {?>
                    <p>
                        <a class="w3-button w3-hover-primary w3-tertiary" href='../teamcenter/tc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?=$neuigkeit['neuigkeiten_id']?>'>bearbeiten</a>
                    </p>
                <?php } //end if?> 
            </div>
        <?php } //end for?>
    </div>
</div>

<?php include '../../templates/footer.tmp.php';

