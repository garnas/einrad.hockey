<?php
// Diese Datei schaltet bei Aufruf die Seite im Wartungsmodus für den jeweiligen Nutzer frei.
session_start();
$_SESSION['wartungsmodus'] = true;
?>
<a href='liga/neues.php'>weiter</a>