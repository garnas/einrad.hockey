<?php

use App\Entity\Team\Freilos;
use App\Entity\Team\FreilosGrund;

?>
<div class="w3-card-4 w3-responsive w3-panel">
    <form method='post'>
        <h3>Ligaausschuss</h3>
        <div class="w3-row-padding">
            <div class="w3-half">
                <p>
                    <label for="teamname" class="w3-text-primary">Teamname</label>
                    <input required
                           class='w3-input w3-border w3-border-primary'
                           type='text'
                           id='teamname'
                           name='teamname
                           <?php /** @var \App\Entity\Team\nTeam $team */ ?>
                           value='<?= $team->getName() ?>'
                    >
                </p>
            </div>
            <div class="w3-half">
                <p>
                    <label for="paswort" class="w3-text-primary">Passwort</label>
                    <input class='w3-input w3-border w3-border-primary'
                           type='text'
                           id='passwort'
                           name='passwort'
                           placeholder='Neues Passwort vergeben'>
                </p>
            </div>
            <div class="w3-half">
                <p>
                    <label for="freilos_grund" class="w3-text-primary">Neues Freilos (Grund)</label>
                    <select class='w3-select w3-border w3-border-primary'
                            name='freilos_grund' id="freilos_grund"
                    >
                        <option value='NO_CHANGE'>Auswählen zum Hinzufügen</option>
                        <?php foreach (FreilosGrund::cases() as $grund): ?>
                            <option value=<?= $grund->name ?>><?= $grund->value ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
            </div>
            <div class="w3-half">
                <p>
                    <label for="freilos_saison" class="w3-text-primary">Neues Freilos (Saison)</label>
                    <select class='w3-select w3-border w3-border-primary'
                            name='freilos_saison' id="freilos_saison"
                    >
                        <option selected
                                value=<?= Config::SAISON ?>><?= Html::get_saison_string(Config::SAISON) ?></option>
                        <option value=<?= Config::SAISON - 1 ?>><?= Html::get_saison_string(Config::SAISON - 1) ?></option>
                    </select>
                </p>
            </div>
        </div>
        <div class="w3-container">
            <?php if ($team->getOffeneFreilose()->count() > 0): ?>
                <p>
                    <label for="freilos_delete" class="w3-text-primary">Ungesetztes Freilos löschen</label>
                    <select class='w3-select w3-border w3-border-primary'
                            name='freilos_delete' id="freilos_delete"
                    >
                        <option value="NO_CHANGE">Auswählen zum Löschen</option>
                        <?php /** @var Freilos $freilos */
                        foreach ($team->getOffeneFreilose() as $freilos): ?>
                            <option value=<?= $freilos->id() ?>>
                                <?= $freilos->getGrund()->value ?> | Saison <?= Html::get_saison_string($freilos->getSaison()) ?>
                                | Erstellt <?= $freilos->getErstelltAm()->format("d.m.Y") ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
            <?php else: ?>
                <p class="w3-text-grey">Keine offenen Freilose zum Löschen vorhanden.</p>
            <?php endif; ?>
        </div>
        <p>
            <button type='submit'
                    class='w3-button w3-secondary w3-block'
                    name="change_la"
            >
                <?= Html::icon('create') ?> Teamdaten ändern (nur LA)
            </button>
        </p>
    </form>
</div>
