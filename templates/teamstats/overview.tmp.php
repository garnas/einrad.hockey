<style>
    .ehl-green {background-color: hsl(154deg, 38%, 58%)}
    .ehl-yellow {background-color: hsl(38deg, 38%, 58%)}
    .ehl-red {background-color: hsl(7deg, 38%, 58%)}
</style>

<?php
    include "gegner.tmp.php";
    if (!is_null($liebling)) include "lieblingsgegner.tmp.php";
    if (!is_null($angst)) include "angstgegner.tmp.php";
    include "turnierergebnisse.tmp.php";
    include "spielergebnisse.tmp.php";
    include "gesamt_verteilung.tmp.php";
    include "schwach_verteilung.tmp.php";
    include "stark_verteilung.tmp.php";