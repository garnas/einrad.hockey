<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

//Assoziatives Array aller Turnieranmeldungen der Aktuellen Saison
$turnieranmeldungen = Turnier::get_all_anmeldungen();

//db::debug wird verwendet fürs debugging. Es zeigt immer den Inhalt von Variablen oben auf der aufgerufenen Seite auf.
db::debug($turnieranmeldungen);


//Datum-Umwandlung auf Deutsch:
//strtotime erstellt aus einem String eine Unix-Zeit
$datum = strftime("%A, %d.%m.%Y %H:%M&nbsp;Uhr", strtotime("2020-11-12"));

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>
<h1>Oben werden Beispiel Arrays dargestellt</h1>
<p>Beispieldatum: <?=$datum?></p>

<?php include '../../templates/footer.tmp.php';