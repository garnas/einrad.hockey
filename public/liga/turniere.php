<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$alle_turniere = nTurnier::get_turniere_kommend();
$finalturniere = nTurnier::get_finalturniere();

//Liste der Finalturniere erstellen
foreach ($finalturniere as $turnier) {
    switch ($turnier->get_tblock())
    {
        case "DFINALE":
            $dfinale['turnier_id'] = $turnier->get_turnier_id();
            $dfinale['ort'] = $turnier->get_ort();
            $dfinale['datum'] = strftime("%d.%m.%Y", strtotime($turnier->get_datum()));
            break;
        case "CFINALE":
            $cfinale['turnier_id'] = $turnier->get_turnier_id();
            $cfinale['ort'] = $turnier->get_ort();
            $cfinale['datum'] = strftime("%d.%m.%Y", strtotime($turnier->get_datum()));
            break;
        case "BFINALE":
            $bfinale['turnier_id'] = $turnier->get_turnier_id();
            $bfinale['ort'] = $turnier->get_ort();
            $bfinale['datum'] = strftime("%d.%m.%Y", strtotime($turnier->get_datum()));
            break;
        case "AFINALE":
            $finale['turnier_id'] = $turnier->get_turnier_id();
            $finale['ort'] = $turnier->get_ort();
            $finale['datum'] = strftime("%d.%m.%Y", strtotime($turnier->get_datum()));
            break;
    }
}

//Turnierdarten parsen
foreach ($alle_turniere as $turnier) {
    $turnier_id = $turnier->get_turnier_id();

    $turniere[$turnier_id]['turnier_id'] = $turnier_id;
    $turniere[$turnier_id]['art'] = $turnier->get_art();
    $turniere[$turnier_id]['plaetze'] = $turnier->get_plaetze();
    $turniere[$turnier_id]['ort'] = $turnier->get_ort();
    $turniere[$turnier_id]['tblock'] = $turnier->get_tblock();
    $turniere[$turnier_id]['tname'] = $turnier->get_tname();
    $turniere[$turnier_id]['teamname'] = Team::id_to_name($turnier->get_ausrichter());
    $turniere[$turnier_id]['phase'] = $turnier->get_phase();
    $turniere[$turnier_id]['hinweis'] = $turnier->get_hinweis();
    $turniere[$turnier_id]['ausrichter'] = $turnier->get_ausrichter();
    $turniere[$turnier_id]['spielen_liste'] = $turnier->get_spielenliste();
    $turniere[$turnier_id]['warte_liste'] = $turnier->get_warteliste();
    $turniere[$turnier_id]['melde_liste'] = $turnier->get_meldeliste();

    // Zeit und Datum
    $turniere[$turnier_id]['wochentag'] = strftime("%A", strtotime($turnier->get_datum()));
    $turniere[$turnier_id]['datum'] = strftime("%d.%m.", strtotime($turnier->get_datum()));
    $turniere[$turnier_id]['startzeit'] = substr($turnier->get_startzeit(), 0, -3);

    // Turnierbesprechung
    if ($turnier->get_besprechung() == 'Ja') {
        $turniere[$turnier_id]['besprechung'] = 'Gemeinsame Teambesprechung um ' . date('H:i', strtotime($turniere[$turnier_id]['startzeit']) - 15 * 60) . '&nbsp;Uhr';
    } else {
        $turniere[$turnier_id]['besprechung'] = '';
    }

    // Spielmodus
    switch ($turnier->get_format()) 
    {
        case 'jgj':
            $turniere[$turnier_id]['format'] = 'Jeder-gegen-Jeden';
            break;
        case 'dko':
            $turniere[$turnier_id]['format'] = 'Doppel-KO';
            break;
        case 'gruppen':
            $turniere[$turnier_id]['format'] = 'zwei Gruppen';
            break;
    }

    // Turnierblock
    switch ($turnier->get_tblock()) 
    {
        case 'AFINALE':
            $turniere[$turnier_id]['tblock'] = '';
            $turniere[$turnier_id]['tname'] = 'Finale der Deutschen Einradhockeyliga';
            break;
        case 'BFINALE':
            $turniere[$turnier_id]['tblock'] = '';
            $turniere[$turnier_id]['tname'] = 'B-Finale der Deutschen Einradhockeyliga';
            break;
        case 'CFINALE':
            $turniere[$turnier_id]['tblock'] = '';
            $turniere[$turnier_id]['tname'] = 'C-Finale der Deutschen Einradhockeyliga';
            break;
        case 'DFINALE':
            $turniere[$turnier_id]['tblock'] = '';
            $turniere[$turnier_id]['tname'] = 'Saisonschlussturnier';
            break;
        default:
            $turniere[$turnier_id]['tblock'] = '(' . $turnier->get_tblock() . ')';
    }

    // Spassturnier
    if ($turnier->get_art() == 'spass') {
        $turniere[$turnier_id]['tblock'] = '';
    }
}

//Parsen der Warteliste und Spieleliste
$warteliste = $spieleliste = $meldeliste = [];
foreach ($alle_turniere as $turnier) {

    $turnier_id = $turnier->get_turnier_id();
    
    //Feststellung der freien Plätze und Anzahl der Mannschaften auf den unterschiedlichen Listen
    $freie_plaetze = $turnier->get_freie_plaetze();

    $anz_spieleliste = $turnier->get_anz_spielenliste();
    $turniere[$turnier_id]['anz_spieleliste'] = $anz_spieleliste;

    $anz_meldeliste = $turnier->get_anz_meldeliste();
    $turniere[$turnier_id]['anz_meldeliste'] = $anz_meldeliste;

    $anz_warteliste = $turnier->get_anz_warteliste();
    $turniere[$turnier_id]['anz_warteliste'] = $anz_warteliste;

    //Oben rechts Plätze frei
    if ($turnier->get_phase() == 'spielplan') {
        $turniere[$turnier_id]['plaetze_frei'] = '<span class="w3-text-gray">geschlossen</span>';
    } elseif ($freie_plaetze > 0) {
        $turniere[$turnier_id]['plaetze_frei'] = '<span class="w3-text-green">frei</span>';
    } elseif ($turnier->get_phase() == 'offen' && $anz_spieleliste + $anz_meldeliste > $turnier->get_plaetze()) {
        $turniere[$turnier_id]['plaetze_frei'] = '<span class="w3-text-yellow">losen</span>';
    } elseif ($turnier->get_plaetze() - $anz_spieleliste <= 0) {
        $turniere[$turnier_id]['plaetze_frei'] = '<span class="w3-text-red">voll</span>';
    }

    //Unten links Phase
    if ($turnier->get_art() == 'final') {
        $turniere[$turnier_id]['phase'] = 'Finale';
    }
    if (
            $turnier->get_art() === 'spass'
            && $turnier->get_phase() !== 'spielplan'
    ) {
        $turniere[$turnier_id]['phase'] = 'Nichtligaturnier';
    }

    if ($turnier->get_phase() == 'spielplan') {
        $turniere[$turnier_id]['phase'] = Html::link($turnier->get_spielplan_datei() ?: ('spielplan.php?turnier_id=' . $turnier_id), 'Spielplan', true);
        $turniere[$turnier_id]['phase_spielplan'] = true;
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Turnierliste | Deutsche Einradhockeyliga";
Html::$page_width = "800px";
Html::$content = "Eine Liste aller ausstehenden Spaß-, Final- und Ligaturniere der Deutschen Einradhockeyliga.";
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

    <?php include '../../templates/finalturniere22.tmp.php'; ?>
    
    <h1 class="w3-text-primary">Turniere der Saison <?= Html::get_saison_string() ?></h1>

    <!-- Turnier suchen -->
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <?= Html::icon("search") ?><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;" type="text" placeholder="Turnier suchen">
    </div>

    <?php 
    if (empty($turniere)):
        Html::message('info', "Keine Turniere gefunden.", NULL);
    endif; 
    ?>

    <!-- zu durchsuchendes div -->
    <div id="myDIV">
        <!--Turnierpanels -->
        <?php foreach ($turniere as $turnier): ?>
            <section onclick="modal('modal<?= $turnier['turnier_id'] ?>')"
                     class='w3-display-container w3-panel w3-card'
                     style='cursor: pointer; <?php if ($turnier['art'] == 'final') { ?>background-color:#edf0f7;<?php } ?>'
                     id='<?= $turnier['turnier_id'] ?>'>
                <!-- Angezeigtes Turnierpanel -->
                <div class='w3-panel'>
                    <div class="w3-center">
                        <?php if ($turnier['art'] != 'final'): ?>
                            <h4 class=''><?= $turnier['datum'] ?>
                                <span class="w3-text-primary"><?= $turnier['ort'] ?></span> <?= $turnier['tblock'] ?></h4>
                            <p class='w3-text-grey'><?= $turnier['tname'] ?></p>
                        <?php else: ?>
                            <h4 class='w3-text-primary'>
                                <?= $turnier['tname'] ?> </h4>
                            <h4 class=''> 
                                <?= $turnier['datum'] ?> <span class="w3-text-primary"><?= $turnier['ort'] ?></span></h4>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 13px;" class="w3-text-grey">
                        <i class='w3-display-topleft w3-padding'><?= $turnier['plaetze_frei'] ?? '<span class="w3-text-green">frei</span>' ?></i>
                        <i class='w3-display-bottomleft w3-padding'><?= $turnier['phase'] ?></i>
                        <i class='w3-display-topright w3-padding'><?= ($turnier['anz_spieleliste'] ?? 0) . "(" . (($turnier['anz_meldeliste'] ?? 0) + ($turnier['anz_warteliste'] ?? 0)) . ")" ?>
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
                                <?php if (!empty($turnier['spielen_liste'])): ?>
                                    <!-- Ausklappbarer Content -->
                                    <p>
                                        <i>
                                            <?php foreach ($turnier['spielen_liste'] as $team): ?>
                                                <?= $team->get_teamname()?><span class="w3-text-primary">
                                                (<?= $team->get_tblock() ?? 'NL' ?>)</span><br>
                                            <?php endforeach; ?>
                                        </i>
                                    </p>
                                <?php else: ?>
                                    <i>leer</i> 
                                <?php endif; ?>
                            </div>
                            <div class='w3-half'>
                                <?php if ($turnier['phase'] == 'offen' || $turnier['art'] == 'final'): ?>
                                    <?php if (!empty($turnier['melde_liste'])): ?>
                                        <h4 class='w3-text-primary'><span>Meldeliste</span></h4>
                                        <p>
                                            <i>
                                                <?php foreach (($turnier['melde_liste']) as $team): ?>
                                                    <?= $team->get_teamname() ?>
                                                    <span class="w3-text-primary">(<?= $team->get_tblock() ?? 'NL' ?>)</span>
                                                    <br>
                                                <?php endforeach; ?>
                                            </i>
                                        </p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if (!empty($turnier['warte_liste'])): ?>
                                        <h4 class='w3-text-primary'><span>Warteliste</span></h4>
                                        <p>
                                            <i>
                                                <?php foreach (($turnier['warte_liste']) as $team): ?>
                                                    <?= $team->get_warteliste_postition() . ". " . $team->get_teamname() ?>
                                                    <span class="w3-text-primary">(<?= $team->get_tblock() ?? 'NL' ?>)</span>
                                                    <br>
                                                <?php endforeach; ?>
                                            </i>
                                        </p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($turnier['art'] == 'spass'): ?>
                            <p class="w3-text-green">Anmeldung erfolgt beim Ausrichter</p>
                        <?php endif; ?>

                        <!-- Turnierdetails -->
                        <p class="w3-text-grey w3-border-bottom w3-border-grey">Details</p>
                        <div class="w3-responsive w3-stretch">
                            <table class="w3-table">
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary" style="width: 150px"><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">pending</i> Plätze') ?></td>
                                    <td><?= $turnier['plaetze'] ?> (<?= $turnier['format'] ?>)</td>
                                </tr>
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary"><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">schedule</i> Beginn') ?></td>
                                    <td><?= $turnier['startzeit'] ?>
                                        &nbsp;Uhr<?php if (!empty($turnier['besprechung'])): ?>
                                            <i>(<?= $turnier['besprechung'] ?>)</i><?php endif; ?></td>
                                </tr>
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary" style=""><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">event</i> Wochentag') ?></td>
                                    <td><?= $turnier['wochentag'] ?></td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; vertical-align: middle;" class="w3-text-primary"><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">announcement</i> Hinweis') ?></td>
                                    <td style="white-space: normal"><?= nl2br($turnier['hinweis']) ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Links -->
                        <div style="margin-bottom: 24px;">
                            <p class="w3-text-grey w3-border-bottom w3-border-grey">Links</p>
                            <p><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier['turnier_id'], 'Alle Turnierdetails', icon:'info') ?></p>
                            <?php if ($turnier['phase_spielplan'] ?? false) { ?>
                                <p><?= Html::link($turnier['spielplan_datei'] ?? ('../liga/spielplan.php?turnier_id=' . $turnier['turnier_id']), 'Zum Spielplan', icon:'reorder') ?></p>
                            <?php } //endif?>
                            <?php if (isset($_SESSION['logins']['team'])) { ?>
                                <p><?= Html::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier['turnier_id'], 'Zur Anmeldeseite', icon:'how_to_reg') ?></p>
                                <p><?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], 'Zum Turnierreport', icon:'article') ?></p>
                            <?php } else { ?>
                                <p><?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], 'Zum Turnierreport', icon:'lock') ?></p>
                            <?php } //endif?>
                            <?php if (($_SESSION['logins']['team']['id'] ?? 0) === $turnier['ausrichter']) { ?>
                                <p><?= Html::link('../teamcenter/tc_turnier_bearbeiten.php?turnier_id=' . $turnier['turnier_id'], 'Turnier als Ausrichter bearbeiten', icon:'create') ?></p>
                            <?php } //endif?>
                            <?php if (isset($_SESSION['logins']['la'])) { ?>
                                <p><?= Html::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $turnier['turnier_id'], 'Turnier bearbeiten (Ligaausschuss)') ?></p>
                                <p><?= Html::link('../ligacenter/lc_team_anmelden.php?turnier_id=' . $turnier['turnier_id'], 'Teams anmelden (Ligaausschuss)') ?></p>
                                <p><?= Html::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $turnier['turnier_id'], 'Turnierlog einsehen (Ligaausschuss)') ?></p>
                                <p><?= Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], 'Zum Turnierreport (Ligaausschuss)', icon:'article') ?></p>
                            <?php } //endif?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
<?php include '../../templates/footer.tmp.php';
