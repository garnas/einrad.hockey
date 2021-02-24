<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$turniere = Turnier::get_turniere('ergebnis', false);

if (empty($turniere)) Form::info("Es wurden noch keine Turniere eingetragen.");

$all_anmeldungen = Turnier::get_all_anmeldungen();

//Turnierdarten parsen
foreach ($turniere as $turnier_id => $turnier) {
    $turniere[$turnier_id]['wochentag'] = strftime("%A", strtotime($turniere[$turnier_id]['datum']));
    $turniere[$turnier_id]['datum'] = strftime("%d.%m.", strtotime($turniere[$turnier_id]['datum']));
    $turniere[$turnier_id]['startzeit'] = substr($turniere[$turnier_id]['startzeit'], 0, -3);

    if ($turniere[$turnier_id]['art'] == 'spass') {
        $turniere[$turnier_id]['tblock'] = 'Spaß';
    }
    if ($turniere[$turnier_id]['besprechung'] == 'Ja') {
        $turniere[$turnier_id]['besprechung'] = 'Gemeinsame Teambesprechung um ' . date('H:i', strtotime($turniere[$turnier_id]['startzeit']) - 15 * 60) . '&nbsp;Uhr';
    } else {
        $turniere[$turnier_id]['besprechung'] = '';
    }
    //Spielmodus
    if ($turniere[$turnier_id]['format'] == 'jgj') {
        $turniere[$turnier_id]['format'] = 'Jeder-gegen-Jeden';
    } elseif ($turniere[$turnier_id]['format'] == 'dko') {
        $turniere[$turnier_id]['format'] = 'Doppel-KO';
    } elseif ($turniere[$turnier_id]['format'] == 'gruppen') {
        $turniere[$turnier_id]['format'] = 'zwei Gruppen';
    }
}

//Parsen der Warteliste und Spieleliste
$warteliste = $spieleliste = $meldeliste = [];
$anz_warteliste = $anz_spieleliste = $anz_meldeliste = [];
foreach ($all_anmeldungen as $turnier_id => $liste) {

    $anz_warteliste[$turnier_id] = count($liste['warte'] ?? []);
    $anz_spieleliste[$turnier_id] = count($liste['spiele'] ?? []);
    $anz_meldeliste[$turnier_id] = count($liste['melde'] ?? []);

    $freie_plaetze = $turniere[$turnier_id]['plaetze'] - $anz_spieleliste[$turnier_id] - $anz_meldeliste[$turnier_id] - $anz_warteliste[$turnier_id];

    //Oben rechts Plätze frei
    if ($freie_plaetze > 0) {
        $turniere[$turnier_id]['plaetze_frei'] = '<span class="w3-text-green">frei</span>';
    } elseif ($freie_plaetze < 0 && $turniere[$turnier_id]['phase'] == 'offen' && $turniere[$turnier_id]['plaetze'] - $anz_spieleliste[$turnier_id] > 0) {
        $turniere[$turnier_id]['plaetze_frei'] = '<span class="w3-text-yellow">losen</span>';
    } elseif (($turniere[$turnier_id]['plaetze'] - $anz_spieleliste[$turnier_id]) <= 0) {
        $turniere[$turnier_id]['plaetze_frei'] = '<span class="w3-text-red">voll</span>';
    }

    if ($turniere[$turnier_id]['art'] == 'final') {
        $turniere[$turnier_id]['phase'] = 'Finale';
    }
    if ($turniere[$turnier_id]['art'] == 'spass') {
        $turniere[$turnier_id]['phase'] = 'Nichtligaturnier';
    }

    if ($turniere[$turnier_id]['phase'] == 'spielplan') {
        $turniere[$turnier_id]['phase'] = Form::link($turniere[$turnier_id]['spielplan_datei'] ?: ('spielplan.php?turnier_id=' . $turnier_id), 'Spielplan', true, 'sports_hockey');
        $turniere[$turnier_id]['phase_spielplan'] = true;
    }

}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$titel = "Turnierliste | Deutsche Einradhockeyliga";
Config::$page_width = "800px";
Config::$content = "Eine Liste aller ausstehenden Spaß-, Final- und Ligaturniere der Deutschen Einradhockeyliga.";
include '../../templates/header.tmp.php';
?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        //Turnierergebnisse filtern
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#myDIV section").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>

    <h1 class="w3-text-primary">Ausstehende Turniere</h1>

    <!-- Turnier suchen -->
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <?= Form::icon("search") ?><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;" type="text" placeholder="Turnier suchen">
    </div>

    <div id="myDIV"><!-- zu durchsuchendes div -->
                    <!--Turnierpanels -->
        <?php foreach ($turniere as $turnier) { ?>
            <section onclick="modal('modal<?= $turnier['turnier_id'] ?>')"
                     class='w3-display-container w3-panel <?php if ($turnier['art'] == 'final') { ?>w3-pale-red<?php } ?> w3-card'
                     style='cursor: pointer'
                     id='<?= $turnier['turnier_id'] ?>'>
                <!-- Angezeigtes Turnierpanel -->
                <div class='w3-panel'>
                    <div class="w3-center">
                        <h4 class=''><?= $turnier['datum'] ?>
                            <span class="w3-text-primary"><?= $turnier['ort'] ?></span> (<?= $turnier['tblock'] ?>)</h4>
                        <p class='w3-text-grey'><?= $turnier['tname'] ?></p>
                    </div>
                    <div style="font-size: 13px;" class="w3-text-grey">
                        <i class='w3-display-topleft w3-padding'><?= $turnier['plaetze_frei'] ?? '<span class="w3-text-green">frei</span>' ?></i>
                        <i class='w3-display-bottomleft w3-padding'><?= $turnier['phase'] ?></i>
                        <i class='w3-display-topright w3-padding'><?= ($anz_spieleliste[$turnier['turnier_id']] ?? 0) . "(" . (($anz_warteliste[$turnier['turnier_id']] ?? 0) + ($anz_meldeliste[$turnier['turnier_id']] ?? 0)) . ")" ?>
                            von <?= $turnier['plaetze'] ?></i>
                        <i class='w3-display-bottomright w3-padding'><?= $turnier['teamname'] ?></i>
                    </div>

                    <!-- Ausklappbarer Content -->
                    <div style='display: none' class='' id="modal<?= $turnier['turnier_id'] ?>">
                        <!-- Listen -->
                        <p class="w3-text-grey w3-border-bottom w3-border-grey">Listen</p>
                        <div class='w3-row'>
                            <div class='w3-half'>
                                <h4 class='w3-text-primary'><span>Spielen-Liste</span></h4>
                                <?php if (!empty($all_anmeldungen[$turnier['turnier_id']]['spiele'])) { ?>
                                    <!-- Ausklappbarer Content -->
                                    <p>
                                        <i>
                                            <?php foreach ($all_anmeldungen[$turnier['turnier_id']]['spiele'] as $team) { ?>
                                                <?= $team['teamname'] ?><span class="w3-text-primary">
                                                (<?= $team['tblock'] ?: 'NL' ?>)</span><br>
                                            <?php }//end foreach?>
                                        </i>
                                    </p>
                                <?php } else { ?>
                                    <i>leer</i> <?php }//end if?>
                            </div>
                            <div class='w3-half'>
                                <?php if ($turnier['phase'] == 'offen' || $turnier['art'] == 'final') { ?>
                                    <?php if (!empty($all_anmeldungen[$turnier['turnier_id']]['melde'])) { ?>
                                        <h4 class='w3-text-primary'><span>Meldeliste</span></h4>
                                        <p>
                                            <i>
                                                <?php foreach (($all_anmeldungen[$turnier['turnier_id']]['melde']) as $team) { ?>
                                                    <?= $team['teamname'] ?>
                                                    <span class="w3-text-primary">(<?= $team['tblock'] ?? 'NL' ?>)</span>
                                                    <br>
                                                <?php }//end foreach?>
                                            </i>
                                        </p>
                                    <?php }//end if?>
                                <?php } else { //else phase?>
                                    <?php if (!empty($all_anmeldungen[$turnier['turnier_id']]['warte'])) { ?>
                                        <h4 class='w3-text-primary'><span>Warteliste</span></h4>
                                        <p>
                                            <i>
                                                <?php foreach (($all_anmeldungen[$turnier['turnier_id']]['warte']) as $team) { ?>
                                                    <?= $team['position_warteliste'] . ". " . $team['teamname'] ?>
                                                    <span class="w3-text-primary">(<?= $team['tblock'] ?? 'NL' ?>)</span>
                                                    <br>
                                                <?php }//end foreach?>
                                            </i>
                                        </p>
                                    <?php }//end if?>
                                <?php } //end if phase?>
                            </div>
                        </div>
                        <?php if ($turnier['art'] == 'spass') { ?>
                            <p class="w3-text-green">Anmeldung erfolgt beim Ausrichter</p>
                        <?php } //end if spass?>

                        <!-- Turnierdetails -->
                        <p class="w3-text-grey w3-border-bottom w3-border-grey">Details</p>
                        <div class="w3-responsive w3-stretch">
                            <table class="w3-table">
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary" style="width: 150px"><?= Form::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">pending</i> Plätze') ?></td>
                                    <td><?= $turnier['plaetze'] ?> (<?= $turnier['format'] ?>)</td>
                                </tr>
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary"><?= Form::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">schedule</i> Beginn') ?></td>
                                    <td><?= $turnier['startzeit'] ?>
                                        &nbsp;Uhr<?php if (!empty($turnier['besprechung'])) { ?>
                                            <i>(<?= $turnier['besprechung'] ?>)</i><?php } //endif?></td>
                                </tr>
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary" style=""><?= Form::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">event</i> Wochentag') ?></td>
                                    <td><?= $turnier['wochentag'] ?></td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; vertical-align: middle;" class="w3-text-primary"><?= Form::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">announcement</i> Hinweis') ?></td>
                                    <td style="white-space: normal"><?= nl2br($turnier['hinweis']) ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Links -->
                        <div style="margin-bottom: 24px;">
                            <p class="w3-text-grey w3-border-bottom w3-border-grey">Links</p>
                            <p><?= Form::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], 'Alle Turnierdetails', icon:'info') ?></p>
                            <?php if ($turnier['phase_spielplan'] ?? false) { ?>
                                <p><?= Form::link($turnier['spielplan_datei'] ?? ('../liga/spielplan.php?turnier_id=' . $turnier['turnier_id']), 'Zum Spielplan', icon:'reorder') ?></p>
                            <?php } //endif?>
                            <?php if (isset($_SESSION['logins']['team'])) { ?>
                                <p><?= Form::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier['turnier_id'], 'Zur Anmeldeseite', icon:'how_to_reg') ?></p>
                                <p><?= Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], 'Zum Turnierreport', icon:'article') ?></p>
                            <?php } else { ?>
                                <p><?= Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], 'Zum Turnierreport', icon:'lock') ?></p>
                            <?php } //endif?>
                            <?php if (($_SESSION['logins']['team']['id'] ?? 0) === $turnier['ausrichter']) { ?>
                                <p><?= Form::link('../teamcenter/tc_turnier_bearbeiten.php?turnier_id=' . $turnier['turnier_id'], 'Turnier als Ausrichter bearbeiten', icon:'create') ?></p>
                            <?php } //endif?>
                            <?php if (isset($_SESSION['logins']['la'])) { ?>
                                <p><?= Form::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $turnier['turnier_id'], 'Turnier bearbeiten (Ligaausschuss)') ?></p>
                                <p><?= Form::link('../ligacenter/lc_team_anmelden.php?turnier_id=' . $turnier['turnier_id'], 'Teams anmelden (Ligaausschuss)') ?></p>
                                <p><?= Form::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $turnier['turnier_id'], 'Turnierlog einsehen (Ligaausschuss)') ?></p>
                                <p><?= Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], 'Zum Turnierreport (Ligaausschuss)', icon:'article') ?></p>
                            <?php } //endif?>
                        </div>
                    </div>
                </div>
            </section>
        <?php } //end foreach?>
    </div>
<?php include '../../templates/footer.tmp.php';







