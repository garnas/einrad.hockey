<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$turnier_id = (int) $_GET['turnier_id'] ?? 0;
$turnier = new Turnier ($turnier_id);

if (empty($turnier->details)){
    Form::error("Das Turnier existiert nicht");
    header('Location: turniere.php');
    die();
}

$kontakt = new Kontakt ($turnier->details['ausrichter']);

// Email-Adressen hinzufügen
$turnier->details['email'] = implode(',', $kontakt->get_emails('public'));

$liste = $turnier->get_anmeldungen(); //Anmeldungen für dieses Turnier Form: $liste['warte'] = Array([0] => Array['teamname','team_id','tblock', etc])

// Parsing
if(in_array($turnier->details['art'],['I','II','III'])){
    $turnier->details['loszeit'] = strftime("%A, %d.%m.%Y %H:%M&nbsp;Uhr", Ligabot::time_offen_melde($turnier->details['datum'])-1);
}

$turnier->details['datum'] = strftime("%d.%m.%Y&nbsp;(%A)", strtotime($turnier->details['datum']));
$turnier->details['startzeit'] = substr($turnier->details['startzeit'], 0, -3);

if($turnier->details['besprechung'] == 'Ja'){
    $turnier->details['besprechung'] = 'Alle Teams sollen sich um ' . date('H:i', strtotime($turnier->details['startzeit']) - 15*60) . '&nbsp;Uhr zu einer gemeinsamen Turnierbesprechung einfinden.';
}else{
    $turnier->details['besprechung'] = '';
}

if ($turnier->details['art'] == 'spass'){
    $turnier->details['tblock'] = '--';
    $turnier->details['art'] = 'Spaßturnier';
}
if ($turnier->details['art'] == 'I'){
    $turnier->details['art'] = 'I: Blockeigenes Turnier (Der Turnierblock wandert mit Ausrichterblock)';
}
if ($turnier->details['art'] == 'II'){
    $turnier->details['art'] = 'II: Blockhöheres Turnier (Der Turnierblock wandert nur höherwertig mit Ausrichterblock)';
}
if ($turnier->details['art'] == 'III'){
    $turnier->details['art'] = 'III: Blockfreies Turnier';
}
if ($turnier->details['art'] == 'final'){
    $turnier->details['art'] = 'Abschlussturnier';
}
if ($turnier->details['art'] == 'fixed'){
    $turnier->details['art'] = 'Manuell';
}
if ($turnier->details['phase'] == 'melde'){
    $turnier->details['phase'] = 'Meldephase';
}
if ($turnier->details['phase'] == 'offen'){
    $turnier->details['phase'] = 'Offene Phase';
}
if ($turnier->details['phase'] == 'ergebnis'){
    $turnier->details['phase'] = 'Ergebnisphase';
}
if ($turnier->details['phase'] == 'spielplan'){
    $turnier->details['phase'] = 'Spielplanphase';
}
//Spielmodus
if ($turnier->details['spielplan'] == 'jgj'){
    $turnier->details['spielplan'] = 'Jeder-gegen-Jeden';
}elseif($turnier->details['spielplan'] == 'dko'){
    $turnier->details['spielplan'] = 'Doppel-KO bei acht Teams, sonst Jeder-gegen-Jeden';
}elseif($turnier->details['spielplan'] == 'gruppen'){
    $turnier->details['spielplan'] = 'Zwei Gruppen bei acht Teams, sonst Jeder-gegen-Jeden';
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = $turnier->details['tname'] ?: $turnier->details['ort'] ." | Deutsche Einradhockeyliga";
$content = "Alle wichtigen Turnierdetails werden hier angezeigt.";
include '../../templates/header.tmp.php';
?>

<!-- Überschrift -->
<h1 class="w3-text-primary">
    <span class="w3-text-grey"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">info</i> Turnierinfos:</span>
    <br><?=$turnier->details['tname']?> <?=$turnier->details['ort']?> (<?=$turnier->details['tblock']?>), <?=$turnier->details['datum']?>
</h1>

<!-- Anzeigen der allgemeinen Infos -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Allgemeine Infos</p>  
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr style="white-space: nowrap;">
            <td class="w3-primary" style="vertical-align: middle; width: 100px;"><i class="material-icons">map</i> Adresse</td>
            <td>
                <?=$turnier->details['hallenname']?><br>
                <?=$turnier->details['strasse']?><br>
                <?=$turnier->details['plz'].' '.$turnier->details['ort']?><br>
                <?=Form::link(str_replace(' ', '%20', 'https://www.google.de/maps/search/' . $turnier->details['hallenname'] ."+". $turnier->details['strasse'] ."+" . $turnier->details['plz'] ."+". $turnier->details['ort'] .'/'), 'Google Maps', true);?>
                <?php if (!empty($turnier->details['haltestellen'])){?><p style="white-space: normal;"><i>Haltestellen: <?=$turnier->details['haltestellen']?></i></p> <?php } // endif?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">schedule</i> Beginn</td>
            <td>
                <?=$turnier->details['startzeit']?>&nbsp;Uhr
                <?php if (!empty($turnier->details['besprechung'])){?><p><i><?=$turnier->details['besprechung']?></i></p><?php }//endif?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">mail</i> Kontakt</td>
            <td>
                <p>
                    <i>Ausrichter:</i>
                    <br>
                    <?=Form::mailto($turnier->details['email'], $turnier->details['teamname'])  ?: $turnier->details['teamname']?>
                </p> 
                <p><i>Organisator:</i><br><?=$turnier->details['organisator']?></p>
                <p><i>Handy:</i><br><?=Form::link('tel:' . str_replace(' ', '', $turnier->details['handy']), "<i class='material-icons'>smartphone</i>" . $turnier->details['handy'])?></p>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">payments</i> Startgebühr</td>
            <td><?=$turnier->details['startgebuehr']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">format_align_center</i> Spielplan</td>
            <td>
                <?=$turnier->details['spielplan']?>
                <?php if($turnier->details['phase'] == 'Spielplanphase'){?>
                    <br><?=Form::link($turnier->get_spielplan_link(), 'Zum Spielplan', true, "reorder")?>
                <?php }//end if?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">announcement</i> Hinweis</td>
            <td><?=nl2br($turnier->details['hinweis'])?></td>
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
<?php if($turnier->details['phase'] == 'Offene Phase' or $turnier->details['art'] == 'Abschlussturnier'){ ?>
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
    <p>Freie Plätze: <?=$turnier->details['plaetze'] - count(($liste['spiele'] ?? array()))?> von <?=$turnier->details['plaetze']?></p>
<?php  } //end if phase?>
<?php if ($turnier->details['art'] == 'Spaßturnier'){?>
    <p class="w3-text-green">Anmeldung erfolgt beim Ausrichter
<?php }//end if spass?>

<!-- Anzeigen der Ligaspezifischen Infos -->
<p class="w3-text-grey w3-margin-top w3-border-bottom w3-border-grey">Ligaspezifische Infos</p> 
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr>
            <td class="w3-primary" style="vertical-align: middle; width: 20px;">Turnier-ID</td>
            <td><?=$turnier->details['turnier_id']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Phase</td>
            <td><?=$turnier->details['phase'] ?: '--'?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Losung</td>
            <td><?=$turnier->details['loszeit'] ?? '--'?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Spieltag</td>
            <td><?=$turnier->details['spieltag'] ?: '--'?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Art</td>
            <td><?=$turnier->details['art']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Turnierblock</td>
            <td><?=$turnier->details['tblock']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Plätze</td>
            <td><?=$turnier->details['plaetze']?></td>
        </tr>
    </table>
</div>

<!-- Weiterführende Links -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Links</p>
<p><?=Form::link('../liga/turniere.php#' . $turnier->details['turnier_id'], '<i class="material-icons">event</i> Anstehende Turniere')?></p>
<?php if($turnier->details['phase'] == 'Spielplanphase'){?>
    <p><?=Form::link($turnier->get_spielplan_link(), 'Zum Spielplan', true, "reorder")?></p>
<?php }//end if?>

<?php if (isset($_SESSION['team_id'])){?>
    <p><?=Form::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">how_to_reg</i> Zum Turnier anmelden')?></p>
    <p><?=Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">article</i> Zum Turnierreport')?></p>
<?php }else{ ?>
    <p><?=Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">lock</i> Zum Turnierreport')?></p>
<?php } //endif?>

<?php if (($_SESSION['team_id'] ?? '') == $turnier->details['ausrichter']){?>
    <p><?=Form::link('../teamcenter/tc_turnier_bearbeiten.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">create</i> Turnier als Ausrichter bearbeiten')?></p>
<?php } //endif?>

<?php if (isset($_SESSION['la_id'])){?> 
    <p><?=Form::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $turnier->details['turnier_id'], 'Turnier bearbeiten (Ligaausschuss)')?></p>
    <p><?=Form::link('../ligacenter/lc_team_anmelden.php?turnier_id=' . $turnier->details['turnier_id'], 'Teams anmelden (Ligaausschuss)')?></p>
    <p><?=Form::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $turnier->details['turnier_id'], 'Turnierlog einsehen (Ligaausschuss)')?></p>
    <p><?=Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">article</i> Zum Turnierreport (Ligaausschuss)')?></p>
<?php } //endif?>

<?php include '../../templates/footer.tmp.php';