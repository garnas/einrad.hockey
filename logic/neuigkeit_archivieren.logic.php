<?php

use App\Service\Neuigkeit\PermissionService;
use App\Repository\Neuigkeit\NeuigkeitRepository;

$neuigkeiten_id = (int) @$_GET['neuigkeiten_id'];
$neuigkeit = NeuigkeitRepository::get()->findById($neuigkeiten_id);

if (empty($neuigkeit)) {
    Helper::not_found("Neuigkeiteneintrag konnte nicht gefunden werden.");
}

if (!PermissionService::canArchive($neuigkeit->getEingetragenVon())) {
    Html::error("Neuigkeit darf nicht archiviert werden.");
    Helper::reload("/liga/neues.php");
}

$neuigkeit->setAktiv(false);
NeuigkeitRepository::get()->update($neuigkeit);
Html::info("Neuigkeit wurde archiviert");
header('Location: ../liga/neues.php');
