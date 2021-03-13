<?php 
// Weiterleitung auf Landingpage
include '../init.php';
header("Location: " . Env::BASE_URL . "/liga/neues.php");
die();