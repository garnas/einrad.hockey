<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

use App\Repository\Turnier\TurnierRepository;

require_once '../../init.php';

$saison = (isset($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;
$turniere = TurnierRepository::getErgebnisTurniere(saison: $saison);

if (empty($turniere)) {
    Html::info("Es wurden keine Turnierergebnisse der Saison " . Html::get_saison_string($saison) . " eingetragen");
}

//Turnierreport Icon
$icon = (isset($_SESSION['logins']['team'])) ? 'article' : 'lock';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Turnierergebnisse " . Html::get_saison_string($saison) . " | Deutsche Einradhockeyliga";
Html::$content = 'Hier kann man die Ergebnisse und Tabellen der Saison ' . Html::get_saison_string($saison) . ' sehen.';
include '../../templates/header.tmp.php';

?>

<!--Javascript für Suchfunktion-->
<script src="<?= Env::BASE_URL ?>/javascript/jquery.min.js?v=20250825"></script>
<script>
    // Turnierergebnisse filtern
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
<h1 class="w3-text-primary">Ergebnisse</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

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

<!-- Turnierergebnisse -->
<div id="myDIV" class="ergebnisse-container">
    <?php foreach ($turniere as $turnier_id => $turnier): ?>
        <?php include '../../templates/turnier_ergebnis.tmp.php'; ?>
    <?php endforeach; ?>
</div>

<?php include '../../templates/footer.tmp.php';