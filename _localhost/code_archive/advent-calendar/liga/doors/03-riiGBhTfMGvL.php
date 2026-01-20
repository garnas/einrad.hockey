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
    <?php if ($now >= new DateTime('2025-12-03 12:30')): ?>
        
        <div class="slide-container">
            <!-- Fakt 1/5 -->
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/10.jpg" />
            </div>
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/11.jpg" />
            </div>
            <div class="slideshow">
                <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/12.jpg" />
            </div>

            <?php if ($now >= new DateTime('2025-12-03 14:00')): ?>
                <!-- Fakt 2/5 -->
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/13.jpg" />
                </div>
            <?php endif; ?>        

            <?php if ($now >= new DateTime('2025-12-03 15:30')): ?>
                <!-- Fakt 3/5 -->
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/14.jpg" />
                </div>
            <?php endif; ?>
        
            <?php if ($now >= new DateTime('2025-12-03 17:00')): ?>
                <!-- Fakt 4/5 -->
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/15.jpg" />
                </div>
            <?php endif; ?>
        
            <?php if ($now >= new DateTime('2025-12-03 18:30')): ?>
                <!-- Fakt 5/5 -->
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/16.jpg" />
                </div>
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/17.jpg" />
                </div>
            <?php endif; ?>
        
            <?php if ($now >= new DateTime('2025-12-03 23:59')): ?>
                <!-- Lösung -->
                <div class="slideshow">
                    <img class="slide" src="../../../bilder/advent/riiGBhTfMGvL/18.jpg" />
                </div>
            <?php endif; ?>
        </div>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    
    <?php else: ?>
    
        <div class="slide-container"><img class="slide" src="../../../bilder/advent/time1230.jpg" /></div>
    
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