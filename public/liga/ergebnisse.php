<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';

$saison = (isset($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;
$turniere = TurnierRepository::getErgebnisTurniere(saison: $saison);

if (empty($turniere)) {
    Html::info("Es wurden keine Turnierergebnisse der Saison " . Html::get_saison_string($saison) . " eingetragen");
}
//Farbe für die Plätze auf dem Turnier
$color[0] = "w3-text-tertiary";
$color[1] = "w3-text-grey";
$color[2] = "w3-text-brown";

//Turnierreport Icon
$icon = (isset($_SESSION['logins']['team'])) ? 'article' : 'lock';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Turnierergebnisse " . Html::get_saison_string($saison) . " | Deutsche Einradhockeyliga";
Html::$content = 'Hier kann man die Ergebnisse und Tabellen der Saison ' . Html::get_saison_string($saison) . ' sehen.';
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
        <?= Html::icon("emoji_events", tag: "h1") ?> Turnierergebnisse
        <br>
        <span class="w3-text-grey">
            Saison <?= Html::get_saison_string($saison) ?>
        </span>
    </h1>

    <!-- Ergebnis suchen -->
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <label for="myInput" class="w3-left"><?= Html::icon("search", 70) ?></label>
        <input id="myInput"
               class='w3-padding w3-border-0'
               style="width: 225px; display: inline-block;"
               type="text"
               placeholder="Ergebnis suchen"
        >
    </div>

    <!--Turnierergebnisse-->
    <div id="myDIV">
        <?php foreach ($turniere as $turnier_id => $turnier) { ?>
            <section class="w3-section" id="<?= $turnier_id ?>">
                <h3>
                    <?= TurnierSnippets::datumOrtBlock($turnier) ?>
                    <br>
                    <span class="<?= $turnier->isFinalTurnier() ? "w3-text-secondary" : "w3-text-grey" ?>">
                        <?= $turnier->getName() ?? '' ?>
                    </span>
                </h3>
                <div class="w3-responsive w3-card-4">
                    <table class="w3-table w3-centered w3-striped w3-leftbar w3-border-tertiary">
                        <tr class="w3-primary">
                            <th>
                                <?= Html::icon("bar_chart") ?>
                                <br>Platz
                            </th>
                            <th>
                                <?= Html::icon("group") ?>
                                <br>Team
                            </th>
                            <th class="w3-center">
                                <?= Html::icon("emoji_events") ?>
                                <br>Ergebnis
                            </th>
                        </tr>
                        <?php foreach ($turnier->getErgebnis() as $key => $ergebnis): ?>
                            <tr class="<?= $color[$key] ?? '' ?>">
                                <td><?= $ergebnis->getPlatz() ?></td>
                                <td style="white-space: nowrap"><?= $ergebnis->getTeam()->getName() ?></td>
                                <td><?= $ergebnis->getErgebnis() ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?= TurnierService::hasNlTeamErgebnis($turnier)
                    ? "<span class='w3-text-grey w3-small'>* Nichtligateam</span>"
                    : "" ?>
                <p>
                    <?php if ($saison <= 25) { ?>
                        <?= Html::link('archiv.php', 'Details', icon:'info') ?>
                    <?php } else { ?>
                        <span>
                        <?= Html::link(TurnierLinks::spielplan($turnier), 'Spielergebnisse', icon:'info') ?>
                        </span>
                        <?= Html::link("../teamcenter/tc_turnier_report.php?turnier_id=" . $turnier->id(), 'Turnierreport', icon:$icon) ?>
                        <?php if (isset($_SESSION['logins']['la'])) { ?>
                            <?= Html::link("../ligacenter/lc_turnier_report.php?turnier_id=" . $turnier->id(), 'Turnierreport (Ligaausschuss)', icon:'article') ?>
                            <?= Html::link("../ligacenter/lc_spielplan.php?turnier_id=" . $turnier->id(), 'Spielergebnisse verwalten (Ligaausschuss)', icon:'info') ?>
                        <?php }//endif?>
                    <?php }//end if?>
                </p>
            </section>
        <?php } //end foreach?>
    </div>

<?php include '../../templates/footer.tmp.php';