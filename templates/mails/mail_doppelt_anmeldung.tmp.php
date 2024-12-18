<?php
/** @var \App\Entity\Team\nTeam $team */
/** @var App\Event\Turnier\ $turnier */
?>

<p>Hallo <?= $team->getName() ?></p>
<p>
    Ihr standet für das <?= \App\Service\Turnier\TurnierSnippets::ortWochentagDatumBlock($turnier) ?>
    auf der Warteliste. Da ihr jedoch am gleichen Turniertag schon auf die Setzliste eines Turnieres gelost wurdet, seid ihr vom obigen Turnier abgemeldet worden.
</p>