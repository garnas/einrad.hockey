<?php
// (1) Diese Datei kommt in den Public-Ordner und wird vom User aufgerufen.

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// (2) first.logic muss immer als Erstes geladen werden.
// Pfad der first.logic.php im logic Ordner muss angepasst werden.
require_once 'logic/first.logic.php'; //autoloader und Session

// (3) Authentifikation
# require_once Env::BASE_PATH . '/logic/session_la.logic.php'; // Zugriff nur mit LA-Login
# require_once Env::BASE_PATH . '/logic/session_team.logic.php'; // Zugriff nur mit Team-Login

// (4) Beispiel-Meldungen, diese werden nach dem Laden der Navigation ausgegeben.
#Form::info("Alles gut");
#Form::error("Ein Fehler");
#Form::notice("Ein Hinweis");


// (5) Datenbankabfragen sollen in einer .class.php im classes Ordner getätigt werden. Die Klasse wird
// dann hier aufgerufen.

// (6) Beispiel: Liste aller Teams wird an den HTML-Code übergeben
$teams = Team::get_liste(); // $teams wird jetzt ans Layout übergeben.

// (7) Formularverarbeitung findet hier statt.
if (isset($_POST['mein_formular'])) {
    Form::info("Formular abgesendet. Dein Text: " . $_POST['mein_text']);
}

// (8) Debuggen von Arrays:
#dbi::debug($teams);
#dbi::debug(Config::$teamcenter, true);
#dbi::debug([1.2234, 1, 'hallo', [false, true]], true);


// (9) Beispiel Fehler, welcher das Skript beendet und eine Fehlerseite aufruft.
#trigger_error("Dies ist ein Beispielfehler.", E_USER_ERROR);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// (10) HTML-Header und Navigation laden. Titel- und Content-Tags ans Template übergeben.
Config::$titel = "Tutorial";
Config::$content = "Dieses Tutorial soll dabei helfen, die Seite zu verstehen.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <!-- (11) Dein HTML-Code: Ab hier PHP nur noch als Templating-Engine verwenden. -->
    <h1 class="w3-text-primary">
        <?= Form::icon("list", tag:"h1") ?> Eine Liste aller Teams
    </h1>
    <p>
        <?php foreach ($teams as $teamname) { ?>
            <?= $teamname ?><br>
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
                <?= Form::icon("create") ?> Formular absenden
            </button>
        </form>
    </div>

    <!-- (13) Ein Beispiellink -->
    <?= Form::link("https://www.google.de", "Beispiellink", true, 'launch') ?>

<?php
// (14) Einfügen des Footers
include Env::BASE_PATH . '/templates/footer.tmp.php';