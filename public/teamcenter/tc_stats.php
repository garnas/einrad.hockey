<?php
use App\Service\Team\TeamstatsAndersEingebunden; // Lädt die Klasse TeamstatsAndersEingebunden
// In der Klasse TeamstatsAndersEingebunden ist der namespace deklariert,
// um die Klasse via Composer laden zu können!

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

// Via Composer Autoloader eingebunden (best practice)
// Siehe Zeile 2 oben für use statement
// und composer.json autoload in Zeile 13
echo TeamstatsAndersEingebunden::exampleMethod();

$team_id = $_SESSION['logins']['team']['id'];

// Via selbstgeschriebenen Classloader eingebunden (antipattern, aber einfach)
// Daher ist auch das .class notwendig im Dateinamen der Klasse...
// Siehe init.php Zeile spl_autoload_register Zeile 60
$spiele = Teamstats::get_anzahl_spiele($team_id);
db::debug($spiele);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include Env::BASE_PATH . '/templates/header.tmp.php';


var_dump($spiele);
include '../../templates/footer.tmp.php';