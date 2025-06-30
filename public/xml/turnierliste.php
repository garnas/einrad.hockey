<?php

use App\Repository\Turnier\TurnierRepository;

require_once '../../init.php';

Helper::$log_user = false; // Keine User-Logs

$turniere = TurnierRepository::getKommendeTurniere()->toArray();
header('Content-type: text/xml');
echo xml::turniereToXml(turniere: $turniere);