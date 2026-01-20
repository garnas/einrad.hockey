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
    <?php if ($now >= new DateTime('2025-12-15 08:00')): ?>
        
        <div class="slide-container">
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/uUV15ZUXRQfd/25.jpg" />
            </div>
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/uUV15ZUXRQfd/26.jpg" />
            </div>
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/uUV15ZUXRQfd/27.jpg" />
            </div>
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/uUV15ZUXRQfd/28.jpg" />
            </div>
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/uUV15ZUXRQfd/29.jpg" />
            </div>
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/uUV15ZUXRQfd/30.jpg" />
            </div>
            
            <?php if ($now >= new DateTime('2025-12-15 23:59')): ?>
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/uUV15ZUXRQfd/31.jpg" />
                </div>
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/uUV15ZUXRQfd/32.jpg" />
                </div>
            <?php endif; ?>
        </div>
        
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    
    <?php else: ?>
    
        <div class="slide-container"><img class="slide" src="../../../bilder/advent/time0800.jpg" /></div>
    
    <?php endif; ?>


<script>
    
    function showDivs(n) {
        var i;
        var x = document.getElementsByClassName('slideshow');
        if (n > x.length) { slideIndex = 1 }
        if (n < 1) { slideIndex = x.length }
        for (i = 0; i < x.length; i++) {
            x[i].style.display = "none";  
        }
        x[slideIndex - 1].style.display = "block";  
    }

    function plusDivs(n) {
        showDivs(slideIndex += n);
    }

    var slideIndex = 1;
    showDivs(slideIndex);

</script>

<?php include '../../../../templates/footer.tmp.php'; ?>