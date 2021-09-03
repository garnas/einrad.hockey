<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$turnier_id = (int) @$_GET['turnier_id'];
$turnier = new Turnier ($turnier_id);

if (empty($turnier->details)){
    Helper::not_found("Das Turnier konnte nicht gefunden werden.");
}

$kontakt = new Kontakt ($turnier->details['ausrichter']);

// Email-Adressen hinzufügen
$turnier->details['email'] = implode(',', $kontakt->get_emails('public'));

$liste = $turnier->get_anmeldungen(); // Anmeldungen für dieses Turnier Form: $liste['warte'] = Array([0] => Array['teamname','team_id','tblock', etc])

// Parsing
if(in_array($turnier->details['art'],['I','II','III'])){
    $turnier->details['loszeit'] = strftime("%A, %d.%m.%Y %H:%M&nbsp;Uhr", Ligabot::time_offen_melde($turnier->details['datum'])-1);
}

$turnier->details['datum'] = strftime("%d.%m.%Y&nbsp;(%A)", strtotime($turnier->details['datum']));
$turnier->details['startzeit'] = substr($turnier->details['startzeit'], 0, -3);

//Turnierbesprechung
if($turnier->details['besprechung'] == 'Ja'){
    $turnier->details['besprechung'] = 'Alle Teams sollen sich um ' . date('H:i', strtotime($turnier->details['startzeit']) - 15*60) . '&nbsp;Uhr zu einer gemeinsamen Turnierbesprechung einfinden.';
}else{
    $turnier->details['besprechung'] = '';
}

//Turnierart
switch ($turnier->details['art'])
{
    case 'spass':
        $turnier->details['tblock'] = '';
        $turnier->details['art'] = 'Spaßturnier';
        break;
    case 'I':
        $turnier->details['tblock'] = ' (' . $turnier->details['tblock'] . ')';
        $turnier->details['art'] = 'I: Blockeigenes Turnier (Der Turnierblock wandert mit Ausrichterblock)';
        break;
    case 'II':
        $turnier->details['tblock'] = ' (' . $turnier->details['tblock'] . ')';
        $turnier->details['art'] = 'II: Blockhöheres Turnier (Der Turnierblock wandert nur höherwertig mit Ausrichterblock)';
        break;
    case 'III':
        $turnier->details['tblock'] = ' (' . $turnier->details['tblock'] . ')';
        $turnier->details['art'] = 'III: Blockfreies Turnier';
        break;
    case 'final':
        $turnier->details['tblock'] = '';
        $turnier->details['tname'] = '';
        $turnier->details['art'] = 'Finalturnier';
        break;
    case 'fixed':
        $turnier->details['art'] = 'Manuell';
        break;
}

//Turnierphase
switch ($turnier->details['phase'])
{
    case 'offen':
        $turnier->details['phase'] = 'Offene Phase';
        break;
    case 'melde':
        $turnier->details['phase'] = 'Meldephase';
        break;
    case 'ergebnis':
        $turnier->details['phase'] = 'Ergebnisphase';
        break;
    case 'spieplan':
        $turnier->details['phase'] = 'Spielplanphase';
        break;
}

//Spielmodus
if ($turnier->details['format'] == 'jgj'){
    $turnier->details['format'] = 'Jeder-gegen-Jeden';
}elseif($turnier->details['format'] == 'dko'){
    $turnier->details['format'] = 'Doppel-KO bei acht Teams, sonst Jeder-gegen-Jeden';
}elseif($turnier->details['format'] == 'gruppen'){
    $turnier->details['format'] = 'Zwei Gruppen bei acht Teams, sonst Jeder-gegen-Jeden';
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = $turnier->details['tname'] ?: $turnier->details['ort'] ." | Deutsche Einradhockeyliga";
Html::$content = "Alle wichtigen Turnierdetails werden hier angezeigt.";
include '../../templates/header.tmp.php';
?>

<!-- Überschrift -->
<h1 class="w3-text-primary">
    <span class="w3-text-grey"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">info</i> Turnierinfos:</span>
    <br><?=$turnier->details['tname']?> <?=$turnier->details['ort']?><?=$turnier->details['tblock']?>, <?=$turnier->details['datum']?>
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
                <?=Html::link(
                        str_replace(' '
                            , '%20'
                            , 'https://www.google.de/maps/search/' . $turnier->details['hallenname']
                                . "+" . $turnier->details['strasse']
                                . "+" . $turnier->details['plz']
                                . "+" . $turnier->details['ort']
                                . '/'),
                        'Google Maps',
                        true,
                        'launch')?>
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
                    <?=Html::mailto($turnier->details['email'], $turnier->details['teamname'])  ?: $turnier->details['teamname']?>
                </p> 
                <p><i>Organisator:</i><br><?=$turnier->details['organisator']?></p>
                <p><i>Handy:</i><br><?=Html::link('tel:' . str_replace(' ', '', $turnier->details['handy']), "<i class='material-icons'>smartphone</i>" . $turnier->details['handy'])?></p>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">payments</i> Startgebühr</td>
            <td><?=$turnier->details['startgebuehr']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">format_align_center</i> Spielplan</td>
            <td>
                <?= $turnier->details['format'] ?>
                <?php if($turnier->details['phase'] == 'Spielplanphase'){?>
                    <br><?=Html::link($turnier->get_spielplan_link(), 'Zum Spielplan', true, "reorder")?>
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
<?php if($turnier->details['phase'] == 'Offene Phase' or $turnier->details['art'] == 'Finalturnier'){ ?>
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
<p><?=Html::link('../liga/turniere.php#' . $turnier->details['turnier_id'], '<i class="material-icons">event</i> Anstehende Turniere')?></p>
<?php if($turnier->details['phase'] == 'Spielplanphase'){?>
    <p><?=Html::link($turnier->get_spielplan_link(), 'Zum Spielplan', true, "reorder")?></p>
<?php }//end if?>

<?php if (isset($_SESSION['logins']['team'])){?>
    <p><?=Html::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">how_to_reg</i> Zum Turnier anmelden')?></p>
    <p><?=Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">article</i> Zum Turnierreport')?></p>
<?php }else{ ?>
    <p><?=Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">lock</i> Zum Turnierreport')?></p>
<?php } //endif?>

<?php if (($_SESSION['logins']['team']['id'] ?? '') == $turnier->details['ausrichter']){?>
    <p><?=Html::link('../teamcenter/tc_turnier_bearbeiten.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">create</i> Turnier als Ausrichter bearbeiten')?></p>
<?php } //endif?>

<?php if (isset($_SESSION['logins']['la'])){?>
    <p><?=Html::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $turnier->details['turnier_id'], 'Turnier bearbeiten (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_team_anmelden.php?turnier_id=' . $turnier->details['turnier_id'], 'Teams anmelden (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $turnier->details['turnier_id'], 'Turnierlog einsehen (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier->details['turnier_id'], '<i class="material-icons">article</i> Zum Turnierreport (Ligaausschuss)')?></p>
<?php } //endif?>

<?php include '../../templates/footer.tmp.php';