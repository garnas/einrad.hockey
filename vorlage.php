<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

// Pfad muss angepasst werden
require_once 'logic/first.logic.php'; //autoloader und Session
// require_once '../../logic/session_la.logic.php'; // Ligaausschusslogin erforderlich
// require_once '../../logic/session_team.logic.php'; // Teamlogin erforderlich

// Beispiel-Meldungen
Form::info("Alles gut");
Form::error("Ein Fehler");
Form::notice("Ein Hinweis");

// Data für Html-Code
$teams = Team::get_liste();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<h1 class="w3-text-primary">Eine Liste aller Teams</h1>

<?php foreach ($teams as $teamname){ ?>
    <p><?= $teamname ?></p>
<?php } // end foreach ?>

<p class="w3-text-grey">Ende der Liste</p>

<?php include Env::BASE_PATH . '/templates/footer.tmp.php';