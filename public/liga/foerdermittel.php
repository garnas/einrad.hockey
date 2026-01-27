<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

use App\Repository\Neuigkeit\NeuigkeitRepository;
use App\Enum\NeuigkeitArt;

require_once '../../init.php';

$saison = (isset($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;
$foerdermittel = NeuigkeitRepository::get()->findByType(NeuigkeitArt::FOERDERMITTEL);

$foerdermittel_nach_jahr = [];

// gruppiere die foerdermittel nach jahr und monaten
$formatter = new IntlDateFormatter('de_DE', pattern: 'MMMM');
foreach ($foerdermittel as $mittel) {
    $jahr = $mittel->getZeit()->format('Y');
    $monat = $formatter->format($mittel->getZeit());
    $foerdermittel_nach_jahr[$jahr][$monat][] = $mittel;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

Html::$titel = "Fördermittel | Deutsche Einradhockeyliga";
Html::$content = "Hier findet man alle Fördermittel-Einträge des Ligaausschusses und der Teams der Deutschen Einradhockeyliga.";
include '../../templates/header.tmp.php'; ?>

<h1 class="w3-text-primary">Fördermittel</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<div class='w3-card w3-panel w3-leftbar w3-border-yellow w3-pale-yellow'>
    <h3>Fördermittelantrag</h3>
    <div class='w3-section'>Der Fördermittelantrag ist hier zu finden: <span><?= Html::link("foerdermittel_antrag.php", " Fördermittelantrag", false, "") ?></span></div>
</div>

<div class="w3-row-padding w3-stretch">
    
    <div class="w3-col l3 m4 w3-hide-small">
        <div class="w3-panel w3-card-4">
            <div class="w3-stretch w3-container w3-primary">
                <h3>Zeitleiste</h3>
            </div>
            <?php foreach ($foerdermittel_nach_jahr as $jahr => $neuigkeiten_nach_monat): ?>
                <div class="w3-section">
                    <h4 class="w3-text-primary"><?= $jahr ?></h4>
                    <ul class="w3-ul w3-hoverable">
                        <?php foreach ($neuigkeiten_nach_monat as $monat => $neuigkeiten): ?>
                            <li class="w3-padding-small">
                                <a class="" href="#<?= $jahr ?><?= $monat ?>" style="display:block; color:inherit; text-decoration:none;">
                                    <?= $monat ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="w3-col l9 m8">
        <?php foreach ($foerdermittel_nach_jahr as $jahr => $neuigkeiten_nach_monat): ?>
            <?php foreach ($neuigkeiten_nach_monat as $monat => $neuigkeiten): ?>
                <h2 id="<?= $jahr ?><?= $monat ?>" class="w3-text-primary w3-border-bottom w3-border-grey"><?= $monat ?> <?= $jahr ?></h2>
                <?php foreach ($neuigkeiten as $neuigkeit): ?>
                    <?php include '../../templates/neuigkeiten/neuigkeit.tmp.php'; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

</div>

<?php include '../../templates/footer.tmp.php';