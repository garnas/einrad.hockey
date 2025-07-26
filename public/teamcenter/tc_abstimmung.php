<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/abstimmung.logic.php';
$teams = TeamRepository::get()->activeLigaTeams();
$saison = (isset($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Abstimmung zum Ligalogo</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<section class="w3-section">
    <?php include '../../templates/abstimmung/intro.tmp.php'; ?>
</section>

<!-- vor der abstimmung -->
<?php if (time() < $beginn): ?>
    <section class="w3-section w3-padding w3-light-grey">
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
        <p>
            Die Abstimmung endet am
            <strong><?=date("d.m.Y", $abschluss)?></strong> um <strong><?=date("H:i", $abschluss)?> Uhr</strong>.
        </p>
        <?php Html::countdown($abschluss); ?>
    </section>

    <section class="w3-section">
        <?php include '../../templates/abstimmung/hinweise.tmp.php'; ?>
    </section>

    <hr>

    <section class="w3-section w3-light-grey w3-padding">
        <?php include '../../templates/abstimmung/status.tmp.php'; ?>
    </section>

    <hr>

    <section class="w3-section">
        <?php include '../../templates/abstimmung/form.tmp.php'; ?>
    </section>

<?php endif; ?>

<!-- nach der abstimmung -->
<?php if ($abschluss <= time()): ?>
    <section class="w3-section w3-padding w3-light-grey">
        <p>
            Die Abstimmung ist beendet. Vielen Dank für eure Teilnahme!
        </p>
    </section>
<?php endif; ?>

<?php
include '../../templates/footer.tmp.php';