<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "25 Jahre | Deutsche Einradhockeyliga";
$content = "Spielerprofile für das 25. Jubiläum der Deutschen Einradhockeyliga";
include '../../templates/header.tmp.php';
?>

<div class="w3-content">
    <img class="slideshow1" src="../bilder/spielerprofile/Adrian.jpg" style="width:100%">
    <img class="slideshow1" src="../bilder/spielerprofile/Adrian2.jpg" style="width:100%">
    <img class="slideshow1" src="../bilder/spielerprofile/Adrian6.jpg" style="width:100%">
    <img class="slideshow1" src="../bilder/spielerprofile/Adrian7.jpg" style="width:100%">
</div>

<?php include '../../templates/footer.tmp.php';