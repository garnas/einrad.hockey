<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/abstimmung.logic.php';

$teams = TeamRepository::get()->activeLigaTeams();
$saison = (isset($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;

$abschluss = strtotime(Abstimmung::ENDE);
$beginn = strtotime(Abstimmung::BEGINN);

$num_stimmen = Abstimmung::get_anzahl_stimmen();
$ergebnisse = Abstimmung::get_ergebnisse(0);

$logo = $ergebnisse['logo'] ?? [];


/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Abstimmung zum Ligalogo</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<?php include '../../templates/abstimmung/intro.tmp.php'; ?>

<!-- vor der abstimmung -->
<?php if (time() < $beginn): ?>
    
    <section class="w3-section w3-padding w3-light-grey">
        <h2 class="w3-large w3-text-secondary">Status der Abstimmung</h2>
        <p>
            Die Abstimmung beginnt am
            <strong><?=date("d.m.Y", $beginn)?></strong> um <strong><?=date("H:i", $beginn)?> Uhr</strong>
            und endet am
            <strong><?=date("d.m.Y", $abschluss)?></strong> um <strong><?=date("H:i", $abschluss)?> Uhr</strong>.
        </p>
        <?php Html::countdown($beginn); ?>
    </section>
<?php endif; ?>

<!-- waehrend der abstimmung -->
<?php if ($beginn <= time() && time() < $abschluss): ?>
    <section class="w3-section w3-padding w3-light-grey">
        <h2 class="w3-large w3-text-secondary">Status der Abstimmung</h2>
        <p>
            Die Abstimmung endet am
            <strong><?=date("d.m.Y", $abschluss)?></strong> um <strong><?=date("H:i", $abschluss)?> Uhr</strong>.
        </p>
        <?php Html::countdown($abschluss); ?>
    </section>
<?php endif; ?>

<!-- nach der abstimmung -->
<?php if ($abschluss <= time()): ?>
    <section class="w3-section w3-padding w3-light-grey">
        <h2 class="w3-large w3-text-secondary">Status der Abstimmung</h2>
        <p>Die Abstimmung ist abgeschlossen.</p>
    </section>

    <?php include '../../templates/abstimmung/beteiligung.tmp.php'; ?>
    <?php include '../../templates/abstimmung/frage_logo.tmp.php'; ?>
<?php endif; ?>

<?php
include '../../templates/footer.tmp.php';