<?php

use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierSnippets;
use App\Repository\Team\TeamRepository;

?>
<p>Hallo <?= TeamRepository::get()->getTeamName($team) ?>, </p>
<p>
    dein Team ist auf dem Turnier in <?= TurnierSnippets::ortDatumBlock($turnier) ?>
    von der Warteliste auf die Setzliste aufgerückt. Somit spielt ihr auf diesem Turnier!
</p>
<p>
    <a href='<?= TurnierLinks::details($turnier) ?>'>Link zum Turnier</a>
</p>
