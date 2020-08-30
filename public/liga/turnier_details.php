<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$turnier_id=$_GET['turnier_id'];
$akt_turnier = new Turnier ($turnier_id);


$daten = $akt_turnier->daten;
$akt_kontakt = new Kontakt ($daten['ausrichter']);

if (empty($daten)){
    Form::error("Das Turnier existiert nicht");
    header('Location: turniere.php');
    die();
}

$emails = $akt_kontakt->get_emails_public();
$emails_string = '';
foreach ($emails as $email){
    $emails_string .= $email . ',';
}
$daten['email'] = substr($emails_string, 0, -1);

$liste = $akt_turnier->get_anmeldungen(); //Anmeldungen für dieses Turnier Form: $liste['warte'] = Array([0] => Array['teamname','team_id','tblock', etc])

//Parsing
if(in_array($daten['art'],['I','II','III'])){
    $daten['loszeit'] = strftime("%A, %d.%m.%Y %H:%M&nbsp;Uhr", Ligabot::time_offen_melde($daten['datum'])-1);
}

$daten['datum'] = strftime("%d.%m.%Y&nbsp;(%A)", strtotime($daten['datum']));
$daten['startzeit'] = substr($daten['startzeit'], 0, -3);

if($daten['besprechung'] == 'Ja'){
    $daten['besprechung'] = 'Alle Teams sollen sich um ' . date('h:i', strtotime($daten['startzeit']) - 15*60) . '&nbsp;Uhr zu einer gemeinsamen Turnierbesprechung einfinden.';
}else{
    $daten['besprechung'] = '';
}

if ($daten['art'] == 'spass'){
    $daten['tblock'] = '--';
    $daten['art'] = 'Spaßturnier';
}
if ($daten['art'] == 'I'){
    $daten['art'] = 'I: Blockeigenes Turnier (Der Turnierblock wandert mit Ausrichterblock)';
}
if ($daten['art'] == 'II'){
    $daten['art'] = 'II: Blockhöheres Turnier (Der Turnierblock wandert nur höherwertig mit Ausrichterblock)';
}
if ($daten['art'] == 'III'){
    $daten['art'] = 'III: Blockfreies Turnier';
}
if ($daten['art'] == 'final'){
    $daten['art'] = 'Abschlussturnier';
}
if ($daten['art'] == 'fixed'){
    $daten['art'] = 'Manuell';
}
if ($daten['phase'] == 'melde'){
    $daten['phase'] = 'Meldephase';
}
if ($daten['phase'] == 'offen'){
    $daten['phase'] = 'Offene Phase';
}
if ($daten['phase'] == 'ergebnis'){
    $daten['phase'] = 'Ergebnisphase';
}
if ($daten['phase'] == 'spielplan'){
    $daten['phase'] = 'Spielplanphase';
}
//Spielmodus
if ($daten['spielplan'] == 'jgj'){
    $daten['spielplan'] = 'Jeder-gegen-Jeden';
}elseif($daten['spielplan'] == 'dko'){
    $daten['spielplan'] = 'Doppel-KO';
}elseif($daten['spielplan'] == 'gruppen'){
    $daten['spielplan'] = 'zwei Gruppen';
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = $daten['tname'] ?: $daten['ort'] ." | Deutsche Einradhockeyliga";
$content = "Alle wichtigen Turnierdetails werden hier angezeigt.";
include '../../templates/header.tmp.php';
?>

<!-- Überschrift -->
<h1 class="w3-text-primary">
    <span class="w3-text-grey"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">info</i> Turnierinfos:</span>
    <br><?=$daten['tname']?> <?=$daten['ort']?> (<?=$daten['tblock']?>), <?=$daten['datum']?>
</h1>

<!-- Anzeigen der allgemeinen Infos -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Allgemeine Infos</p>  
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr style="white-space: nowrap;">
            <td class="w3-primary" style="vertical-align: middle; max-width: 100px;"><i class="material-icons">map</i> Adresse</td>
            <td>
                <?=$daten['hallenname']?><br>
                <?=$daten['strasse']?><br>
                <?=$daten['plz'].' '.$daten['ort']?><br>
                <?=Form::link(str_replace(' ', '%20', 'https://www.google.de/maps/search/' . $daten['hallenname'] ."+". $daten['strasse'] ."+" . $daten['plz'] ."+". $daten['ort'] .'/'), 'Google Maps', true);?>
                <?php if (!empty($daten['haltestellen'])){?><p style="white-space: normal;"><i>Haltestellen: <?=$daten['haltestellen']?></i></p> <?php } // endif?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">schedule</i> Beginn</td>
            <td>
                <?=$daten['startzeit']?>&nbsp;Uhr
                <?php if (!empty($daten['besprechung'])){?><p><i><?=$daten['besprechung']?></i></p><?php }//endif?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">mail</i> Kontakt</td>
            <td>
                <p>
                    <i>Ausrichter:</i>
                    <br>
                    <?=Form::mailto($daten['email'], $daten['teamname'])?>
                </p> 
                <p><i>Organisator:</i><br><?=$daten['organisator']?></p>
                <p><i>Handy:</i><br><?=Form::link('tel:' . str_replace(' ', '', $daten['handy']), "<i class='material-icons'>smartphone</i>" . $daten['handy'])?></a></p>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">payments</i> Startgebühr</td>
            <td><?=$daten['startgebuehr']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">format_align_center</i> Spielplan</td>
            <td>
                <?=$daten['plaetze'] . ' ' . $daten['spielplan']?>
                <?php if(!empty($daten['link_spielplan'])){?>
                    <br>
                    <?=Form::link($daten['link_spielplan'], 'Download Spielplan')?>
                <?php }else{?>
                    <br>
                    <span class="w3-text-grey">Der Spielplan wird in der Woche vor dem Turnier erstellt</span>
                <?php }//end if?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">announcement</i> Hinweis</td>
            <td><?=nl2br($daten['hinweis'])?></td>
        </tr>
    </table>
</div>
<!--Anmeldungen / Listen -->

<p class="w3-text-grey w3-border-bottom w3-border-grey">Spielen-Liste</p> 
<p><i>
    <?php if (!empty($liste['spiele'])){?>
        <?php foreach ($liste['spiele'] as $team){?>
            <?=$team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?: 'NL'?>)</span><br>
        <?php }//end foreach?>
    <?php }else{?><i>leer</i><?php } //endif?> 
</i></p>
<?php if($daten['phase'] == 'Offene Phase' or $daten['art'] == 'Abschlussturnier'){ ?>
    <p class="w3-text-grey w3-border-bottom w3-border-grey">Meldeliste</p> 
    <p><i>
        <?php if (!empty($liste['melde'])){?>
            <?php foreach ($liste['melde'] as $team){?>
                <?=$team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?: 'NL'?>)</span><br>
            <?php }//end foreach?>
        <?php }else{?><i>leer</i><?php } //endif?>
    </i></p>
<?php }else{//else phase?>
    <p class="w3-text-grey w3-border-bottom w3-border-grey">Warteliste</p> 
    <p><i>
        <?php if (!empty($liste['warte'])){?>
            <?php foreach ($liste['warte'] as $team){?>
                <?=$team['position_warteliste'] . ". " . $team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?? 'NL'?>)</span><br>
            <?php }//end foreach?>
        <?php }else{?><i>leer</i><?php } //endif?> 
    </i></p>
    <p>Freie Plätze: <?=$daten['plaetze'] - count(($liste['spiele'] ?? array()))?> von <?=$daten['plaetze']?></p>
<?php  } //end if phase?>
<?php if ($daten['art'] == 'Spaßturnier'){?>
    <p class="w3-text-green">Anmeldung erfolgt beim Ausrichter
<?php }//end if spass?>

<!-- Anzeigen der Ligaspezifischen Infos -->
<p class="w3-text-grey w3-margin-top w3-border-bottom w3-border-grey">Ligaspezifische Infos</p> 
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr>
            <td class="w3-primary" style="vertical-align: middle; width: 20px;">Turnier-ID</td>
            <td><?=$daten['turnier_id']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Phase</td>
            <td><?=$daten['phase'] ?: '--'?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Losung</td>
            <td><?=$daten['loszeit'] ?? '--'?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Spieltag</td>
            <td><?=$daten['spieltag'] ?: '--'?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Art</td>
            <td><?=$daten['art']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Turnierblock</td>
            <td><?=$daten['tblock']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Plätze</td>
            <td><?=$daten['plaetze']?></td>
        </tr>
    </table>
</div>

<!-- Weiterführende Links -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Links</p>
<p><?=Form::link('../liga/turniere.php#' . $daten['turnier_id'], '<i class="material-icons">reorder</i> Anstehende Turniere')?></p>

<?php if (isset($_SESSION['la_id'])){?> 
    <p><?=Form::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $daten['turnier_id'], 'Turnier bearbeiten (Ligaausschuss)')?></p>
    <p><?=Form::link('../ligacenter/lc_team_anmelden.php?turnier_id=' . $daten['turnier_id'], 'Teams anmelden (Ligaausschuss)')?></p>
    <p><?=Form::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $daten['turnier_id'], 'Turnierlog einsehen (Ligaausschuss)')?></p>
    <p><?=Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $daten['turnier_id'], '<i class="material-icons">article</i> Zum Turnierreport (Ligaausschuss)')?></p>
<?php } //endif?>

<?php if (isset($_SESSION['team_id'])){?>
    <p><?=Form::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $daten['turnier_id'], '<i class="material-icons">how_to_reg</i> Zum Turnier anmelden')?></p>
    <p><?=Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $daten['turnier_id'], '<i class="material-icons">article</i> Zum Turnierreport')?></p>
<?php }else{ ?>
    <p><?=Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $daten['turnier_id'], '<i class="material-icons">lock</i> Zum Turnierreport')?></p>
<?php } //endif?>

<?php if (($_SESSION['team_id'] ?? '') == $daten['ausrichter']){?>
    <p><?=Form::link('../teamcenter/tc_turnier_bearbeiten.php?turnier_id=' . $daten['turnier_id'], '<i class="material-icons">create</i> Turnier als Ausrichter bearbeiten')?></p>
<?php } //endif?>

<?php include '../../templates/footer.tmp.php';