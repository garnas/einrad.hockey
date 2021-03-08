<?php

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php'; // Autoloader und Session, muss immer geladen werden!
# require_once '../../logic/session_team.logic.php'; // Nur im Teamcenter zugreifbar

$regeln = SchiriTest::get_regelwerk();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

Html::$titel = 'Regelwerk der Deutschen Einradhockeyliga';
include '../../templates/header.tmp.php'; // Html-header und Navigation ?>

<!-- <h1 class="w3-text-primary">Einzelne Regeln</h1>
<?php
    [$nummer, $part, $titel, $text] = SchiriTest::get_regel('5.2');
    Html::message('notice', $text, $nummer . $part . ": " . $titel);

    [$nummer, $part, $titel, $text] = SchiriTest::get_regel('5.2b');
    Html::message('notice', $text, $nummer . $part . ": " . $titel);
?>
-->

<h1 class="w3-text-primary">Regelwerk der Deutschen Einradhockeyliga</h1>
<?php
foreach ($regeln as $regel_id => $regel) {
    $text = $regel['regeltext'];
    // Buchstaben für Unterpunkte anzeigen:
    $text = preg_replace('/<p part="([a-z])">/', '<p><b>($1)</b> ', $text);
    Html::message('info', $text, $regel['regelnummer'] . ": " . $regel['regeltitel'],
              esc:false);
}

include '../../templates/footer.tmp.php';
?>
