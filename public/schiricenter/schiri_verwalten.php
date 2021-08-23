<?php # -*- php -*-

require_once '../../init.php'; # Autoloader und Session, muss immer geladen werden!
require_once '../../logic/session_la.logic.php'; # Auth

require_once '../../logic/la_spieler_waehlen.logic.php';

if (isset($_GET['spieler_id'])) {
    $spieler = nSpieler::get((int)$_GET['spieler_id']);
    if  (!isset($spieler->spieler_id)) {
        Html::error("Spieler wurde nicht gefunden");
    }
}

if (isset($_POST['create'])) {
    $email = $_POST['email'];
    $level = $_POST['level'];

    $test = new SchiriTest();
    $test->set_spieler($spieler)
        ->set_level($level)
        ->set_pruefungs_fragen();

    if ($test->create()) {
        $test->mail_on_create();
        Html::info("Der Test wurde in die Datenbank eingetragen.");
        Helper::reload();
    } else {
        Html::error("Test konnte nicht erstellt werden");
    }
    db::debug($test);

}
##############################################################################


include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<h1 class="w3-text-primary">Schiritest verwalten</h1>

<?php include Env::BASE_PATH . '/templates/la_spieler_waehlen.tmp.php'; ?>

<?php if (isset($spieler->spieler_id)): ?>
    <form method="post">
        <h2 class="w3-text-grey">Prüfling</h2>
        <h2 class="w3-text-secondary"><?= $spieler->get_name() ?> | <?= $spieler->get_team() ?></h2>

        <!-- Level -->
        <p>
            <label for="level">Level wählen</label>
            <select required id="level" name="level" class="w3-select w3-border w3-border-primary">
                <option value="junior">Juniortest</option>
                <option value="basis" selected>Basistest</option>
                <option value="fortgeschritten">Fortgeschritten</option>
            </select>
        </p>

        <!-- E-Mail -->
        <p>
            <label for="email">E-Mail-Adresse des Spielers</label>
            <input required type="email" name="email" id="email" class="w3-input w3-border w3-border-primary">
        </p>

        <p>
            <button type="submit" class="w3-button w3-tertiary" name="create">Schiritest erstellen</button>
        </p>
    </form>
<?php endif; ?>

<?php include Env::BASE_PATH . '/templates/footer.tmp.php';