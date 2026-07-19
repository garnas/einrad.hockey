<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/turnier_bearbeiten_first.logic.php'; //Turnier und $daten-Array erstellen + Sanitizing + Berechtigung Prüfen + Existiert das Turnier?
require_once '../../logic/turnier_bearbeiten_la.logic.php';

$ausrichter = $turnier->getAusrichter();
$ausrichter_name = $ausrichter->getName();
$ausrichter_team_id = $ausrichter->id();
$ausrichter_block = $ausrichter->getBlock();

$saison = (isset($_GET['saison'])) ? (int) $_GET['saison'] : Config::SAISON;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Turnier absagen (Ligaausschuss)</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<h2 class="w3-text-primary">
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
</h2>

<?php if ($turnier->isCanceled()): ?>
    <?php Html::message('info', 'Turnier wurde abgesagt - wende dich an den Technikausschuss um es wieder herzustellen.'); ?>
<?php else: ?>
    <form method="post">
        <div class="w3-panel w3-card-4">
            <h3 class="w3-text-secondary">Turnier absagen</h3>
            <div class="w3-panel w3-leftbar w3-border-grey w3-light-grey">
                <p>
                    Das Turnier wird in der Datenbank als "gelöscht" markiert und wird nicht mehr angezeigt. Das Turnier kann damit problemlos wiederhergestellt werden!
                </p>
            </div>
            <p>
                <label for='grund' class="w3-text-primary">Grund der Turnierabsage</label>
                <input required list="browsers" id="grund" name="grund" placeholder="Bitte eingeben..." class="w3-input w3-border w3-border-primary">
                <datalist id="browsers">
                    <option value="Zu wenig spielberechtigte Ligateams">
                    <option value="Vom Ausrichter im Vorfeld abgesagt">
                    <option value="Spaßturnier">
                </datalist>
            </p>
            <p>
                <input class="w3-check" type="checkbox" id="send_mail" name="send_mail" checked value="send_mail">
                <label for="send_mail">
                    Allen Teams auf der Warte- und Setzliste wird eine automatische E-Mail geschickt (mit dem Grund)
                </label>
            </p>
            <p>
                <input type="submit" value="Turnier absagen" name="absagen_turnier" class="w3-secondary w3-button w3-block">
            </p>
        </div>
    </form>
<?php endif; ?>

<p>
    <a href='../liga/turnier_details.php?turnier_id=<?=$turnier->id()?>'><button class="w3-button w3-text-primary w3-border w3-border-primary no">Zu den Turnierdetails</button></a>
    <a href='../ligacenter/lc_turnierliste.php?turnier_id=<?=$turnier->id()?>'><button style='display: inline;' class="w3-button w3-right w3-border w3-border-primary w3-text-primary no">Turniere verwalten (Liste)</button></a>
</p>

<?php
include '../../templates/footer.tmp.php';
