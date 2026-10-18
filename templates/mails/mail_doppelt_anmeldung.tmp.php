<?php
use App\Turnier\Service\TurnierSnippets;

?>

<p>Hallo <?= $team->getName() ?></p>
<p>
    Ihr standet für das <?= TurnierSnippets::ortWochentagDatumBlock($turnier) ?>
    auf der Warteliste. Da ihr jedoch am gleichen Turniertag schon auf die Setzliste eines Turnieres gelost wurdet, seid ihr vom obigen Turnier abgemeldet worden.
</p>