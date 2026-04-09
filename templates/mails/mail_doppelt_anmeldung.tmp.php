<?php

use App\Service\Turnier\TurnierSnippets;
use App\Repository\Team\TeamRepository;

?>

<p>Hallo <?= TeamRepository::get()->getTeamName($team) ?></p>
<p>
    Ihr standet für das <?= TurnierSnippets::ortWochentagDatumBlock($turnier) ?>
    auf der Warteliste. Da ihr jedoch am gleichen Turniertag schon auf die Setzliste eines Turnieres gelost wurdet, seid ihr vom obigen Turnier abgemeldet worden.
</p>