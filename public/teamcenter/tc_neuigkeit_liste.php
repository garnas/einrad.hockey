<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

Html::notice('Klicke bei deiner Neuigkeit auf den Button "bearbeiten", um deine Neuigkeit zu verändern.');
header("Location: ../liga/neues.php");
die();