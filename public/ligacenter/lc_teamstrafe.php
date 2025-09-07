<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Entity\Team\Strafe;
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Team\TeamService;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

//Turnierdaten für Select
$turniere = TurnierRepository::getAlleTurniere();
$strafen = TeamRepository::get()->getStrafenBySaison();

//Formularauswertung
foreach ($strafen as $strafe) {
    if (isset($_POST['delete' . $strafe->getStrafeId()])) {
        TeamRepository::get()->deleteStrafe(strafeId: $strafe->getStrafeId());
        Html::info("Strafe wurde gelöscht.");
        Helper::reload("ligacenter/lc_teamstrafe.php");
    }
}

if (isset($_POST['strafe_eintragen'])) {
    $error = false;
    if (empty($_POST['teamname']) or empty($_POST['grund'])) {
        $error = true;
        Html::error("Bitte Team auswählen und Begründung eintragen.");
    }
    if (isset($_POST['verwarnung']) && !empty($_POST['prozent'])) {
        $error = true;
        Html::error("Prozentstrafen sind bei Vewarnungen nicht möglich.");
    }
    $team = TeamRepository::get()->findByName($_POST['teamname']);
    if (!$team->isLigaTeam()) {
        $error = true;
        Html::error("Teamname gehört zu keinem Ligateam");
    }
    if (!$error) {
        $strafe = new Strafe();
        $strafe
            ->setTeam($team)
            ->setGrund($_POST['grund'])
            ->setProzentsatz($_POST['prozent'])
            ->setVerwarnung($_POST['verwarnung'] ?? 'Nein')
            ->setSaison(Config::SAISON)
        ;
        $turnier_id = $_POST['turnier'];
        if ($turnier_id) {
            $turnier = TurnierRepository::get()->turnier(turnier_id: $_POST['turnier']);
            $strafe->setTurnier($turnier);
        }
        $team->getStrafen()->add($strafe);
        TeamRepository::get()->speichern($team);
        Html::info("Strafe wurde eingetragen.");
        Helper::reload("/liga/tabelle.php#strafen");
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<!-- Vergangene Strafen -->
<h2 class="w3-bottombar w3-border-primary">Vergebene Strafen/Verwarnungen der Saison <?= Html::get_saison_string() ?></h2>
<div class="w3-responsive">
    <table class="w3-table w3-striped">
        <thead>
            <tr class="w3-primary">
                <th>ID</th>
                <th>Verwarnung</th>
                <th>Team</th>
                <th>Grund</th>
                <th>Turnier</th>
                <th></th>
            </tr>
        </thead>
        <?php foreach ($strafen as $strafe) { ?>
            <tr>
                <td style="vertical-align: middle"><?= $strafe->getStrafeId() ?></td>
                <td style="vertical-align: middle"><?= $strafe->getVerwarnung() ?></td>
                <td style="white-space: nowrap; vertical-align: middle;"><?= $strafe->getTeam()->getName() ?></td>
                <td style="vertical-align: middle">
                    <?= $strafe->getGrund() ?>
                    <?php if (!empty($strafe->getProzentsatz())) { ?>(<?= $strafe->getProzentsatz() ?>&nbsp;%)<?php } //endif
                                                                                                    ?>
                </td>
                <td style="vertical-align: middle"><?= ($strafe->getTurnier()?->getDatum()?->format("d.m.Y") ?? '') . ' ' . ($strafe->getTurnier()?->getDetails()?->getOrt() ?? '') ?></td>
                <td style="vertical-align: middle">
                    <form method="POST" onsubmit="return confirm('Soll die Strafe/Verwarnung für das Team <?= $strafe->getTeam()->getName() ?> wirklich gelöscht werden?')">
                        <input type="hidden" name="delete<?= $strafe->getStrafeId() ?>" value='delete'>
                        <input class="w3-button w3-text-primary" type="submit" name="delete<?= $strafe->getStrafeId()  ?>" value="Löschen">
                    </form>
                </td>
            </tr>
        <?php } //end foreach
        ?>
    </table>
</div>

<!-- Neue Strafe -->
<h2 class="w3-bottombar w3-border-primary">Teamstrafe / -verwarnung eintragen</h2>
<form method="POST">
    <p>
        <input class="w3-check" type="checkbox" value="Ja" name="verwarnung" id="verwarnung" checked="checked">
        <label style="cursor: pointer" class="w3-text-primary w3-hover-text-secondary" for="verwarnung">Es handelt sich um eine Verwarnung</label>
    </p>
    <p>
        <label class="w3-text-primary" for="teamname">Team</label>
        <input required type="text" class="w3-input w3-border w3-border-primary" placeholder="Team eingeben" list="teams" id="teamname" name="teamname">
        <?= Html::datalist_teams(); ?>
    </p>
    <p>
        <label class="w3-text-primary" for="turnier">Turnier (optional)</label>
        <select class="w3-select w3-border w3-border-primary" name="turnier" id="turnier">
            <option value="" selected></option>
            <?php foreach ($turniere as $turnier) { ?>
                <option value="<?= $turnier->id() ?>"><?= $turnier->getDatum()->format("d.m.Y") . ' ' . $turnier->getDetails()->getOrt() . ' (' . $turnier->getBlock() . ')' ?></option>
            <?php } //end foreach
            ?>
        </select>
    <p>
    <p>
        <label class="w3-text-primary" for="grund">Grund</label>
        <input required class="w3-input w3-border w3-border-primary" type="text" name="grund" id="grund">
    <p>
    <p>
        <label class="w3-text-primary" for="prozent">Prozentstrafe in % (ganze Zahlen | leer lassen, wenn keine Prozentstrafe)</label>
        <input class="w3-input w3-border w3-border-primary" type="number" step="1" min="1" max="100" name="prozent" id="prozent">
    <p>
    <p>
        <input class="w3-button w3-tertiary" type="submit" name="strafe_eintragen" value="Strafe/Verwarnung eintragen">
    <p>
</form>

<?php include '../../templates/footer.tmp.php';
