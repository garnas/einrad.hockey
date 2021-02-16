<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$saison = (isset($_GET['saison']) && is_numeric($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;
$turnier_ergebnisse = Tabelle::get_all_ergebnisse($saison);

if (empty($turnier_ergebnisse)) {
    Form::info("Es wurden noch keine Turnierergebnisse der Saison " . Form::get_saison_string($saison) . " eingetragen");
}
$turniere = Turnier::get_turniere('ergebnis', true, false);

//Farbe für die Plätze auf dem Turnier
$color[0] = "w3-text-tertiary";
$color[1] = "w3-text-grey";
$color[2] = "w3-text-brown";

//Turnierreport Icon
$icon = (isset($_SESSION['team_id'])) ? 'article' : 'lock';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$titel = "Turnierergebnisse " . Form::get_saison_string($saison) . " | Deutsche Einradhockeyliga";
Config::$content = 'Hier kann man die Ergebnisse und Tabellen der Saison ' . Form::get_saison_string($saison) . ' sehen.';
include '../../templates/header.tmp.php'; ?>

    <!--Javascript für Suchfunktion-->
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

    <!--Überschrift-->
    <h1 class="w3-text-primary">
        <?= Form::icon("emoji_events", tag: "h1") ?> Turnierergebnisse
        <br>
        <span class="w3-text-grey">
            Saison <?= Form::get_saison_string($saison) ?>
        </span>
    </h1>

    <!-- Ergebnis suchen -->
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <label for="myInput" class="w3-left"><?= Form::icon("search", 70) ?></label>
        <input id="myInput"
               class='w3-padding w3-border-0'
               style="width: 225px; display: inline-block;"
               type="text"
               placeholder="Ergebnis suchen"
        >
    </div>

    <!--Turnierergebnisse-->
    <div id="myDIV">
        <?php foreach ($turnier_ergebnisse as $turnier_id => $ergebnisse) { ?>
            <section class="w3-section" id="<?= $turnier_id ?>">
                <h3>
                    <?= date("d.m.Y", strtotime($turniere[$turnier_id]['datum'])) ?>
                    <span class="w3-text-primary"><?= $turniere[$turnier_id]['ort'] ?></span>
                    <i>(<?= $turniere[$turnier_id]['tblock'] ?>)</i>
                    <br>
                    <span class="<?= ($turniere[$turnier_id]['art'] == 'final') ? "w3-text-secondary" : "w3-text-grey" ?>">
                        <?= $turniere[$turnier_id]['tname'] ?? '' ?>
                    </span>
                </h3>
                <div class="w3-responsive w3-card-4">
                    <table class="w3-table w3-centered w3-striped w3-leftbar w3-border-tertiary">
                        <tr class="w3-primary">
                            <th>
                                <?= Form::icon("bar_chart") ?>
                                <br>Platz
                            </th>
                            <th>
                                <?= Form::icon("group") ?>
                                <br>Team
                            </th>
                            <th class="w3-center">
                                <?= Form::icon("emoji_events") ?>
                                <br>Punkte
                            </th>
                        </tr>
                        <?php foreach ($ergebnisse as $key => $ergebnis) { ?>
                            <tr class="<?= $color[$key] ?? '' ?>">
                                <td><?= $ergebnis['platz'] ?></td>
                                <td style="white-space: nowrap"><?= $ergebnis['teamname'] ?></td>
                                <td><?= $ergebnis['ergebnis'] ?: '-' ?></td>
                            </tr>
                        <?php } //end foreach?>
                    </table>
                </div>
                <?php if (in_array('Nein', array_column($ergebnisse, 'ligateam'))) { ?>
                    <span class="w3-text-grey w3-small">* Nichtligateam</span>
                <?php } //endif?>
                <p>
                    <?php if ($saison <= 25) { ?>
                        <?= Form::link('archiv.php', '<i class="material-icons">info</i> Details') ?>
                    <?php } else { ?>
                        <span>
                        <?= Form::link($turniere[$turnier_id]['link_spielplan'] ?: ('spielplan.php?turnier_id=' . $turnier_id), '<i class="material-icons">info</i> Spielergebnisse') ?>
                    </span>
                        <?= Form::link("../teamcenter/tc_turnier_report.php?turnier_id=$turnier_id", ' <i class="material-icons">' . $icon . '</i> Turnierreport') ?>
                        <?php if (isset($_SESSION['logins']['la'])) { ?>
                            <?= Form::link("../ligacenter/lc_turnier_report.php?turnier_id=$turnier_id", '<i class="material-icons">article</i> Turnierreport (Ligaausschuss)') ?>
                            <?= Form::link("../ligacenter/lc_spielplan.php?turnier_id=$turnier_id", '<i class="material-icons">info</i> Spielergebnisse verwalten (Ligaausschuss)') ?>
                        <?php }//endif?>
                    <?php }//end if?>
                </p>
            </section>
        <?php } //end foreach?>
    </div>

<?php include '../../templates/footer.tmp.php';