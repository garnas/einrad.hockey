<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$fortschritt = round(100*(Config::time_offset()-strtotime(Saison::get_saison_anfang()))/(strtotime(Saison::get_saison_ende())-strtotime(Saison::get_saison_anfang())),0);
$tage = round((strtotime(Saison::get_saison_anfang()) - Config::time_offset())/(24*60*60),0);

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

$statistik['max_tore'] = Neuigkeit::get_statistik_tore();

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
            <a href='ueber_uns.php' class="no">
                <h1 class="w3-text-primary"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">help_outline</i> Interesse</h1>
            </a>
            <p class="w3-text-grey w3-small w3-border-top w3-border-grey"></p>
            <p>Die Einradhockeyliga steht jedem Einradhockeybegeisterten offen!</p>
            <p><?=Form::link("ueber_uns.php", "Mehr Infos")?></p> 
        </div>

        <!-- Kilometer Challenge -->
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
            <a href='challenge.php' class="no">
                <h1 class="w3-text-primary"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">outlined_flag</i> km-Challenge</h1>
            </a>
            <p class="w3-text-grey w3-small w3-border-top w3-border-grey"></p>
            <p>Sammelt Kilometer für euer Team und legt die längste Strecke zurück.</p>
            <!-- Countdown -->
            <a href='challenge.php' class='no'><?=Form::countdown('2020-12-20')?></a>
            <p>
                <a href='../teamcenter/tc_challenge.php' class="w3-button w3-primary">Kilometer eintragen!</a>
            </p>
        </div>

        <!-- Anstehende Turniere -->
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
            <a href='turniere.php' class="no">
                <h2 class="w3-text-primary"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">event</i> Turniere</h2>
            </a>
            <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <?php $i=0; foreach ($next_turniere as $turnier){  $i++;?>
                <p>
                    <?=date("d.m", strtotime($turnier['datum']))?> 
                    <?=Form::link('turnier_details.php?turnier_id=' . $turnier['turnier_id'], $turnier['tname'] .' '. $turnier['ort'])?>
                    <i>(<?=$turnier['tblock']?>)</i>
                </p>
                <?php if ($i > 4){ break; }?>
            <?php } //end foreach?>
        </div>
        
        <!-- Ergebnisse -->
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
            <a href='ergebnisse.php' class="no">
                <h2 class="w3-text-primary"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">sports_hockey</i> Ergebnisse</h2>
            </a>
            <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <?php if (empty($last_turniere)){?><p class="w3-text-grey">Es liegen keine Ergebnisse vor</p><?php } //end if?>
            <?php $i=0; foreach ($last_turniere as $turnier){ $i++;?>
                <p>
                    <?=date("d.m", strtotime($turnier['datum']))?> 
                    <?=Form::link('ergebnisse.php#' . $turnier['turnier_id'], $turnier['tname'] .' '. $turnier['ort'])?>
                    <i>(<?=$turnier['tblock']?>)</i>
                </p>
                <?php if ($i > 4){ break; }?>
            <?php } //end foreach?>
        </div>
        
        <!-- Statistik -->
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
        <h2 class="w3-text-primary"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">insert_chart_outlined</i> Statistik</h2>
        <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <p><?=Form::link("ergebnisse.php", ($anz_last_turniere ?: '0') . " Turniere")?> gespielt</p>
            <p><?=Form::link("turniere.php", ($anz_next_turniere ?: '0') . " Turniere")?> ausstehend</p>
            <!--
            <p>Die meisten Tore: <br> Nicht implementiert</p>
            -->
            <?php if (!empty($statistik['max_turniere'])) {?>
                <span>Die meisten gespielten Turniere:</span>
                <table class="w3-table">
                    <?php foreach ($statistik['max_turniere'] as $team){?>
                        <tr class="<?=$team['color']?>">
                            <td><?=$team['teamname']?></td>
                            <td><?=$team['gespielt']?></td>
                        </tr>
                    <?php } //end foreach?>
                </table>
            <?php }else{?> 
                <p class="w3-text-grey w3-center">Keine gespielten Turniere</p> 
            <?php } //end if?>
             <?php if (!empty($statistik['max_tore'])) {?>
                <span>Die meisten Tore:</span>
                <p><i>Um das Abschießen von Teams zu verhindern, werden auf Anweisung des Ligaausschusses keine Torstatistiken mehr angezeigt.</i></p>
                <?php /*
                <table class="w3-table">
                    <?php $i=-1; foreach ($statistik['max_tore'] as $team_id => $tore){ $i++;?>
                        <tr class="<?=$colors[$i]?>">
                            <td><?=Team::teamid_to_teamname($team_id)?></td>
                            <td><?=$tore?></td>
                        </tr>
                    <?php } //end foreach?>
                </table>
                */?>
            <?php }//endif?>
            <!--
            <div class="w3-border">
                <div class="w3-primary w3-center" style="height:24px;width:<?=max($fortschritt,0)?>%"></div>
            </div>
            <?php if ($fortschritt < 0){?>
                <span>Die Saison fängt in <?=$tage?> Tagen an</span>
            <?php }else{?>
                <span>Saison zu <?=$fortschritt?>% abgeschlossen</span>
            <?php } //end if?>
            -->
        </div>

        <!-- Links -->
        <div class="w3-panel w3-card w3-light-grey w3-border-primary w3-leftbar w3-responsive">
            <h2 class="w3-text-primary"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">public</i> Links</h2>
            <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <p><?=Form::link(Config::LINK_SWISS, "Schweizer Einradhockeyliga")?></p>
            <p><?=Form::link(Config::LINK_AUSTRALIA, "Australische Einradhockeyliga")?></p>
            <p><?=Form::link(Config::LINK_FRANCE, "Französische Einradbasketballliga")?></p>
            <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <p><?=Form::link(Config::LINK_EV, "Einradverband Deutschland")?></p>
            <p><?=Form::link(Config::LINK_EV_SH, "Einradverband Schleswig-Holstein")?></p>
            <p><?=Form::link(Config::LINK_EV_BY, "Einradverband Bayern")?></p>
            <p class="w3-text-grey w3-border-top w3-border-grey"></p>
            <p><?=Form::link(Config::LINK_IUF, "International Unicycle Federation")?></p>
        </div>

    </div>

    <!-- Neuigkeiten-Einträge -->
    <div class="w3-col l8 m7">
        <?php foreach($neuigkeiten as $neuigkeit){ //Schleife für jede Neuigkeit?>
            <div class='w3-card w3-panel w3-responsive'>
                
                <!-- Überschrift -->
                <h2 class="w3-text-primary"><?=$neuigkeit['titel']?></h2>
                
                <!-- Autor -->
                <p class="w3-text-grey w3-border-top w3-border-grey"><i style="font-size: 22px; vertical-align: -26%" class='material-icons'>create</i> <?=($neuigkeit['eingetragen_von'])?></p>
                
                <!-- Bild -->
                <?php if ($neuigkeit['link_jpg'] != ''){?>
                    <div class='w3-center w3-card'> 
                        <a href='<?=$neuigkeit['bild_verlinken'] ?: $neuigkeit['link_jpg']?>'>
                            <img class='w3-image w3-hover-opacity' alt="<?=$neuigkeit['titel']?>" src=<?=$neuigkeit['link_jpg']?>>
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
                        <p class=""><i class='w3-xxlarge material-icons'>insert_drive_file</i> Download</p>
                    </a>
                <?php } //end if?>

                <!-- Zeitstempel -->
                <p class='w3-text-grey w3-border-bottom w3-border-grey' style="text-align: right;"><i style="font-size: 22px; vertical-align: -26%" class='material-icons'>schedule</i> <?=$neuigkeit['zeit']?></p>

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