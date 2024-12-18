<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;
use App\Service\Team\TeamService;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

require_once '../../logic/la_spieler_waehlen.logic.php';

if (isset($_GET['spieler_id'])) {

    $spieler = nSpieler::get((int)$_GET['spieler_id']);
    if (!isset($spieler->spieler_id)) {
        Html::error("Spieler wurde nicht gefunden");
    }

}

// Formularauswertung Spielerdaten verändern
if (isset($_POST['spieler_aendern'])) {

    $vorname = $_POST['vorname'];
    $nachname = $_POST['nachname'];
    $jahrgang = (int)$_POST['jahrgang'];
    $geschlecht = $_POST['geschlecht'];
    $teamname = $_POST['teamname'];
    $letzte_saison = (int)$_POST['letzte_saison'];
    $schiri = (int)$_POST['schiri'];
    $junior = (string)($_POST['junior'] ?? null);

    if ($spieler
        ->set_vorname($vorname)
        ->set_nachname($nachname)
        ->set_jahrgang($jahrgang)
        ->set_geschlecht($geschlecht)
        ->set_teamname($teamname)
        ->set_letzte_saison($letzte_saison)
        ->set_schiri($schiri)
        ->set_junior($junior)
        ->speichern()) {

        Html::info("Spielerdaten wurden gespeichert.");
        $team = TeamRepository::get()->team($spieler->team_id);
        if (TeamService::handleSchiriFreilos($team)) {
            TeamRepository::get()->speichern($team);
            Html::info("Schirifreilos erhalten!");
        }
        Helper::reload('/ligacenter/lc_kader.php', '?team_id=' . $spieler->team_id);

    } else {

        Html::error("Spieler konnte nicht gespeichert werden. Alle Änderungen bitte erneut eingeben");
        Helper::reload(get: "?spieler_id=" . $spieler->id());

    }

}

//Formularauswertung Spieler löschen
if (isset($_POST['delete_spieler'])) {
    $spieler->delete();
    Html::info("Der Spieler " . $spieler->get_name() . " mit der ID " . $spieler->id() . " wurde gelöscht.");
    Helper::reload();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php'; ?>

<?php include '../../templates/la_spieler_waehlen.tmp.php'; ?>

<?php if (isset($spieler->spieler_id)) { ?>
    <form class="w3-card-4 w3-panel" method='post'>
        <!-- Spieler-Details -->
        <h3>Spieler mit der ID <?= $spieler->id() ?> ändern</h3>
        <p>
            <label class="w3-text-primary" for="vorname">Vorname</labeL>
            <input class="w3-input w3-border w3-border-primary"
                   type="text"
                   name="vorname"
                   id="vorname"
                   value="<?= $spieler->get_vorname() ?>"
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
                   value="<?= $spieler->get_nachname() ?>"
                   autocomplete="off"
                   required>
        </p>
        <p>
            <label class="w3-text-primary" for="jahrgang">Jahrgang</labeL>
            <input class="w3-input w3-border w3-border-primary"
                   type="number"
                   name="jahrgang"
                   id="jahrgang"
                   value="<?= $spieler->get_jahrgang() ?>"
                   autocomplete="off"
                   required>
        </p>
        <p>
            <label class="w3-text-primary" for="geschlecht">Geschlecht</labeL>
            <select style="height:40px" class='w3-input w3-border w3-border-primary' id='geschlecht' name='geschlecht'>
                <option <?= ($spieler->geschlecht === 'm') ? 'selected' : '' ?> value='m'>männlich</option>
                <option <?= ($spieler->geschlecht === 'w') ? 'selected' : '' ?> value='w'>weiblich</option>
                <option <?= ($spieler->geschlecht === 'd') ? 'selected' : '' ?> value='d'>divers</option>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="schiri">
                Schiri
            </labeL>
            <select class='w3-input w3-border w3-border-primary' id='schiri' name='schiri'>
                <option <?= ($spieler->schiri === Config::SAISON + 2) ? 'selected' : '' ?>
                        value='<?= Config::SAISON + 2 ?>'>
                    bis inkl. Saison <?= Html::get_saison_string(Config::SAISON + 2) ?>
                </option>
                <option <?= ($spieler->schiri == Config::SAISON + 1) ? 'selected' : '' ?>
                        value='<?= Config::SAISON + 1 ?>'>
                    bis inkl. Saison <?= Html::get_saison_string(Config::SAISON + 1) ?>
                </option>
                <option <?= ($spieler->schiri == Config::SAISON) ? 'selected' : '' ?>
                        value='<?= Config::SAISON ?>'>
                    bis inkl. Saison <?= Html::get_saison_string() ?>
                </option>
                <option <?= (is_null($spieler->schiri)) ? 'selected' : '' ?>
                        value=''>kein Schiri
                </option>
            </select>
        </p>
        <p>
            <input class="w3-check"
                   type="checkbox"
                   id="junior"
                   name="junior" <?php if ($spieler->junior === "Ja") { ?> checked <?php } //endif?>
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
                   value="<?= $spieler->get_team() ?>"
                   list="teams"
                   id="teamname"
                   name="teamname">
            <?= Html::datalist_teams() ?>
            <?= Html::link("lc_kader.php?team_id=" . $spieler->team_id,
                'Zum Teamkader der ' . $spieler->get_team(),
                true,
                'group') ?>
        </p>
        <p>
            <label class="w3-text-primary" for="letzte_saison">Letzte aktive Saison</label>
            <input class="w3-input w3-border w3-border-primary"
                   type="number"
                   id="letzte_saison"
                   name="letzte_saison"
                   value="<?= $spieler->letzte_saison ?>"
                   autocomplete="off"
                   required>
            <span class="w3-text-grey">
                (Saison Nr. <?= $spieler->letzte_saison ?> entspricht Saison
                <?= $spieler->get_letzte_saison() ?>)
            </span>
        </p>
        <p>
            <input class="w3-button w3-tertiary w3-block" type="submit" name="spieler_aendern" value="Spieler ändern">
        </p>
    </form>
    <form onsubmit="return confirm(
            'Der Spieler mit der ID <?= $spieler->id() ?> <?= $spieler->get_name() ?>) wird gelöscht werden.');"
          class="w3-container w3-card-4 w3-panel"
          method="POST">
        <p>
            <input class="w3-button w3-secondary w3-block" type="submit" name="delete_spieler" value="Spieler löschen">
        </p>
    </form>
<?php } //Ende IF

include '../../templates/footer.tmp.php';