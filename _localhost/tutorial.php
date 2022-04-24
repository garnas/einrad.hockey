<?php
// (1) Die angezeigte Datei kommt in den Public-Ordner und wird vom User aufgerufen.

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// (2) init.php muss immer als Erstes geladen werden.
// Pfad zur init.php muss angepasst werden.
// Diese Datei lädt wichtige PHP-Einstellungen und Sachen wie zB Autoloader der Klassen und Session-Handling
require_once '../init.php';

// (3) Authentifikation
# require_once Env::BASE_PATH . '/logic/session_la.logic.php'; // Zugriff nur mit LA-Login
# require_once Env::BASE_PATH . '/logic/session_team.logic.php'; // Zugriff nur mit Team-Login

// (4) Beispiel-Meldungen, diese werden nach dem Laden der Navigation ausgegeben und vorher in der Session gespeichert.
# Html::info("Alles gut");
# Html::error("Ein Fehler");
# Html::notice("Ein Hinweis");


// (5) Datenbankabfragen sollen in einer *.class.php im classes Ordner getätigt werden. Die Klasse wird
// dann hier aufgerufen.

// (6) Beispiel: Liste aller Teams, welche an den HTML-Code übergeben wird
$teams = Team::get_liste();

// (7) Formularverarbeitung findet hier im Logikteil statt.
if (isset($_POST['mein_formular'])) {
    Html::info("Formular abgesendet. Dein Text: " . $_POST['mein_text']);
    // Weitere Formularverarbeitung
    Helper::reload(); // Lädt das Skript neu nach der Formularverarbeitung.
}

// (8) Debuggen von Arrays:
# db::debug($teams);
# db::debug(Helper::$teamcenter, true);
# db::debug([1.2234, 1, 'hallo', [false, true]], true);


// (9) Beispiel Fehler, welche das Skript beenden und eine Fehlerseite aufrufen.
# trigger_error("Dies ist ein Beispielfehler.", E_USER_ERROR);
# Helper::not_found("Die Teamliste konnte nicht gefunden werden");

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// (10) HTML-Header und Navigation laden. Titel- und Content-Tags ans Template übergeben.
Html::$titel = "Tutorial";
Html::$content = "Dieses Tutorial soll dabei helfen, die Seite zu verstehen.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <!-- (11) Dein HTML-Code: Ab hier PHP nur noch als Templating-Engine verwenden. -->
    <h1 class="w3-text-primary">
        <?= Html::icon("info", tag:"h1") ?> Eine Liste aller Teams
    </h1>
    <p>
        <?php foreach ($teams as $teamname) { ?>
            <?= $teamname // Html-Code in Strings soll vermieden werden ?><br>
        <?php } // end foreach ?>
    </p>
    <p class="w3-text-grey">Ende der Liste</p>

    <!-- (12) Ein Beispielformular -->
    <div class="w3-panel w3-card-4">
        <form method="post">
            <h3>
                <label for="mein_text" class="w3-text-primary">
                    Beispielformular
                </label>
            </h3>
            <input type="text"
                   class="w3-input"
                   name="mein_text"
                   id="mein_text"
                   placeholder="Beispieltext eingeben"
                   required
            >
            <button type="submit" name="mein_formular" class="w3-button w3-primary w3-section">
                <?= Html::icon("create") ?> Formular absenden
            </button>
        </form>
    </div>

    <!-- (13) Ein Beispiellink -->
    <?= Html::link("https://phpdelusions.net/", "Beispiellink", true, 'launch') ?>

<?php
// (14) Einfügen des Footers
include Env::BASE_PATH . '/templates/footer.tmp.php';