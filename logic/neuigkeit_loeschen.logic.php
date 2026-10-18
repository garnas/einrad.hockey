<?php

use App\Neuigkeit\Service\NeuigkeitPermission;
use App\Neuigkeit\Repository\NeuigkeitRepository;

$neuigkeiten_id = (int) @$_GET['neuigkeiten_id'];
$neuigkeit = NeuigkeitRepository::get()->findById($neuigkeiten_id);

if (empty($neuigkeit)) {
    Helper::not_found("Neuigkeiteneintrag konnte nicht gefunden werden.");
}

if (!NeuigkeitPermission::canDelete($neuigkeit->getEingetragenVon())) {
    Html::error("Neuigkeit darf nicht gelöscht werden.");
    Helper::reload("/liga/neues.php");
}

NeuigkeitRepository::get()->delete($neuigkeit);
Html::info("Neuigkeit wurde gelöscht");
header('Location: ../liga/neues.php');
