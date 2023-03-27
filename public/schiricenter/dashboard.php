<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$sql = "
            SELECT schiri_test_id,
                   spieler.spieler_id,
                   spieler.vorname,
                   spieler.nachname,
                   spieler.schiri,
                   spieler.junior,
                   teams_liga.teamname,
                   test_level,
                   t_erstellt,
                   t_gestartet,
                   t_abgegeben,
                   bestanden,
                   saison
            FROM schiri_ergebnis
            INNER JOIN spieler ON schiri_ergebnis.spieler_id = spieler.spieler_id
            INNER JOIN teams_liga  on spieler.team_id = teams_liga.team_id
            WHERE saison = ?
            ORDER BY t_erstellt DESC, t_abgegeben DESC 
";

$results = db::$db->query($sql, Config::SAISON)->fetch();

$sql = " 
    SELECT spieler_id, vorname, nachname, teamname, schiri
    From spieler
    INNER JOIN teams_liga  on spieler.team_id = teams_liga.team_id
    WHERE letzte_saison = ?
    ";
$check = db::$db->query($sql, Config::SAISON)->fetch("spieler_id");


$getRowColor = static function ($result) {
    if ($result["schiri"] >= (Config::SAISON + 1)) {
        return "w3-green";
    }
    if ($result["bestanden"] === "Ja") {
        return "w3-light-green";
    }
    return "";
};


//DoctrineWrapper::dump(TurnierRepository::get());
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php'; ?>

    <h1 class="w3-text-primary">Schiri Dashboard</h1>
<?= HTML::link(Env::BASE_URL . "/schiricenter/schiritest_stats.php",
    "Excel-Datei Auswertung",
    "true",
    "launch") ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        //Turnierergebnisse filtern
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#myDIV tbody").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>

    <h1 class="w3-text-grey">Schiri-Pipeline</h1>
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <?= Html::icon("search") ?><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;"
                                          type="text" placeholder="Schiri suchen">
    </div>
    <div id="myDIV" class="w3-responsive w3-card-4">
        <table class="w3-table w3-bordered w3-striped w3-centered" style="white-space: nowrap">
            <tr class='w3-primary'>
                <th>Team</th>
                <th>Spieler</th>
                <th>Theo. Bestanden</th>
                <th>Fertig</th>
                <th>Abgegeben <br> Gestartet <br> Erstellt <br></th>
                <th>Level</th>
            </tr>
            <?php foreach ($results as $result): ?>

                <tr class="<?= ($result["bestanden"] === "Ja") ? "w3-green" : "" ?>">
                    <td><?= $result["teamname"] ?></td>
                    <td><?= $result["vorname"] . " " . mb_substr($result["nachname"], 0, 3, "UTF-8") . "." ?></td>
                    <td><?= ($result["bestanden"] == "Ja") ? Html::icon("check_circle_outline") : $result["bestanden"] ?></td>
                    <td><?= ($result["schiri"] >= (Config::SAISON + 1)) ? "Ja" : "Nein" ?></td>
                    <td>
                        <?= $result["t_abgegeben"] ?><br>
                        <?= $result["t_gestartet"] ?><br>
                        <?= $result["t_erstellt"] ?>
                    </td>
                    <td><?= $result["test_level"] ?></td>
                </tr>

            <?php endforeach; ?>
        </table>
    </div>
<?php
include '../../templates/footer.tmp.php';