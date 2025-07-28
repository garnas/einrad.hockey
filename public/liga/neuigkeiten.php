<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

require_once '../../init.php';

$neuigkeiten = Neuigkeit::get_neuigkeiten(limit: false, aktiv: null);

foreach ($neuigkeiten as $neuigkeiten_id => $neuigkeit) {
    $delta_zeit = (time() - strtotime($neuigkeit['zeit'])) / (60 * 60); //in Stunden
    if ($delta_zeit < 24) {
        $zeit = ($delta_zeit <= 1.5) ? "gerade eben" : "vor " . round($delta_zeit) . " Stunden";
    } elseif ($delta_zeit < 7 * 24) {
        $zeit = ($delta_zeit <= 1.5 * 24) ? "vor einem Tag" : "vor " . round($delta_zeit / 24) . " Tagen";
    } else {
        $zeit = date("d.m.Y", strtotime($neuigkeit['zeit']));
    }
    $neuigkeiten[$neuigkeiten_id]['zeit'] = $zeit;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

Html::$titel = "Neuigkeiten | Deutsche Einradhockeyliga";
Html::$content = "Hier findet man alle Neuigkeiteneinträge des Ligaausschusses und der Teams der Deutschen Einradhockeyliga.";
include '../../templates/header.tmp.php'; ?>

<?php foreach ($neuigkeiten as $neuigkeit):?>
    <?php include '../../templates/neuigkeiten/neuigkeit.tmp.php'; ?>
<?php endforeach; ?>