<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Spieler\SpielerRepository;
use App\Repository\Team\TeamRepository;
use App\Service\Team\FreilosService;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

require_once '../../logic/la_spieler_waehlen.logic.php';

// Formularauswertung Spielerdaten verändern
if (isset($_POST['spieler_aendern'])) {

    $vorname = $_POST['vorname'];
    $nachname = $_POST['nachname'];
    $jahrgang = (int)$_POST['jahrgang'];
    $geschlecht = $_POST['geschlecht'] === "" ? null : $_POST['geschlecht'];
    $teamname = $_POST['teamname'];
    $letzte_saison = (int)$_POST['letzte_saison'];
    $schiri = (int)$_POST['schiri'];
    $junior = $_POST['junior'] ?? null;
    $team = TeamRepository::get()->findByName($teamname);
    if ($team === null) {
        Html::error("Team nicht gefunden.");
        Html::error("Spieler konnte nicht gespeichert werden. Alle Änderungen bitte erneut eingeben");
        Helper::reload(get: "?spieler_id=" . $spieler->getSpielerId());

    }
    if ($team->id() != $spieler?->getTeam()->id()) {
        $spieler->setTeam($team);
        $spieler->setTimestamp(new DateTime());
    }
    $spieler
        ->setVorname($vorname)
        ->setNachname($nachname)
        ->setJahrgang($jahrgang)
        ->setGeschlecht($geschlecht)
        ->setTeam($team)
        ->setLetzteSaison($letzte_saison)
        ->setSchiri($schiri)
        ->setJunior($junior);
    SpielerRepository::get()->speichern($spieler);
    Html::info("Spielerdaten wurden gespeichert.");
    if ($team !== null && FreilosService::handleSchiriFreilos($team)) {
        TeamRepository::get()->speichern($team);
        Html::info("Schirifreilos erhalten!");
    }
}

//Formularauswertung Spieler löschen
if (isset($_POST['delete_spieler'])) {
    $id = $spieler->getSpielerId(); // Die Spieler-ID ist nach dem löschen nicht mehr in der Doctrine Instanz des Spielers
    SpielerRepository::get()->delete($spieler);
    Html::info("Der Spieler " . $spieler->getVorname() . " " . $spieler->getNachname() . " mit der ID " . $id . " wurde gelöscht.");
    Helper::reload();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php'; ?>

<?php include '../../templates/la_spieler_waehlen.tmp.php'; ?>
<?php if (isset($spieler)): ?>
    <form class="w3-card-4 w3-panel" method='post'>
        <!-- Spieler-Details -->
        <h3>Spieler mit der ID <?= $spieler->getSpielerId() ?> ändern</h3>
        <p>
            <label class="w3-text-primary" for="vorname">Vorname</labeL>
            <input class="w3-input w3-border w3-border-primary"
                   type="text"
                   name="vorname"
                   id="vorname"
                   value="<?= $spieler->getVorname() ?>"
                   autocomplete="off"
                   required
            >
        </p>
        <p>
            <label class="w3-text-primary" for="nachname">Nachname</labeL>
            <input class="w3-input w3-border w3-border-primary"
                   type="text"
                   name="nachname"
                   id="nachname"
                   value="<?= $spieler->getNachname() ?>"
                   autocomplete="off"
                   required>
        </p>
        <p>
            <label class="w3-text-primary" for="jahrgang">Jahrgang</labeL>
            <input class="w3-input w3-border w3-border-primary"
                   type="number"
                   name="jahrgang"
                   id="jahrgang"
                   value="<?= $spieler->getJahrgang() ?>"
                   autocomplete="off"
                   required>
        </p>
        <p>
            <label class="w3-text-primary" for="geschlecht">Geschlecht</labeL>
            <select style="height:40px" class='w3-input w3-border w3-border-primary' id='geschlecht' name='geschlecht'>
                <option <?= ($spieler->getGeschlecht() === 'm') ? 'selected' : '' ?> value='m'>männlich</option>
                <option <?= ($spieler->getGeschlecht() === 'w') ? 'selected' : '' ?> value='w'>weiblich</option>
                <option <?= ($spieler->getGeschlecht() === 'd') ? 'selected' : '' ?> value='d'>divers</option>
                <option <?= ($spieler->getGeschlecht() === null) ? 'selected' : '' ?> value=''>Keine Angabe</option>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="schiri">
                Schiri
            </labeL>
            <select class='w3-input w3-border w3-border-primary' id='schiri' name='schiri'>
                <option <?= ($spieler->getSchiri() === Config::SAISON + 2) ? 'selected' : '' ?>
                        value='<?= Config::SAISON + 2 ?>'>
                    bis inkl. Saison <?= Html::get_saison_string(Config::SAISON + 2) ?>
                </option>
                <option <?= ($spieler->getSchiri() == Config::SAISON + 1) ? 'selected' : '' ?>
                        value='<?= Config::SAISON + 1 ?>'>
                    bis inkl. Saison <?= Html::get_saison_string(Config::SAISON + 1) ?>
                </option>
                <option <?= ($spieler->getSchiri() == Config::SAISON) ? 'selected' : '' ?>
                        value='<?= Config::SAISON ?>'>
                    bis inkl. Saison <?= Html::get_saison_string() ?>
                </option>
                <option <?= (is_null($spieler->getSchiri())) ? 'selected' : '' ?>
                        value=''>kein Schiri
                </option>
            </select>
        </p>
        <p>
            <input class="w3-check"
                   type="checkbox"
                   id="junior"
                   name="junior" <?php if ($spieler->getJunior() === "Ja") { ?> checked <?php } //endif?>
                   value="Ja"
            >
            <label for="junior">
                <span class="w3-text-primary w3-hover-text-secondary" style="cursor: pointer;">
                    <i> Junior-Schiedsrichter</i>
                </span>
            </label>
        </p>
        <p>
            <label class="w3-text-primary" for="teamname">Team ändern</label>
            <input type="text"
                   class="w3-input w3-border w3-border-primary"
                   value="<?= $spieler->getTeam()->getName() ?>"
                   list="teams"
                   id="teamname"
                   name="teamname">
            <?= Html::datalist_teams() ?>
            <?= Html::link("lc_kader.php?team_id=" . $spieler->getTeam()?->id(),
                'Zum Teamkader der ' . $spieler->getTeam()?->getName(),
                true,
                'group') ?>
        </p>
        <p>
            <label class="w3-text-primary" for="letzte_saison">Letzte aktive Saison</label>
            <input class="w3-input w3-border w3-border-primary"
                   type="number"
                   id="letzte_saison"
                   name="letzte_saison"
                   value="<?= $spieler->getLetzteSaison() ?>"
                   autocomplete="off"
                   required>
            <span class="w3-text-grey">
                (Saison Nr. <?= $spieler->getLetzteSaison() ?> entspricht Saison
                <?= Html::get_saison_string($spieler->getLetzteSaison()) ?>)
            </span>
        </p>
        <p>
            <input class="w3-button w3-tertiary w3-block" type="submit" name="spieler_aendern" value="Spieler ändern">
        </p>
    </form>
    <form onsubmit="return confirm(
            'Der Spieler mit der ID <?= $spieler->getSpielerId() ?> (<?= $spieler->getVorname() ?>) wird gelöscht werden.');"
          class="w3-container w3-card-4 w3-panel"
          method="POST">
        <p>
            <input class="w3-button w3-secondary w3-block" type="submit" name="delete_spieler" value="Spieler löschen">
        </p>
    </form>
<?php endif;

include '../../templates/footer.tmp.php';