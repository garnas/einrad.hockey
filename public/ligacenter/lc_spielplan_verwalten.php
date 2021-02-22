<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

//Turnierklasse erstellen
$turnier_id = (int)$_GET['turnier_id'];
$turnier = new Turnier($turnier_id);

//Existiert das Turnier?
if (empty($turnier->details)) {
    Form::error("Turnier wurde nicht gefunden");
    header('Location: lc_turnierliste.php');
    die();
}

//Vorhandenes Ergebnis anzeigen
$teamliste = $turnier->get_liste_spielplan();
$turnier_ergebnis = $turnier->get_ergebnis();

//Ergebnis löschen
if (isset($_POST['ergebnis_loeschen'])) {
    $turnier->delete_ergebnis();
    $turnier->set_phase('spielplan');
    Form::info("Ergebnis wurde gelöscht. Das Turnier wurde in die Spielplanphase versetzt.");
    header("Location: lc_spielplan_verwalten.php?turnier_id=" . $turnier->details['turnier_id']);
    die();
}

//Ergebnis eintragen
if (isset($_POST['ergebnis_eintragen'])) {
    if (!Tabelle::check_ergebnis_eintragbar($turnier)) {
        Form::error("Turnierergebnis wurde nicht eingetragen");
        $error = true;
    }
    if (count(array_unique($_POST['team_id'])) != count($_POST['team_id'])) {
        Form::error("Es wurden Teams doppelt eingetragen!");
        $error = true;
    }
    $anzahl_teams = count($teamliste);
    for ($platz = 1; $platz <= $anzahl_teams; $platz++) {
        if (empty($_POST['team_id'][$platz]) || empty($_POST['ergebnis'][$platz])) {
            $error = true;
            Form::error("Formular wurde unvollständig übermittelt");
            break;
        }
    }
    if ($error ?? false) {
        header("Location: lc_spielplan_verwalten.php?turnier_id=" . $turnier->id);
        die();
    }
    // Kein Fehler
    $turnier->delete_ergebnis();
    for ($platz = 1; $platz <= $anzahl_teams; $platz++) {
        $turnier->set_ergebnis($_POST['team_id'][$platz], $_POST['ergebnis'][$platz], $platz);
    }
    $turnier->set_phase('ergebnis');
    Form::info("Ergebnisse wurden manuell eingetragen. Das Turnier wurde in die Ergebnisphase versetzt.");
    header("Location: lc_spielplan_verwalten.php?turnier_id=" . $turnier->details['turnier_id']);
    die();
}

// Spielplan automatisch erstellen
if (isset($_POST['auto_spielplan_erstellen'])) {
    $error = false;
    if ($turnier->details['phase'] != "melde") {
        Form::error("Das Turnier muss in der Meldephase sein.");
        $error = true;
    }
    if (3 > count($teamliste) or count($teamliste) > 7) {
        Form::error("Falsche Anzahl an Teams. Nur 4er - 7er Jeder-gegen-Jeden Spielpläne können erstellt werden.");
        $error = true;
    }
    if (!empty($turnier->details['spielplan_link'])) {
        Form::error("Spielplan konnte nicht erstellt werden. Es existiert ein manuell hochgeladener Spielplan.");
        $error = true;
    }
    if (!$error) {
        if (Spielplan::set_spielplan($turnier)){
            Form::info("Das Turnier wurde in die Spielplan-Phase versetzt. Der Spielplan wird jetzt angezeigt.");
        } else {
            Form::error("Spielplan konnte nicht erstellt werden.");
        }
        header('Location: ../liga/spielplan.php?turnier_id=' . $turnier->id);
        die();
    }
}

//Spielplan löschen
if (isset($_POST['auto_spielplan_loeschen'])) {
    Spielplan::delete_spielplan($turnier);
    Form::info("Der dynamisch erstellte Spielplan wurde gelöscht. Das Turnier wurde in die Meldephase versetzt!");
    header('Location:' . dbi::escape($_SERVER['REQUEST_URI']));
    die();
}

// Spielplan oder Ergebnis manuell hochladen
if (isset($_POST['spielplan_hochladen'])) {
    if (Spielplan::check_exist($turnier->id)) {
        $error = true;
        Form::error("Hochladen nicht möglich. Es existiert bereits ein dynamisch erstellter Spielplan.");
    }
    if (!empty($_FILES["spielplan_file"]["tmp_name"])) {
        $target_dir = "../uploads/s/spielplan/";
        // PDF wird hochgeladen, target_file_pdf = false, falls fehlgeschlagen.
        $target_file_pdf = Neuigkeit::upload_dokument($_FILES["spielplan_file"], $target_dir);
        if ($target_file_pdf === false) {
            Form::error("Fehler beim Upload");
        } else {
            if ($_POST['sp_or_erg'] === 'ergebnis') {
                $turnier->upload_spielplan($target_file_pdf, 'ergebnis');
                Form::notice("Manueller Spielplan hochgeladen. Das Turnier wurde in die Ergebnis-Phase versetzt.");
            } else {
                $turnier->upload_spielplan($target_file_pdf, 'spielplan');
                Form::notice("Manueller Spielplan hochgeladen. Das Turnier wurde in die Spielplan-Phase versetzt.");
            }
            header("Location: lc_spielplan_verwalten.php?turnier_id=$turnier->id" );
            die();
        }
    } else {
        Form::error("Es wurde kein Spielplan gefunden");
    }
}

// Spielplan löschen
if (isset($_POST['spielplan_delete'])) {
    $turnier->upload_spielplan('', 'melde');
    Form::info("Spielplan- / Ergebnisdatei wurde gelöscht. Turnier wurde in die Meldephase versetzt.");
    header("Location: lc_spielplan_verwalten.php?turnier_id=$turnier->id");
    die();
}

// Hinweis Finalturniere-Ergebnis
if ($turnier->details['art'] === 'final') {
    Form::notice("Beim Eintragen von Finalturnieren kann eine beliebige Punktzahl eingeben werden.");
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

include '../../templates/header.tmp.php';
?>

    <!-- Überschrift -->
    <h2 class="w3-text-primary">
        <span class="w3-text-grey">Spielplan/Ergebnis</span>
        <br>
        <?= $turnier->details['datum'] ?> <?= $turnier->details['tname'] ?> <?= $turnier->details['ort'] ?>
        (<?= $turnier->details['tblock'] ?>)
        <br>
        <span class="w3-text-secondary">(<?= $turnier->details['phase'] ?>)</span>
    </h2>

    <!-- Teamliste -->
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <thead class="w3-primary">
            <tr>
                <th>Team ID</th>
                <th>Teamname</th>
                <th class="w3-center">Teamblock</th>
                <th class="w3-center">Wertigkeit</th>
            </tr>
            </thead>
            <?php foreach ($teamliste as $team) { ?>
                <tr>
                    <td><?= $team['team_id'] ?></td>
                    <td><?= $team['teamname'] ?></td>
                    <td class="w3-center"><?= $team['tblock'] ?: 'NL' ?></td>
                    <td class="w3-center"><?= $team['wertigkeit'] ?: 'Siehe Modus' ?></td>
                </tr>
            <?php } //end foreach?>
        </table>
    </div>

    <!-- Dynamischer Spielplan erstellen -->
    <h2 class="w3-text-primary w3-bottombar">Automatischen Spielplan erstellen</h2>

    <?php if (empty($turnier->details['link_spielplan'])) { ?>
        <form method="post">
            <?php if (Spielplan::check_exist($turnier->id)) { ?>
                <p>
                    <input type="submit" name="auto_spielplan_loeschen" value="Dynamischen Spielplan löschen"
                           class="w3-button w3-secondary">
                </p>
            <?php } else { ?>
                <p>
                    <input type="submit" name="auto_spielplan_erstellen" value="Dynamischen Spielplan erstellen"
                           class="w3-button w3-tertiary">
                </p>
            <?php } // endif ?>
        </form>
    <?php } else { ?>
        <p>Bitte zuerst den manuell hochgeladenen Spielplan löschen.</p>
    <?php } // endif ?>

    <!-- Manuellen Spielplan hochladen -->
    <h2 class="w3-text-primary w3-bottombar">oder PDF- oder XLSX-Spielplan hochladen</h2>

    <form method="post" enctype="multipart/form-data">

        <?php if (!Spielplan::check_exist($turnier->id)) { ?>

            <?php if (empty($turnier->details['link_spielplan'])) { ?>
                <p class="w3-text-grey">Nur .pdf oder .xlsx Format</p>
                <p>
                    <input required type="file" name="spielplan_file" id="spielplan_file" class="w3-button w3-tertiary">
                </p>
                <p>
                    <label class="w3-text-grey" for="sp_or_erg">Spielplan oder Ergebnis?</label><br>
                    <select required id="sp_or_erg" name="sp_or_erg" class="w3-select w3-border w3-border-primary"
                            style="max-width: 200px">
                        <option selected disabled>Bitte wählen</option>
                        <option value="spielplan">Spielplan</option>
                        <option value="ergebnis">Ergebnis</option>
                    </select>
                </p>
                <p>
                    <input type="submit" name="spielplan_hochladen" value="Upload" class="w3-button w3-secondary">
                </p>
            <?php } //end if?>

        <?php } else { ?>
            <p>Bitte zuerst dynamischen Spielplan löschen.</p>
        <?php } //end if?>

        <?php if (!empty($turnier->details['link_spielplan'])) { ?>
            <p>
                <?= Form::link($turnier->details['link_spielplan'], 'Spielplan/Ergebnis herunterladen', true); ?>
            </p>
            <p>
                <input type="submit" name="spielplan_delete" value="Vorhandene Spielplandatei löschen"
                       class="w3-button w3-secondary">
            </p>
        <?php } //end if?>

    </form>

    <!-- Ergebnisse eintragen -->
    <h2 class="w3-bottombar w3-text-primary">Ergebnisse manuell eintragen</h2>
    <form method="post">
        <table class="w3-table w3-striped">
            <thead class="w3-primary">
            <tr>
                <th>#</th>
                <th>Teamname</th>
                <th>Turnierergebnis</th>
            </tr>
            </thead>
            <?php for ($platz = 1; $platz <= count($teamliste); $platz++) { ?>
                <tr>
                    <td><?= $platz ?></td>
                    <td>
                        <select required class="w3-select w3-border w3-border-primary" name="team_id[<?= $platz ?>]">
                            <option disabled <?= ($turnier->details['phase'] == "ergebnis") ?: 'selected' ?>>
                                Bitte wählen
                            </option>
                            <?php foreach ($teamliste as $team_id => $team) { ?>
                                <option
                                    <?php if (($turnier_ergebnis[$platz]['team_id'] ?? 0) == $team['team_id']){ ?>selected<?php } //endif?>
                                    value="<?= $team['team_id'] ?>"><?= $team['teamname'] ?></option>
                            <?php } //end foreach?>
                        </select>
                    </td>
                    <td style="width: 30px">
                        <input type="number"
                               required
                               class="w3-input w3-border-primary w3-border"
                               value="<?= $turnier_ergebnis[$platz]['ergebnis'] ?? '' ?>"
                               name="ergebnis[<?= $platz ?>]"
                        >
                    </td>
                </tr>
            <?php } //end foreach?>
        </table>
        <p>
            <input type="submit"
                   name="ergebnis_eintragen"
                   value="Ergebnis eintragen"
                   class="w3-button w3-tertiary"
            >
        </p>
        <p>
            <input type="submit"
                   name="ergebnis_loeschen"
                   value="Ergebnis löschen"
                    <?= (empty($turnier_ergebnis) ? 'disabled' : '')?>
                   class="w3-button w3-secondary"
            >
        </p>
    </form>

<?php include '../../templates/footer.tmp.php';