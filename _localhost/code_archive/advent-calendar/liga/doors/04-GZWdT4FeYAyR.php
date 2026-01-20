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
    <?php if ($now >= new DateTime('2025-12-04 08:00')): ?>
        
        <div class="slide-container">
            <!-- Slide 1 -->
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/GZWdT4FeYAyR/19.jpg" />
            </div>
            
            <!-- Slide 2 -->
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/GZWdT4FeYAyR/20.jpg" />
            </div>

            <!-- Slide 3 -->
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/GZWdT4FeYAyR/21.jpg" />
            </div>

            <!-- Slide 4 -->
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/GZWdT4FeYAyR/22.jpg" />
            </div>
            
            <?php if ($now >= new DateTime('2025-12-04 22:10')): ?>
                <!-- Slide 5 - Loesung -->
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/GZWdT4FeYAyR/23.jpg" />
                </div>
            <?php endif; ?>
        </div>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    
    <?php else: ?>
    
        <div class="slide-container">
            <img class="slide" src="../../../bilder/advent/time0800.jpg" />
        </div>
    
    <?php endif; ?>
</div>

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