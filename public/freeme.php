<?php

// Diese Datei schaltet bei Aufruf die Seite im Wartungsmodus für den jeweiligen Nutzer frei.
require_once '../env.php';

if(!Env::WARTUNGSMODUS) {
    die("Nicht im Wartungsmodus");
}
session_start();

$_SESSION['wartungsmodus'] = true;

?>

<a href='liga/neues.php'>
    <h1 style="text-align: center;">weiter</h1>
</a>