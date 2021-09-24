<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$test_team = new Team(30);
$test_team->set_wertigkeit(5);
db::debug($test_team);

$turnier_id = (int) @$_GET['turnier_id'];
$turnier = nTurnier::get($turnier_id);
$details = array();

if (empty($turnier->get_turnier_id())){
    Helper::not_found("Das Turnier konnte nicht gefunden werden.");
}

$spielenliste = $turnier->get_spielenliste();
$warteliste = $turnier->get_warteliste(); 
$meldeliste = $turnier->get_meldeliste();

$details['turnier_id'] = $turnier->get_turnier_id();
$details['ort'] = $turnier->get_ort();
$details['tname'] = $turnier->get_tname();
$details['tblock'] = $turnier->get_tblock();
$details['strasse'] = $turnier->get_strasse();
$details['plz'] = $turnier->get_plz();
$details['ort'] = $turnier->get_ort();
$details['hallenname'] = $turnier->get_hallenname();
$details['haltestellen'] = $turnier->get_haltestellen();
$details['teamname'] = Team::id_to_name($turnier->get_ausrichter());
$details['organisator'] = $turnier->get_organisator();
$details['handy'] = $turnier->get_handy();
$details['spielplan_link'] = $turnier->get_spielplan_link();
$details['hinweis'] = $turnier->get_hinweis();
$details['plaetze'] = $turnier->get_plaetze();
$details['spieltag'] = $turnier->get_spieltag();
$details['startgebuehr'] = $turnier->get_startgebuehr();

// Email-Adressen hinzufügen
$kontakt = new Kontakt($turnier->get_ausrichter());
$details['email'] = implode(',', $kontakt->get_emails('public'));

// Loszeit
if(in_array($turnier->get_art(), Config::TURNIER_ARTEN)){
    $details['loszeit'] = strftime("%A, %d.%m.%Y %H:%M&nbsp;Uhr", Ligabot::time_offen_melde($turnier->get_datum())-1);
}

// Datum
$details['datum'] = strftime("%d.%m.%Y&nbsp;(%A)", strtotime($turnier->get_datum()));

// Startzeit
$details['startzeit'] = substr($turnier->get_startzeit(), 0, -3);

//Turnierbesprechung
if($turnier->get_besprechung() == 'Ja'){
    $details['besprechung'] = 'Alle Teams sollen sich um ' . date('H:i', strtotime($turnier->get_startzeit()) - 15*60) . '&nbsp;Uhr zu einer gemeinsamen Turnierbesprechung einfinden.';
}else{
    $details['besprechung'] = '';
}

// Turnierart
switch ($turnier->get_art())
{
    case 'spass':
        $details['art'] = 'Spaßturnier';
        $details['tblock'] = '--';
        break;
    case 'I':
        $details['art'] = 'I: Blockeigenes Turnier (Der Turnierblock wandert mit Ausrichterblock)';
        break;
    case 'II':
        $details['art'] = 'II: Blockhöheres Turnier (Der Turnierblock wandert nur höherwertig mit Ausrichterblock)';
        break;
    case 'III':
        $details['art'] = 'III: Blockfreies Turnier';
        break;
    case 'final':
        $details['art'] = 'Finalturnier';
        $details['tblock'] = '--';
        $details['tname'] = '';
        break;
    case 'fixed':
        $details['art'] = 'Manuell';
        break;
}

// Turnierphase
switch ($turnier->get_phase()) 
{
    case 'offen': 
        $details['phase'] = 'Offene Phase'; 
        break;
    case 'melde': 
        $details['phase'] = 'Meldephase'; 
        break;
    case 'spielplan': 
        $details['phase'] = 'Spielplanphase'; 
        break;
    case 'ergebnis': 
        $details['phase'] = 'Ergebnisphase'; 
        break;
    default: 
        $details = '--';
}

// Spielformat
switch ($turnier->get_format())
{
    case 'jgj':
        $details['format'] = 'Jeder-gegen-Jeden';
        break;
    case 'dko':
        $details['format'] = 'Doppel-KO bei acht Teams, sonst Jeder-gegen-Jeden';
        break;
    case 'gruppen':
        $details['format'] = 'Zwei Gruppen bei acht Teams, sonst Jeder-gegen-Jeden';
        break;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = $details['tname'] ?: $details['ort'] ." | Deutsche Einradhockeyliga";
Html::$content = "Alle wichtigen Turnierdetails werden hier angezeigt.";
include '../../templates/header.tmp.php';
?>

<!-- Überschrift -->
<h1 class="w3-text-primary">
    <span class="w3-text-grey"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">info</i> Turnierinfos:</span>
    <br><?=$details['tname']?> <?=$details['ort']?> (<?=$details['tblock']?>), <?=$details['datum']?>
</h1>

<!-- Anzeigen der allgemeinen Infos -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Allgemeine Infos</p>  
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr style="white-space: nowrap;">
            <td class="w3-primary" style="vertical-align: middle; width: 100px;"><i class="material-icons">map</i> Adresse</td>
            <td>
                <?=$details['hallenname']?><br>
                <?=$details['strasse']?><br>
                <?=$details['plz'].' '.$details['ort']?><br>
                <?=Html::link(
                        str_replace(' '
                            , '%20'
                            , 'https://www.google.de/maps/search/' . $details['hallenname']
                                . "+" . $details['strasse']
                                . "+" . $details['plz']
                                . "+" . $details['ort']
                                . '/'),
                        'Google Maps',
                        true,
                        'launch')?>
                <?php if (!empty($details['haltestellen'])){?><p style="white-space: normal;"><i>Haltestellen: <?=$details['haltestellen']?></i></p> <?php } // endif?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">schedule</i> Beginn</td>
            <td>
                <?=$details['startzeit']?>&nbsp;Uhr
                <?php if (!empty($details['besprechung'])){?><p><i><?=$details['besprechung']?></i></p><?php }//endif?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">mail</i> Kontakt</td>
            <td>
                <p>
                    <i>Ausrichter:</i>
                    <br>
                    <?=Html::mailto($details['email'], $details['teamname']) ?: $details['teamname']?>
                </p> 
                <p><i>Organisator:</i><br><?=$details['organisator']?></p>
                <p><i>Handy:</i><br><?=Html::link('tel:' . str_replace(' ', '', $details['handy']), "<i class='material-icons'>smartphone</i>" . $details['handy'])?></p>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">payments</i> Startgebühr</td>
            <td><?=$details['startgebuehr']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">format_align_center</i> Spielplan</td>
            <td>
                <?= $details['format'] ?>
                <?php if($details['phase'] == 'Spielplanphase'){?>
                    <br><?=Html::link($details['spielplan_link'], 'Zum Spielplan', true, "reorder")?>
                <?php }//end if?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">announcement</i> Hinweis</td>
            <td><?=nl2br($details['hinweis'])?></td>
        </tr>
    </table>
</div>

<!--Anmeldungen / Listen -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Spielen-Liste</p> 
<p>
    <?php if (!empty($spielenliste)): ?>
        <i>
            <?php foreach ($spielenliste as $team): ?>
                <?=$team->details['teamname']?> <span class="w3-text-primary">(<?=$team->get_tblock() ?: 'NL'?>)</span><br>
            <?php endforeach; ?>
        </i>
    <?php else: ?>
        <i>leer</i>
    <?php endif;?> 
</p>

<?php if($details['phase'] == 'Offene Phase' or $details['art'] == 'Finalturnier'): ?>
    <p class="w3-text-grey w3-border-bottom w3-border-grey">Meldeliste</p> 
    <p>
        <?php if (!empty($meldeliste)): ?>
            <i>
                <?php foreach ($meldeliste as $team): ?>
                    <?=$team->details['teamname']?> <span class="w3-text-primary">(<?=$team->details['tblock'] ?: 'NL'?>)</span><br>
                <?php endforeach; ?>
            </i>
        <?php else: ?>
            <i>leer</i>
        <?php endif; ?>
    </p>
<?php else: ?>
    <p class="w3-text-grey w3-border-bottom w3-border-grey">Warteliste</p> 
    <p>
        <?php if (!empty($warteliste)):?>
            <i>
                <?php foreach ($warteliste as $team): ?>
                    <?=$team->get_warteliste_postition() . ". " . $team->details['teamname']?> <span class="w3-text-primary">(<?=$team->details['tblock'] ?? 'NL'?>)</span><br>
                <?php endforeach; ?>
            </i>
        <?php else: ?>
            <i>leer</i>
        <?php endif; ?> 
    </p>
    <p>Freie Plätze: <?=$details['plaetze'] - count(($liste['spiele'] ?? array()))?> von <?=$details['plaetze']?></p>
<?php endif; ?>

<?php if ($details['art'] == 'Spaßturnier'):?>
    <p class="w3-text-green">Anmeldung erfolgt beim Ausrichter
<?php endif; ?>

<!-- Anzeigen der Ligaspezifischen Infos -->
<p class="w3-text-grey w3-margin-top w3-border-bottom w3-border-grey">Ligaspezifische Infos</p> 
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr>
            <td class="w3-primary" style="vertical-align: middle; width: 20px;">Turnier-ID</td>
            <td><?=$details['turnier_id']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Phase</td>
            <td><?=$details['phase']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Losung</td>
            <td><?=$details['loszeit'] ?? '--'?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Spieltag</td>
            <td><?=$details['spieltag'] ?: '--'?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Art</td>
            <td><?=$details['art']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Turnierblock</td>
            <td><?=$details['tblock']?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Plätze</td>
            <td><?=$details['plaetze']?></td>
        </tr>
    </table>
</div>

<!-- Weiterführende Links -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Links</p>
<p><?=Html::link('../liga/turniere.php#' . $details['turnier_id'], '<i class="material-icons">event</i> Anstehende Turniere')?></p>
<?php if($details['phase'] == 'Spielplanphase'){?>
    <p><?=Html::link($details['spielplan_link'], 'Zum Spielplan', true, "reorder")?></p>
<?php }//end if?>

<?php if (isset($_SESSION['logins']['team'])){?>
    <p><?=Html::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $details['turnier_id'], '<i class="material-icons">how_to_reg</i> Zum Turnier anmelden')?></p>
    <p><?=Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $details['turnier_id'], '<i class="material-icons">article</i> Zum Turnierreport')?></p>
<?php }else{ ?>
    <p><?=Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $details['turnier_id'], '<i class="material-icons">lock</i> Zum Turnierreport')?></p>
<?php } //endif?>

<?php if ($turnier->is_ausrichter($_SESSION['logins']['team']['id'] ?? 0)){?>
    <p><?=Html::link('../teamcenter/tc_turnier_bearbeiten.php?turnier_id=' . $details['turnier_id'], '<i class="material-icons">create</i> Turnier als Ausrichter bearbeiten')?></p>
<?php } //endif?>

<?php if (isset($_SESSION['logins']['la'])){?>
    <p><?=Html::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $details['turnier_id'], 'Turnier bearbeiten (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_team_anmelden.php?turnier_id=' . $details['turnier_id'], 'Teams anmelden (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $details['turnier_id'], 'Turnierlog einsehen (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $details['turnier_id'], '<i class="material-icons">article</i> Zum Turnierreport (Ligaausschuss)')?></p>
<?php } //endif?>

<?php include '../../templates/footer.tmp.php';