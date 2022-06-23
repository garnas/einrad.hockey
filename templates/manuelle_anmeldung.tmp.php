<?php

use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;

?>
<h1 class="w3-text-primary">Manuelle Teamanmeldung</h1>
<h3 class="w3-text-grey">
    <?= TurnierSnippets::nameBrTitel($turnier) ?>
    <br>
    <span class="w3-text-secondary"><?= TurnierSnippets::translate($turnier->getPhase()) ?></span>
</h3>

<!-- Links -->
<p>
    <?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(),
        'Turnierdetails', icon:'info') ?>
</p>
<?php if (Helper::$ligacenter): ?>
    <p>
        <?= Html::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $turnier->id(),
            'Turnier bearbeiten (Ligaausschuss)', icon:'create') ?>
    </p>
    <p>
        <?= Html::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $turnier->id(),
            'Turnierlog einsehen (Ligaausschuss)', icon:'list') ?>
    </p>
<?php endif; ?>

<!-- Anzeigen der angemeldeten Teams und gleichzeitig Abmeldeformular -->
<div class="w3-panel w3-card-4">
    <?= TurnierSnippets::getListen($turnier) ?>
</div>
<div class="w3-panel w3-card-4">
    <form method='post'>
        <h3>Teams abmelden</h3>
        <?php foreach ($turnier->getListe() as $anmeldung): ?>
            <p>
                <input type='checkbox'
                       class='w3-check'
                       id="<?= $anmeldung->getTeam()->id() ?>"
                       name='team_abmelden[<?= $anmeldung->getTeam()->id() ?>]'
                       value='<?= $anmeldung->getTeam()->id() ?>'
                >
                <label class="w3-hover-text-secondary w3-text-primary"
                       for="<?= $anmeldung->getTeam()->id() ?>">
                    <?= $anmeldung->getTeam()->getName() ?>
                </label>
            </p>
        <?php endforeach; ?>
        <button class="w3-button w3-secondary w3-block" type='submit' name='abmelden'>Ausgewählte Teams abmelden</button>
    </form>

    <!-- Spielen-Liste auffuellen und Warteliste aktualisieren -->
    <?php if (Helper::$ligacenter): ?>
        <form method='post'>
            <p>
                <input type='submit' class='w3-button w3-block w3-tertiary' name='warteliste_aktualisieren' value='Warteliste neu durchnummerieren'>
            </p>
            <p>
                <input type='submit' class='w3-button w3-block w3-tertiary' name='setzliste_auffuellen' value='Warteliste -> Setzliste'>
            </p>
        </form>
    <?php endif; ?>
</div>

<!-- An- und Abmeldung -->
<div class="w3-card-4 w3-panel">
    <form class="" method="post">
        <h3>Ligateam anmelden</h3>
        <p>
            <label for="teamname" class='w3-text-primary'>Team wählen:</label><br>
            <input required type="text" style="max-width:400px" placeholder="Ligateam eingeben" class="w3-input w3-border w3-border-primary" list="teams" id="teamname" name="teamname">
            <?= Html::datalist_teams() ?>
        </p>
        <p>
            <label for="liste" class='w3-text-primary'>Liste wählen:</label>
            <select required class='w3-select w3-border w3-border-primary' name='liste' id='liste'>
                <option selected disabled value=''>--</option>
                <option value='setz'>Setzliste</option>
                <option value='warte'><?= $turnier->isSetzPhase() ? "Ende der" : "" ?> Warteliste</option>
            </select>
        </p>
        <p>
            <input type='submit' class='w3-button w3-margin-bottom w3-tertiary' name='team_anmelden' value='Anmelden'>
        </p>
    </form>
</div>

<div class="w3-panel w3-card-4">
    <form method="post">
        <h3>Nichtligateam anmelden</h3>
        <p>
            <label for="nl_teamname" class='w3-text-primary'>Teamname</label><br>
            <input required type="text" style="max-width:400px" class="w3-input w3-border w3-border-primary" placeholder="Nichtligateam eingeben" id="nl_teamname" name="nl_teamname">
        </p>
        <p>
            <label for="nl_liste" class='w3-text-primary'>Liste wählen:</label>
            <select required class='w3-select w3-border w3-border-primary' name='nl_liste' id='nl_liste'>
                <option selected disabled value=''>--</option>
                <option value='setz'>Setzliste</option>
                <option value='warte'><?= $turnier->isSetzPhase() ? "Ende der" : "" ?> Warteliste</option>
            </select>
        </p>
        <p>
            <input type='submit' class='w3-button w3-margin-bottom w3-tertiary' name='nl_anmelden' value='Anmelden'>
        </p>
    </form>
</div>