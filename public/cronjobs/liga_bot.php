<?php
/////////////////////////////////////////////////////////////////////////////
///////////////////////////////CRONJOB LIGABOT///////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

use App\Event\Turnier\nLigaBot;

$_SESSION['logins']['cronjob'] = 'Cronjob';

nLigaBot::ligaBot();

session_destroy();