<?php

$neuigkeiten_id = (int) @$_GET['neuigkeiten_id'];
$neuigkeit = Neuigkeit::get_neuigkeit_by_id($neuigkeiten_id);

if (empty($neuigkeit)) {
    Helper::not_found("Neuigkeiteneintrag konnte nicht gefunden werden.");
}

if (!Neuigkeit::darf_loeschen($neuigkeit['eingetragen_von'])) {
    Html::error("Neuigkeit darf nicht gelöscht werden.");
    Helper::reload("/liga/neues.php");
}

Neuigkeit::delete($neuigkeiten_id);
Html::info("Neuigkeit wurde gelöscht");
header('Location: ../liga/neues.php');
