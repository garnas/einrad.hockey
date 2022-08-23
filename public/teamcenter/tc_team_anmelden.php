<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Team\TeamService;
use App\Service\Team\TeamValidator;
use App\Service\Turnier\BlockService;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

$team_id = $_SESSION['logins']['team']['id'];


//Check ob das Team über fünf Spieler verfügt
if (!TeamValidator::hasGenugSpieler($teamEntity)) {
    Html::info('Bitte tragt euren Teamkader ein, um euch zu Turnieren anmelden zu können.');
    Helper::reload('/teamcenter/tc_kader.php');
}

// Turnierobjekt erstellen
$turnier_id = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnier_id);

// Existiert das Turnier?
if (is_null($turnier)) {
    Helper::not_found("Turnier wurde nicht gefunden.");
}

if ($turnier->isSpassTurnier()){
    $kontakt = new Kontakt ($turnier->getAusrichter()->id());
    $email = $kontakt->get_emails();
    Html::notice("Anmeldung zu Spass-Turnieren erfolgt über den Ausrichter: "
        . Html::mailto($email, $turnier->getAusrichter()->getName()),
        esc:false
    );
    Helper::reload('/liga/turnier_details.php', '?turnier_id=' . $turnier->id());
}

// Reguläres Anmelden
if (isset($_POST['anmelden'])){
    if (TeamValidator::isValidRegularAnmeldung($teamEntity, $turnier)) {
        TeamService::anmelden($teamEntity, $turnier);
        TurnierRepository::get()->speichern($turnier);
        Html::info("Dein Team wurde zum Turnier angemeldet.");
        Helper::reload('/teamcenter/tc_team_anmelden.php', '?turnier_id=' . $turnier->id());
    } else {
        Html::error("Dein Team wurde nicht angemeldet.");
    }
}

//Freilos setzen
if (isset($_POST['freilos'])){
    if (TeamValidator::isValidFreilos($teamEntity, $turnier)) {
        TeamService::freilos($teamEntity, $turnier);
        TeamRepository::get()->speichern($teamEntity);
        Html::info ("Dein Team wurde zum Turnier via Freilos angemeldet.");
        Helper::reload("/teamcenter/tc_team_anmelden.php", "?turnier_id=" . $turnier->id());
    } else {
        Html::error ("Dein Team wurde nicht angemeldet.");
    }
}

//Team abmelden
if (isset($_POST['abmelden']) && Helper::$teamcenter) {
    if (TeamValidator::isValidAbmeldung($teamEntity, $turnier)) {
        TeamService::abmelden($teamEntity, $turnier);
        TurnierRepository::get()->speichern($turnier);
        Html::info ("Dein Team wurde erfolgreich abgemeldet");
        Helper::reload("/teamcenter/tc_team_anmelden.php", "?turnier_id=" . $turnier->id());
    } else {
        Html::error("Abmeldung war nicht möglich.");
    }
}

// Für Abschlussturnier bewerben
if (isset($_POST['bewerben'])){
    if (TeamValidator::isValidFinalMeldung($teamEntity, $turnier)) {
        TurnierService::addToWarteListe($turnier, $teamEntity);
        TurnierRepository::get()->speichern($turnier);
        Html::info("Deine Meldung wurde erfolgreich entgegen genommmen.");
        Helper::reload("/teamcenter/tc_team_anmelden.php", "?turnier_id=" . $turnier->id());
    } else {
        Html::error("Finalturnieranmeldung war nicht möglich.");
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h2 class="w3-text-primary">Turnieranmeldung</h2>
<h3 class="w3-text-grey"><?= TurnierSnippets::nameBrTitel($turnier) ?></h3>

<!-- Weiterführende Links -->
<?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(),
    "Turnierdetails",
    false,
    "launch") ?>
<?= Html::link('../teamcenter/tc_turnierliste_anmelden.php?turnier_id=' . $turnier->id(),
    "Zurück zur Turnieranmeldeliste",
    false,
    "launch") ?>

<!-- Anzeigen der angemeldeten Teams und gleichzeitig Abmeldeformular -->
<div class="w3-card w3-container">
    <form method='post'>
        <h3 class="w3-text-primary">Listen</h3>

        <?= TurnierSnippets::getListen($turnier) ?>
        <p class="w3-small w3-text-primary">Phase: <?= TurnierSnippets::translate($turnier->getPhase()) ?></p>
    </form>

    <!-- An- und Abmeldung -->
    <?php if ($turnier->isFinalTurnier()) {?>
        <form class="" method="post">
            <p>
                <input type='submit'
                       class='w3-button w3-margin-bottom w3-block
                                w3-tertiary <?= (TeamService::isAngemeldet($teamEntity, $turnier)) ? "w3-opacity" : "" ?>'
                       name='bewerben'
                       value='Melden für Abschlussturnier'
                >
                <span class="w3-text-grey"><i class="material-icons">info</i>Wir wollen auf dem Abschlussturnier spielen bzw. wir wären bereit nachzurücken.</span>
            </p>
        </form>
    <?php } elseif ($turnier->isLigaturnier()) { ?>
        <form class="" method="post">
            <p>
                <input type='submit' class='<?= (TeamService::isAngemeldet($teamEntity, $turnier)) ? "w3-opacity" : "" ?> w3-button w3-margin-bottom w3-block w3-tertiary w3-right'
                       name='anmelden'
                       value='Anmelden <?php if (!BlockService::isBlockPassend($turnier, $teamEntity) && $turnier->isSetzPhase()): ?> (Warteliste)<?php endif; ?>'>
            </p>
        </form>
        <form method="post" onsubmit="return confirm('Freilose setzen dein Team direkt auf die Spielen-Liste. Beim Übergang in die Meldephase wirst du auf die Warteliste gesetzt, wenn dein Teamblock höher ist als der Turnierblock. Das Freilos wird euch dann erstattet.');">
            <p>
                <input type='submit'
                    class='w3-button w3-margin-bottom w3-block w3-tertiary
                    <?php if (TeamService::isAufSetzliste($teamEntity, $turnier)
                                || !TurnierService::isSpielBerechtigtFreilos($turnier, $teamEntity)
                                || $teamEntity->getFreilose() <= 0
                             ): ?> w3-opacity<?php endif; ?>'
                    name='freilos' value='Freilos setzen (<?=$teamEntity->getFreilose()?> vorhanden)'>
            </p>
        </form>
    <?php } //endif?>
        <form method="post" onsubmit="return confirm('Dein Team wird vom Turnier abgemeldet werden.');">
            <p><input type='submit' class='<?php if (!TeamService::isAngemeldet($teamEntity, $turnier)): ?>w3-opacity<?php endif;?> w3-button w3-margin-bottom w3-block w3-tertiary w3-right' name='abmelden' value='Abmelden'></p>
            <?php if($turnier->isLigaturnier()): ?>
                <p class="w3-text-grey">Abmeldung von der Spielen-Liste ist möglich bis <?= TurnierService::getAbmeldeFrist($turnier) ?></p>
            <?php endif; ?>
        </form>
    </div>

<?php include '../../templates/footer.tmp.php';