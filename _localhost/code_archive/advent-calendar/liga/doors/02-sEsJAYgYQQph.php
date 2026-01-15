<?php

// Logik
require_once '../../../../init.php';
$now = new DateTime();

// Layout
Html::$titel = "Adventskalender | Deutsche Einradhockeyliga";
Html::$content = "Adventskalender der Deutschen Einradhockeyliga für das Jahr 2025.";

include '../../../../templates/header.tmp.php'; ?>

<link type="text/css" rel="stylesheet" href="../style.css">
<link type="text/css" rel="stylesheet" href="../colors.css">

<div class="w3-display-container advent-color w3-round-xlarge w3-padding-16" style="margin-top: 16px;">
    <?php if ($now >= new DateTime('2025-12-02 18:00')): ?>
      <div class="slide-container">
        <div class="slideshow">
            <img class="slide" src="../../../bilder/advent/sEsJAYgYQQph/5.jpg" />
        </div>
        <div class="slideshow">
            <img class="slide" src="../../../bilder/advent/sEsJAYgYQQph/6.jpg" />
        </div>
        <div class="slideshow">
            <img class="slide" src="../../../bilder/advent/sEsJAYgYQQph/7.jpg" />
        </div>
        <?php if ($now > new DateTime('2025-12-03')):?>
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/sEsJAYgYQQph/9.jpg" />
            </div>
        <?php endif; ?>
      </div>
      <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
      <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    <?php else: ?>
      <div class="slide-container">
            <img class="slide" src="../../../bilder/advent/time1800.jpg" />
      </div>
    <?php endif; ?>
</div>

<!-- Script für die Slideshow -->
<script>
var slideIndex = [1, 1, 1, 1, 1];
showDivs(1,0);
showDivs(1,1);
showDivs(1,2);
showDivs(1,3);
showDivs(1,4);

function plusDivs(n, no) {
  showDivs(slideIndex[no] += n, no);
}

function showDivs(n, no) {
  var i;
  var x = document.getElementsByClassName('slideshow');
  if (n > x.length) {slideIndex[no] = 1}
  if (n < 1) {slideIndex[no] = x.length}
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  x[slideIndex[no]-1].style.display = "block";  
}
</script>

<?php include '../../../../templates/footer.tmp.php'; ?>