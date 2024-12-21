<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$max_turniere = Stats::get_turniere_team();
$max_gew = Stats::get_gew_spiele_team();
$max_ausrichter = Stats::get_max_ausrichter();
$max_turnierorte = Stats::get_max_turnierorte();

$gespielte_turniere = count(nTurnier::get_turniere_ergebnis($asc = false));
$anstehende_turniere = count(nTurnier::get_turniere_kommend());
$spiele = Stats::get_spiele_anzahl();
$tore = Stats::get_tore_anzahl();

// Erstellen des Strings für die Spielzeit
$zeit = Stats::get_spielminuten_anzahl();
$tage_min = ($zeit - ($zeit % 1440));
$tage =  $tage_min / 1440;
$stunden_min = $zeit - $tage_min - ($zeit % 60);
$stunden = $stunden_min / 60;
$minuten = $zeit - $tage_min - $stunden_min;

$string_zeit = ($tage > 0 ? $tage . "d " : "") . ($stunden > 0 ? $stunden . "h " : "") . ($minuten > 0 ? $minuten . "min" : "");

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Statistik | Deutsche Einradhockeyliga";
Html::$content = "Zahlen zur aktuellen Saison";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<h1 class="w3-text-primary">Statistik der Saison <?= Html::get_saison_string() ?></h1>

<!-- Kacheln für allgemeine Auswertungen -->
<div class="w3-row-padding w3-stretch">
    <!-- gespielte Spiele -->
    <div class="w3-quarter">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?= number_format($spiele, 0, ",", ".") ?></p>
            <p class="w3-center">Spiele</p>
        </div>
    </div>
    <!-- geschossene Tore -->
    <div class="w3-quarter">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?= number_format($tore, 0, ",", ".") ?></p>
            <p class="w3-center">Tore</p>
        </div>
    </div>
    <!-- Spielminuten -->
    <div class="w3-half">
        <div class="w3-panel w3-primary w3-card-4">
            <p class="w3-center w3-xxlarge"><?= $string_zeit ?></p>
            <p class="w3-center">Spielzeit</p>
        </div>
    </div>
</div>

<div class="w3-row-padding">
    <div class="w3-col l6">
        <h3 class="w3-text-secondary">Turnierspieler</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>Anzahl der gespielten Turniere pro Team</span>
        </p>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped">
                <thead class="w3-primary">
                    <tr>
                        <th class="w3-right-align"><b>#</b></th>
                        <th><b>Team</b></th>
                        <th class="w3-right-align"><b>&sum;</b></th>
                    </tr>
                </thead>
                <?php foreach ($max_turniere as $team) : ?>
                    <tr>
                        <td class="w3-right-align"><?= $team['platz'] ?></td>
                        <td><?= $team['teamname'] ?></td>
                        <td class="w3-right-align"><?= $team['gespielt'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="w3-col l6">
        <h3 class="w3-text-secondary">Spielsieger</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>Anzahl der Siege pro Team</span>
        </p>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped">
                <thead class="w3-primary">
                    <tr>
                        <th class="w3-right-align"><b>#</b></th>
                        <th><b>Team</b></th>
                        <th class="w3-right-align"><b>&sum;</b></th>
                    </tr>
                </thead>
                <?php foreach ($max_gew as $team) : ?>
                    <tr>
                        <td class="w3-right-align"><?= $team['platz'] ?></td>
                        <td><?= $team['teamname'] ?></td>
                        <td class="w3-right-align"><?= $team['siege'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<div class="w3-row-padding">
    <div class="w3-col l6">
        <h3 class="w3-text-secondary">Ausrichter</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>Anzahl der ausgerichteten Turniere nach Team</span>
        </p>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped">
                <thead class="w3-primary">
                    <tr>
                        <th class="w3-right-align"><b>#</b></th>
                        <th><b>Team</b></th>
                        <th class="w3-right-align"><b>&sum;</b></th>
                    </tr>
                </thead>
                <?php foreach ($max_ausrichter as $team) : ?>
                    <tr>
                        <td class="w3-right-align"><?= $team['platz'] ?></td>
                        <td><?= $team['teamname'] ?></td>
                        <td class="w3-right-align"><?= $team['anzahl'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="w3-col l6">
        <h3 class="w3-text-secondary">Turnierorte</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>Anzahl der ausgerichteten Turniere nach Ort</span>
        </p>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped">
                <thead class="w3-primary">
                    <tr>
                        <th class="w3-right-align"><b>#</b></th>
                        <th><b>Team</b></th>
                        <th class="w3-right-align"><b>&sum;</b></th>
                    </tr>
                </thead>
                <?php foreach ($max_turnierorte as $ort) : ?>
                    <tr>
                        <td class="w3-right-align"><?= $ort['platz'] ?></td>
                        <td><?= $ort['ort'] ?></td>
                        <td class="w3-right-align"><?= $ort['anzahl'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<?php
include Env::BASE_PATH . '/templates/footer.tmp.php';
