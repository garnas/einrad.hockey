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

<div class="w3-container w3-card-4 w3-primary w3-margin w3-round">
  <div class="w3-third w3-padding">
    <div class="w3-display-container w3-round w3-card" style="border: 1px solid white;">
      <img class="slideshow1 w3-round" src="../bilder/spielerprofile/Adrian.jpg" style="width:100%">
      <img class="slideshow1 w3-round" src="../bilder/spielerprofile/Adrian2.jpg" style="width:100%">
      <img class="slideshow1 w3-round" src="../bilder/spielerprofile/Adrian6.jpg" style="width:100%">
      <img class="slideshow1 w3-round" src="../bilder/spielerprofile/Adrian7.jpg" style="width:100%">
      <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
      <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
  </div>
  <div class="w3-twothird">
    <p class="w3c-margin">Das ist ein Text.</p>
  </div>
</div>


<div class="w3-container w3-card-4 w3-primary w3-margin w3-round">
  <div class="w3-twothird w3-right-align">
    <p>Das ist ein Text.</p>
  </div>
  <div class="w3-third w3-padding">
    <div class="w3-display-container w3-card w3-round" style="border: 1px solid white;">
      <img class="slideshow2 w3-round" src="../bilder/spielerprofile/Adrian.jpg" style="width:100%">
      <img class="slideshow2 w3-round" src="../bilder/spielerprofile/Adrian2.jpg" style="width:100%">
      <img class="slideshow2 w3-round" src="../bilder/spielerprofile/Adrian6.jpg" style="width:100%">
      <img class="slideshow2 w3-round" src="../bilder/spielerprofile/Adrian7.jpg" style="width:100%">
      <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 1)">&#10094;</button>
      <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 1)">&#10095;</button>
    </div>
  </div>
</div>

<div class="w3-container w3-card-4 w3-primary w3-margin w3-round">
  <div class="w3-third w3-padding">
    <div class="w3-display-container w3-card w3-round" style="border: 1px solid white;">
      <img class="slideshow3 w3-round" src="../bilder/spielerprofile/Adrian.jpg" style="width:100%">
      <img class="slideshow3 w3-round" src="../bilder/spielerprofile/Adrian2.jpg" style="width:100%">
      <img class="slideshow3 w3-round" src="../bilder/spielerprofile/Adrian6.jpg" style="width:100%">
      <img class="slideshow3 w3-round" src="../bilder/spielerprofile/Adrian7.jpg" style="width:100%">
      <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 2)">&#10094;</button>
      <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 2)">&#10095;</button>
    </div>
  </div>
  <div class="w3-twothird">
    <p>Das ist ein Text.</p>
  </div>
</div>

<script>
var slideIndex = [1,1,1];
var slideId = ["slideshow1","slideshow2","slideshow3"];
showDivs(1,0);
showDivs(1,1);
showDivs(1,2);

function plusDivs(n, no) {
  showDivs(slideIndex[no] += n, no);
}

function showDivs(n, no) {
  var i;
  var x = document.getElementsByClassName(slideId[no]);
  if (n > x.length) {slideIndex[no] = 1}
  if (n < 1) {slideIndex[no] = x.length}
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  x[slideIndex[no]-1].style.display = "block";  
}
</script>


<?php include '../../templates/footer.tmp.php';