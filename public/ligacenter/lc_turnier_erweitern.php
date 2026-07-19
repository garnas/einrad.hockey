<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

use App\Service\Turnier\TurnierFormService;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/turnier_bearbeiten_first.logic.php'; //Turnier und $daten-Array erstellen + Sanitizing + Berechtigung Prüfen + Existiert das Turnier?

require_once '../../logic/turnier_erweitern.logic.php';

$saison = (isset($_GET['saison'])) ? (int) $_GET['saison'] : Config::SAISON;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Turnier erweitern (Ligaausschuss)</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<h2 class="w3-text-primary">
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
</h2>

<form method="post">
    <div class="w3-panel w3-card-4">
        <h3 class="w3-text-secondary">Turnier erweitern</h3>

        <h4 class="w3-text-primary">Blockhöher / -niedriger erweitern</h4>
        <p>
            <input 
                class="w3-radio" 
                style="cursor: pointer" 
                type="radio" 
                name="single" 
                id="higher" 
                value="higher" 
                <?php if (TurnierFormService::isSelected('single_higher', $turnier)): ?> checked <?php endif; ?>
            >
            <label for="higher" style="cursor: pointer">Turnier um einen Block <b>nach oben</b> erweitern</label>
        </p>
        <p>
            <input 
                class="w3-radio" 
                style="cursor: pointer" 
                type="radio" 
                name="single" 
                id="lower" 
                value="lower" 
                <?php if (TurnierFormService::isSelected('single_lower', $turnier)): ?> checked <?php endif; ?>
            >
            <label for="lower" style="cursor: pointer">Turnier um einen Block <b>nach unten</b> erweitern</label>
        </p>
        <p>
            <input 
                class="w3-radio" 
                style="cursor: pointer" 
                type="radio" 
                name="single" 
                id="single_none" 
                value="none"
                <?php if (TurnierFormService::isSelected('single_none', $turnier)): ?> checked <?php endif; ?>
            >
            <label for="single_none" style="cursor: pointer">Keine einzelne Blockerweiterung vornehmen</label>
        </p>

        <h4 class="w3-text-primary">Blockfrei erweitern</h4>
        <p>
            <input 
                class="w3-radio" 
                style="cursor: pointer" 
                type="radio" 
                name="multiple" 
                id="free" 
                value="free"
                <?php if (TurnierFormService::isSelected('multiple_free', $turnier)): ?> checked <?php endif; ?>
            >
            <label for="free" style="cursor: pointer">Turnier für <b>alle</b> Blöcke erweitern</label>
        </p>
        <p>
            <input 
                class="w3-radio" 
                style="cursor: pointer" 
                type="radio" 
                name="multiple" 
                id="multiple_none" 
                value="none"
                <?php if (TurnierFormService::isSelected('multiple_none', $turnier)): ?> checked <?php endif; ?>
            >
            <label for="multiple_none" style="cursor: pointer">Keine einzelne Blockerweiterung vornehmen</label>
        </p>

        <p><input type="submit" value="Turnier erweitern" name="erweitern_turnier" class="w3-tertiary w3-button w3-block"></p>
    </div>
</form>

<p>
    <a href='../liga/turnier_details.php?turnier_id=<?=$turnier->id()?>'><button class="w3-button w3-text-primary w3-border w3-border-primary no">Zu den Turnierdetails</button></a>
    <a href='../ligacenter/lc_turnierliste.php?turnier_id=<?=$turnier->id()?>'><button style='display: inline;' class="w3-button w3-right w3-border w3-border-primary w3-text-primary no">Turniere verwalten (Liste)</button></a>
</p>

<?php
include '../../templates/footer.tmp.php';
