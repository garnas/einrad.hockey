<?php

use App\Repository\Team\TeamRepository;

/** @var \App\Model\Spielplan\Spielplan $spielplan */
$schiri_name = static fn(int $team_id): ?string => ($spielplan->getPlatzierungstabelle()[$team_id] ?? null)?->teamname
    ?? TeamRepository::get()->team($team_id)?->getName();
?>
<h3 class="w3-text-secondary w3-margin-top">Tore eintragen</h3>
<form method="post">
    <!-- Tore zwischenspeichern -->
    <p>
        <button type="submit"
               name="tore_speichern"
               class="w3-block w3-card w3-button w3-tertiary"
        >
            <span class="material-icons">save</span>
            Tore zwischenspeichern
        </button>
    </p>
    <!-- Tabelle: Tore eintippen -->
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-centered w3-striped">
            <tr class="w3-primary">
                <th>
                    <i class="material-icons">schedule</i>
                    <br>
                    Zeit
                </th>
                <th>
                    <i class="material-icons">sports</i>
                    <br>
                    Schiri
                </th>
                <th>
                    <i class="material-icons">sports_hockey</i>
                    <br>
                    Spiele
                </th>
                <th>
                    <i class="material-icons">sports_baseball</i>
                    <br>Tore
                </th>
                <th>
                    <i class="material-icons">priority_high</i>
                    <br>
                    Penalty
                </th>
            </tr>
            <?php foreach ($spielplan->getSpiele() as $spiel_id => $spiel) { ?>
                <tr <?php if (null !== $spiel->getToreA()
                        && null !== $spiel->getToreB()
                        && !$spielplan->hatPenaltySpiel($spiel_id, true)) { ?>
                        class="w3-pale-green"
                    <?php } //endif?>
                >
                    <td><?= $spiel->getZeit() ?></td>
                    <!-- Schiris -->
                    <td class="w3-center   w3-text-primary">
                        <span class="w3-tooltip">
                            <i><?= $spiel->getSchiriIdB() ?></i>
                            <span style="white-space: nowrap; position:absolute;left:0;bottom:15px"
                                  class="w3-text w3-small w3-primary w3-tag">
                                <i class="material-icons" style="vertical-align: -30%">keyboard_arrow_down</i>
                                <?= $schiri_name($spiel->getSchiriIdB()) ?>
                            </span>
                        </span>
                        <br>
                        <span class="w3-tooltip">
                            <i><?= $spiel->getSchiriIdA() ?></i>
                            <span style="white-space: nowrap; position:absolute;left:0;top:15px"
                                  class="w3-text w3-small w3-primary w3-tag">
                                <i class="material-icons" style="vertical-align: -30%">keyboard_arrow_up</i>
                                <?= $schiri_name($spiel->getSchiriIdA()) ?>
                            </span>
                        </span>
                    </td>
                    <!-- Teams -->
                    <td class="w3-center" style="white-space: nowrap;">
                        <span><?= $spiel->getTeamnameA() ?></span>
                        <br>
                        <span><?= $spiel->getTeamnameB() ?></span>
                    </td>
                    <!-- Tore Mobil -->
                    <td class="w3-center">
                        <input id="tore_a[<?= $spiel_id ?>]"
                               name="tore_a[<?= $spiel_id ?>]"
                               value='<?= $spiel->getToreA() ?>'
                               class='w3-input w3-border w3-round w3-center'
                               style='padding: 2px; width: 65px; display: inline-block;'
                               type='number'
                               autocomplete='off'
                               min='0'
                               step='1'
                        >
                        <br>
                        <input id="tore_b[<?= $spiel_id ?>]"
                               name="tore_b[<?= $spiel_id ?>]"
                               value='<?= $spiel->getToreB() ?>'
                               class='w3-input w3-border w3-round w3-center'
                               style='padding: 2px; width: 65px; display: inline-block;'
                               type='number'
                               autocomplete='off'
                               min='0'
                               step='1'
                        >
                    </td>
                    <!-- Penalty -->
                    <td class="w3-center <?= (!$spielplan->validatePenaltySpiel($spiel)) ?: 'w3-secondary' ?>">
                        <input id="penalty_a[<?= $spiel_id ?>]"
                               name="penalty_a[<?= $spiel_id ?>]"
                               value='<?= $spiel->getPenaltyA() ?>'
                               class='w3-input w3-border w3-round w3-center w3-text-secondary'
                               style='padding: 2px; width: 65px; display: inline-block;'
                               <?= !($spielplan->hatPenaltySpiel($spiel_id)
                                   || $spielplan->validatePenaltySpiel($spiel)) ? 'disabled placeholder = "/"' : '' ?>
                               type='number'
                               autocomplete='off'
                               min='0'
                               step='1'
                        >
                        <br>
                        <input id="penalty_b[<?= $spiel_id ?>]"
                               name="penalty_b[<?= $spiel_id ?>]"
                               value='<?= $spiel->getPenaltyB() ?>'
                               class='w3-input w3-border w3-round w3-center w3-text-secondary'
                               style='padding: 2px; width: 65px; display: inline-block;'
                                <?= !($spielplan->hatPenaltySpiel($spiel_id)
                                    || $spielplan->validatePenaltySpiel($spiel)) ? 'disabled placeholder = "/"' : '' ?>
                               type='number'
                               autocomplete='off'
                               min='0'
                               step='1'
                        >
                    </td>
                </tr>
                <?php if ($spielplan->getPause($spiel_id) > 0) { ?>
                    <!-- Spielpause -->
                    <tr>
                        <td>
                            <?= date("H:i",
                                strtotime(($spielplan->getSpiele()[$spiel_id + 1] ?? null)?->getZeit() ?? '')
                                - $spielplan->getPause($spiel_id) * 60) ?>
                        </td>
                        <td></td>
                        <td class="w3-center">
                            <i class="material-icons">schedule</i>
                            <i><?= $spielplan->getPause($spiel_id) ?>&nbsp; min Pause</i>
                            <i class="material-icons">schedule</i>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                <?php }// endif pause?>
            <?php }// end foreach spiele?>
        </table>
    </div>
    <!-- Formular für die Ergebniseintragung in den Centern erstellen -->
    <p>
        <button type="submit"
                name="tore_speichern"
                class="w3-block w3-card w3-button w3-tertiary"
        >
            <span class="material-icons">save </span>
            Tore zwischenspeichern
        </button>
    </p>
</form>

<script>
    document.addEventListener("wheel", function (event) {
        if (document.activeElement.type === "number") {
            document.activeElement.blur();
        }
    });
</script>
