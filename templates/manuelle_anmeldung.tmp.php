<h1 class="w3-text-primary">Manuelle Teamanmeldung</h1>
<h3 class="w3-text-grey">
    <?= $turnier->details['tname'] ?: 'Turnier' ?>
    in <?= $turnier->details['ort'] ?>
    am <?= $turnier->details['datum'] ?>
    (<?= $turnier->details['tblock'] ?>)
</h3>

<!-- Links -->
<p>
    <?= Form::link('../liga/turnier_details.php?turnier_id=' . $turnier->details['turnier_id'],
        'Turnierdetails', icon:'info') ?>
</p>
<?php if (Config::$ligacenter){ ?>
    <p>
        <?= Form::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $turnier->details['turnier_id'],
            'Turnier bearbeiten (Ligaausschuss)', icon:'create') ?>
    </p>
    <p>
        <?= Form::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $turnier->details['turnier_id'],
            'Turnierlog einsehen (Ligaausschuss)', icon:'list') ?>
    </p>
<?php }//endif?>

<!-- Anzeigen der angemeldeten Teams und gleichzeitig Abmeldeformular -->
<div class="w3-panel w3-card-4">
    <form method='post'>
        <h3>Angemeldete Teams</h3>
        <h4 class="w3-text-primary">Spielen-Liste:</h4>
        <p>
            <?php if (!empty($anmeldungen['spiele'])) { ?>
                <?php foreach ($anmeldungen['spiele'] as $team) { ?>
                    <?= $team['teamname'] ?> <span class="w3-text-primary">(<?= $team['tblock'] ?: 'NL' ?>)</span>
                    <input type='submit' class='w3-button w3-text-primary' name='abmelden<?= $team['team_id'] ?>' value='Abmelden'>
                    <br>
                <?php }//end foreach?>
            <?php } else { ?>
                <i>leer</i>
            <?php } //endif?>
        </p>
        <h4 class="w3-text-primary">Meldeliste:</h4>
        <p>
            <?php if (!empty($anmeldungen['melde'])) { ?>
                <?php foreach ($anmeldungen['melde'] as $team) { ?>
                    <?= $team['teamname'] ?> <span class="w3-text-primary">(<?= $team['tblock'] ?: 'NL' ?>)</span>
                    <input type='submit' class='w3-button w3-text-primary' name='abmelden<?= $team['team_id'] ?>' value='Abmelden'>
                    <br>
                <?php }//end foreach?>
            <?php } else { ?>
                <i>leer</i>
            <?php } //endif?>
        </p>
        <h4 class="w3-text-primary">Warteliste:</h4>
        <p>
            <?php if (!empty($anmeldungen['warte'])) { ?>
                <?php foreach ($anmeldungen['warte'] as $team) { ?>
                    <?= $team['position_warteliste'] . ". " . $team['teamname'] ?>
                    <span class="w3-text-primary">(<?= $team['tblock'] ?? 'NL' ?>)</span>
                    <input type='submit' class='w3-button w3-text-primary' name='abmelden<?= $team['team_id'] ?>' value='Abmelden'>
                    <br>
                <?php }//end foreach?>
            <?php } else { ?>
                <i>leer</i>
            <?php } //endif?>
        </p>
        <p>
            Freie Plätze: <?= $turnier->details['plaetze'] - count(($anmeldungen['spiele'] ?? [])) ?>
           von <?= $turnier->details['plaetze'] ?>
        </p>
        <p class="w3-small w3-text-primary">
            Phase: <?= $turnier->details['phase'] ?: '--' ?>
        </p>
        <!-- hidden input, um zu erkennen ob ein Team abgemeldet werden soll -->
        <input type='hidden' name='abmelden' value='abmelden'>
    </form>

    <!-- Spielen-Liste auffuellen und Warteliste aktualisieren -->
    <?php if (Config::$ligacenter) { ?>
        <form method='post'>
            <p>
                <input type='submit' class='w3-button w3-block w3-tertiary' name='warteliste_aktualisieren' value='Warteliste aktualisieren'>
            </p>
            <p>
                <input type='submit' class='w3-button w3-block w3-tertiary' name='spieleliste_auffuellen' value='Warteliste -> Spielen-Liste'>
            </p>
        </form>
    <?php }//endif?>
</div>

<!-- An- und Abmeldung -->
<div class="w3-card-4 w3-panel">
    <form class="" method="post">
        <h3>Ligateam anmelden</h3>
        <p>
            <label for="teamname" class='w3-text-primary'>Team wählen:</label><br>
            <input required type="text" style="max-width:400px" placeholder="Ligateam eingeben" class="w3-input w3-border w3-border-primary" list="teams" id="teamname" name="teamname">
            <?= Form::datalist_teams() ?>
        </p>
        <p>
            <label for="liste" class='w3-text-primary'>Liste wählen:</label>
            <select required class='w3-select w3-border w3-border-primary' name='liste' id='liste'>
                <option selected disabled value=''>--</option>
                <option value='spiele'>Spielen-Liste</option>
                <option value='melde'>Meldeliste</option>
                <option value='warte'>Warteliste</option>
            </select>
        </p>
        <p>
            <label for="pos" class='w3-text-primary'>Position auf der Warteliste</label>
            <select required class='w3-select w3-border w3-border-primary' name='pos' id='pos'>
                <option selected value='<?= count($anmeldungen['warte'] ?? []) + 1 ?>'>Ende der Warteliste</option>
                <?php for ($i = 1; $i <= count($anmeldungen['warte'] ?? []); $i++) { ?>
                    <option value='<?= $i ?>'>Position <?= $i ?></option>
                <?php } //end for?>
            </select>
        </p>
        <p>
            <input type='submit' class='w3-button w3-margin-bottom w3-tertiary' name='team_anmelden' value='Anmelden'>
        </p>
    </form>
</div>

<div class="w3-panel w3-card-4">
    <form class="" method="post">
        <h3>Nichtligateam anmelden</h3>
        <p>
            <label for="nl_teamname" class='w3-text-primary'>Teamname</label><br>
            <input required type="text" style="max-width:400px" class="w3-input w3-border w3-border-primary" placeholder="Nichtligateam eingeben" id="nl_teamname" name="nl_teamname">
        </p>
        <p>
            <label for="nl_liste" class='w3-text-primary'>Liste wählen:</label>
            <select required class='w3-select w3-border w3-border-primary' name='nl_liste' id='nl_liste'>
                <option selected disabled value=''>--</option>
                <option value='spiele'>Spielen-Liste</option>
                <option value='melde'>Meldeliste</option>
                <option value='warte'>Warteliste</option>
            </select>
        </p>
        <p>
            <label for="nl_pos" class='w3-text-primary'>Position auf der Warteliste</label>
            <select required class='w3-select w3-border w3-border-primary' name='nl_pos' id='nl_pos'>
                <option selected value='<?= count($anmeldungen['warte'] ?? []) + 1 ?>'>Ende der Warteliste</option>
                <?php for ($i = 1; $i <= count($anmeldungen['warte'] ?? []); $i++) { ?>
                    <option value='<?= $i ?>'>Position <?= $i ?></option>
                <?php } //end for?>
            </select>
        </p>
        <p>
            <input type='submit' class='w3-button w3-margin-bottom w3-tertiary' name='nl_anmelden' value='Anmelden'>
        </p>
    </form>
</div>