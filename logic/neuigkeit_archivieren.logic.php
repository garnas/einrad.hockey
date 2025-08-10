<?php

$neuigkeiten_id = (int) @$_GET['neuigkeiten_id'];
$neuigkeit = Neuigkeit::get_neuigkeit_by_id($neuigkeiten_id);

if (empty($neuigkeit)) {
    Helper::not_found("Neuigkeiteneintrag konnte nicht gefunden werden.");
}

if (!Neuigkeit::darf_archivieren($neuigkeit['eingetragen_von'])) {
    Html::error("Neuigkeit darf nicht archiviert werden.");
    Helper::reload("/liga/neues.php");
}

Neuigkeit::archive($neuigkeiten_id);
Html::info("Neuigkeit wurde archiviert");
header('Location: ../liga/neues.php');
