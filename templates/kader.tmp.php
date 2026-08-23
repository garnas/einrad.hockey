<?php

use App\Service\Team\TeamSnippets;

?>

<!-- Aktuelle Saison -->
<div class="w3-panel">
    <h1 class="w3-text-primary"><?= Html::icon("group", tag: "h1") ?> <?= e($teamEntity->getName()) ?></h1>
    <h3>Saison <?= Html::get_saison_string() ?></h3>
    <div class="w3-responsive">
        <table class="w3-table w3-striped">
            <thead>
            <tr>
                <th class="w3-primary">ID</th>
                <th class="w3-primary">Name</th>
                <th class="w3-primary w3-center">Jahrgang</th>
                <th class="w3-primary w3-center">Schiri<sup>*</sup></th>
                <?php if (Helper::$ligacenter): ?>
                    <th class="w3-primary w3-center">Hinzugefügt am:</th>
                <?php endif; ?>
            </tr>
            </thead>
            <?php foreach ($teamEntity->getKaderAktuell() as $spieler): ?>
                <tr>
                    <td><?= $spieler->getSpielerId() ?></td>
                    <?php if (Helper::$ligacenter): // Link zum Bearbeiten als LA?>
                        <td>
                            <?= Html::link('lc_spieler_aendern.php?spieler_id=' . $spieler->getSpielerId(), $spieler->getName()) ?>
                        </td>
                    <?php else: ?>
                        <td>
                            <?= e($spieler->getName()) ?>
                        </td>
                    <?php endif; ?>
                    <td class='w3-center'>
                        <?= $spieler->getJahrgang() ?>
                    </td>
                    <td class='w3-center'>
                        <?= TeamSnippets::schiritag($spieler) ?>
                    </td>
                    <?php if (Helper::$ligacenter): ?>
                        <td class="w3-center">
                            <?= $spieler->getTimestamp()?->format("d.m.y H:i:s") ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <span class="w3-text-grey w3-small">
        <sup>*</sup>Schirilizenz ist gültig bis zum Ende der angezeigten Saison
    </span>
</div>

<!-- Kader bearbeiten -->
<div class="w3-panel">
    <h2 class="w3-text-primary"><?= Html::icon("edit", tag: "h2") ?> Kader bearbeiten</h2>
    <div class="w3-row-padding w3-stretch">
        <div class="w3-third w3-margin-bottom">
            <button class="w3-button w3-block w3-tertiary"
                    onclick="document.getElementById('spieler_uebernehmen').style.display='block'">
                <?= Html::icon("swap_horiz") ?> Von anderem Team übernehmen
            </button>
            <p class="w3-text-grey w3-small">
                Für Spieler, die in der aktuellen Saison noch nicht gemeldet sind. Der Schiedsrichterstatus wird
                dabei ebenfalls übernommen.
            </p>
        </div>
        <?php if (!empty($kaderVorsaison)): ?>
            <div class="w3-third w3-margin-bottom">
                <button class="w3-button w3-block w3-tertiary"
                        onclick="document.getElementById('spieler_vorsaison').style.display='block'">
                    <?= Html::icon("history") ?> Aus der Vorsaison übernehmen
                </button>
                <p class="w3-text-grey w3-small">
                    Für eigene Spieler aus einer vorherigen Saison, die noch nicht in die aktuelle Saison übernommen
                    wurden.
                </p>
            </div>
        <?php endif; ?>
        <div class="w3-third w3-margin-bottom">
            <button class="w3-button w3-block w3-tertiary"
                    onclick="document.getElementById('spieler_eintragen').style.display='block'">
                <?= Html::icon("person_add") ?> Neuen Spieler eintragen
            </button>
            <p class="w3-text-grey w3-small">
                Sollte ein Spieler in den obigen Listen nicht zu finden sein, kann er hier neu eingetragen werden.
            </p>
        </div>
    </div>

    <!-- Modal: Spieler von einem anderen Team übernehmen -->
    <div class="w3-modal" id="spieler_uebernehmen" style="display: none;">
        <form class="w3-card-4 w3-modal-content w3-panel" style="max-width: 400px;" method='POST'>
            <span onclick="document.getElementById('spieler_uebernehmen').style.display='none'"
                  class="w3-button w3-large w3-text-secondary w3-display-topright">
                &times;
            </span>
            <h3 class="w3-text-primary">Spieler von anderem Team übernehmen</h3>
            <p>
                <label class="w3-text-primary" for="spieler_suche">Name</label>
                <input class="w3-input w3-border w3-border-primary"
                       type="text"
                       list="spieler_liste"
                       id="spieler_suche"
                       autocomplete="off"
                       placeholder="Name eingeben..."
                       required>
                <datalist id="spieler_liste">
                    <?php foreach ($uebernehmbareSpieler as $spieler): ?>
                        <option data-id="<?= $spieler->getSpielerId() ?>" value="<?= e($spieler->getName()) ?> (<?= e($spieler->getTeam()?->getName() ?? 'kein Team') ?>)">
                    <?php endforeach; ?>
                </datalist>
                <input type="hidden" name="spieler_id" id="spieler_id">
            </p>
            <p>
                <input type="checkbox" class="w3-check" value="zugestimmt" name="dsgvo" id="dsgvo_uebernahme">
                <label for="dsgvo_uebernahme" style="cursor: pointer;" class="">
                    Der Spieler hat die aktuellen <?= Html::link(Nav::LINK_DSGVO, "Datenschutz-Hinweise", true) ?>
                    gelesen und der Verwendung seiner Daten zugestimmt.<br>Bei unter 16-Jährigen wurde die Erlaubnis der Eltern eingeholt.
                </label>
            </p>
            <p>
                <input class="w3-button w3-tertiary" type='submit' name='spieler_uebernahme' value='Spieler übernehmen'>
            </p>
        </form>
    </div>

    <!-- Modal: Spieler aus der Vorsaison übernehmen -->
    <div class="w3-modal" id="spieler_vorsaison" style="display: none;">
        <form method="post" class="w3-card-4 w3-modal-content w3-panel" style="max-width: 700px;">
            <span onclick="document.getElementById('spieler_vorsaison').style.display='none'"
                  class="w3-button w3-large w3-text-secondary w3-display-topright">
                &times;
            </span>
            <h3 class="w3-text-primary">Spieler aus der Vorsaison übernehmen</h3>
            <div class="w3-responsive">
                <table class="w3-table w3-striped">
                    <tr>
                        <th class="w3-primary">ID</th>
                        <th class="w3-primary">Name</th>
                        <th class="w3-primary w3-center">Jahrgang</th>
                        <th class="w3-primary w3-center">Schiri</th>
                        <th class="w3-primary ">Übernehmen</th>
                    </tr>
                    <?php if ($kaderVorsaison->isEmpty()): ?>
                        <td colspan="5">Keine Spieler der Vorsaison verfügbar.</td>
                    <?php endif; ?>
                    <?php foreach ($kaderVorsaison as $spieler): ?>
                        <tr>
                            <td><?= $spieler->getSpielerId() ?></td>

                            <?php if (Helper::$ligacenter): // Link zum Bearbeiten als LA?>
                                <td>
                                    <?= Html::link('lc_spieler_aendern.php?spieler_id=' . $spieler->getSpielerId(), $spieler->getName()) ?>
                                </td>
                            <?php else: ?>
                                <td>
                                    <?= e($spieler->getName()) ?>
                                </td>
                            <?php endif; ?>

                            <td class='w3-center'>
                                <?= $spieler->getJahrgang() ?>
                            </td>
                            <td class='w3-center'>
                                <?= TeamSnippets::schiritag($spieler) ?>
                            </td>
                            <td>
                                <input type="checkbox"
                                       class="w3-check"
                                       id="<?= $spieler->getSpielerId() ?>"
                                       name="takeover[]"
                                       value="<?= $spieler->getSpielerId() ?>">
                                <label style="cursor: pointer"
                                       class="w3-hover-text-secondary w3-text-primary"
                                       for="<?= $spieler->getSpielerId() ?>">
                                    Spieler übernehmen
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <p>
                <input type="checkbox" class="w3-check" value="zugestimmt" name="dsgvo" id="dsgvo">
                <label for="dsgvo" style="cursor: pointer;" class="w3-text-black">
                    Alle ausgewählten Spieler haben die aktuellen <?= Html::link(Nav::LINK_DSGVO, 'Datenschutz-Hinweise') ?>
                    gelesen und ihnen zugestimmt. Bei unter 16-Jährigen wurde die Erlaubnis der Eltern eingeholt.
                </label>
            </p>
            <p>
                <input type="submit" name="submit_takeover" value="Ausgewählte Spieler übernehmen" class="w3-button w3-tertiary">
            </p>
        </form>
    </div>

    <!-- Modal: Neuen Spieler eintragen -->
    <div class="w3-modal" id="spieler_eintragen" style="display: none;">
        <form class="w3-card-4 w3-modal-content w3-panel" style="max-width: 400px;" method='POST'>
            <span onclick="document.getElementById('spieler_eintragen').style.display='none'"
                  class="w3-button w3-large w3-text-secondary w3-display-topright">
                &times;
            </span>
            <h3 class="w3-text-primary">Neuen Spieler eintragen</h3>
            <p>
                <label class="w3-text-primary" for="vorname">Vorname</labeL>
                <input class="w3-input w3-border w3-border-primary"
                       value="<?= $_POST['vorname'] ?? '' ?>"
                       type="text"
                       name="vorname"
                       id="vorname"
                       autocomplete="off"
                       required>
            </p>
            <p>
                <label class="w3-text-primary" for="nachname">Nachname</labeL>
                <input class="w3-input w3-border w3-border-primary"
                       type="text"
                       value="<?= $_POST['nachname'] ?? '' ?>"
                       name="nachname"
                       id="nachname"
                       autocomplete="off"
                       required>
            </p>
            <p>
                <label class="w3-text-primary" for="jahrgang">Jahrgang</labeL>
                <input class="w3-input w3-border w3-border-primary"
                       value="<?= $_POST['jahrgang'] ?? '' ?>"
                       placeholder="vierstellig"
                       type="number"
                       name="jahrgang"
                       id="jahrgang"
                       autocomplete="off"
                       required>
            </p>
            <p>
                <label class="w3-text-primary" for="geschlecht">Geschlecht</labeL>
                <select style="height:40px" class='w3-input w3-border w3-border-primary' name='geschlecht' id='geschlecht'>
                    <option <?= $_POST['geschlecht'] ?? 'selected' ?> disabled>Bitte wählen</option>
                    <option <?php if (($_POST['geschlecht'] ?? null) === 'm') { ?>selected<?php } ?> value='m'>m</option>
                    <option <?php if (($_POST['geschlecht'] ?? null) === 'w') { ?>selected<?php } ?> value='w'>w</option>
                    <option <?php if (($_POST['geschlecht'] ?? null) === 'd') { ?>selected<?php } ?> value='d'>d</option>
                    <option <?php if (($_POST['geschlecht'] ?? null) === '') { ?>selected<?php } ?> value=''>Keine Angabe</option>
                </select>
            </p>
            <p>
                <input type="checkbox" class="w3-check" value="zugestimmt" name="dsgvo" id="dsgvo_neu">
                <label for="dsgvo_neu" style="cursor: pointer;" class="">
                    Der Spieler hat die aktuellen <?= Html::link(Nav::LINK_DSGVO, "Datenschutz-Hinweise", true) ?>
                    gelesen und der Verwendung seiner Daten zugestimmt.<br>Bei unter 16-Jährigen wurde die Erlaubnis der Eltern eingeholt.
                </label>
            </p>
            <p>
                <input class="w3-button w3-tertiary" type='submit' name='neuer_eintrag' value='Spieler eintragen'>
            </p>
        </form>
    </div>
</div>

<script>
    // Get the modals
    var modals = [
        document.getElementById('spieler_uebernehmen'),
        document.getElementById('spieler_vorsaison'),
        document.getElementById('spieler_eintragen'),
    ];

    // When the user clicks anywhere outside of a modal, close it
    window.onclick = function (event) {
        modals.forEach(function (modal) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    }

    // Spieler-Auswahl aus der Datalist auf die versteckte Spieler-ID abbilden
    var spielerSuche = document.getElementById('spieler_suche');
    var spielerListe = document.getElementById('spieler_liste');
    var spielerId = document.getElementById('spieler_id');
    spielerSuche.addEventListener('input', function () {
        spielerId.value = '';
        for (var i = 0; i < spielerListe.options.length; i++) {
            if (spielerListe.options[i].value === spielerSuche.value) {
                spielerId.value = spielerListe.options[i].getAttribute('data-id');
                break;
            }
        }
    });
</script>