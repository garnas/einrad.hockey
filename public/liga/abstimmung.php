<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/abstimmung.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Fördermittel Abstimmung | Deutsche Einradhockeyliga";
Html::$content = "Das aktuelle Abstimmungsergebnis der Teams über die Fördermittel.";
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Abstimmung Fördermittel</h1>

<!-- Informationstext für die Abstimmung -->
<p>Der Ligabeitrag wurde unter anderem erhöht, um Projekte wie die Förderung von Einradhockey-Initiativen zu unterstützen. Bislang haben wir vier Anträge erhalten. Um diese Mittel gerecht und transparent zu verteilen, haben wir ein vorläufiges Budget von 3.000,00 € festgelegt, über dessen Verteilung ihr mitentscheiden sollt.</p>
<p>Alle derzeit eingegangenen Anträge können gefördert werden, und es besteht weiterhin die Möglichkeit, weitere Anträge zu stellen, wenn ihr ein neues Projekt einbringen möchtet.</p>

<?php
if (time() > strtotime(Abstimmung::ENDE)){
    include '../../templates/abstimmung_ergebnis.tmp.php';
} else {
    Html::message('notice', "Das Abstimmungsergebnis wird hier am " . Abstimmung::ENDE . " Uhr veröffentlicht.", "");
?>

<a href="../teamcenter/tc_abstimmung.php" class="w3-button w3-section w3-block w3-primary">
    <?= Html::icon("how_to_vote") ?> Jetzt abstimmen!
</a>

<?php } //end if

include '../../templates/footer.tmp.php';