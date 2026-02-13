<?php

use App\Service\Neuigkeit\PermissionService;
use App\Repository\Neuigkeit\NeuigkeitRepository;

$neuigkeiten_id = (int) @$_GET['neuigkeiten_id'];
$neuigkeit = NeuigkeitRepository::get()->findById($neuigkeiten_id);

if (empty($neuigkeit)) {
    Helper::not_found("Neuigkeiteneintrag konnte nicht gefunden werden.");
}

if (!PermissionService::canDelete($neuigkeit->getEingetragenVon())) {
    Html::error("Neuigkeit darf nicht gelöscht werden.");
    Helper::reload("/liga/neues.php");
}

NeuigkeitRepository::get()->delete($neuigkeit);
Html::info("Neuigkeit wurde gelöscht");
header('Location: ../liga/neues.php');
