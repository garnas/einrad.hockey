<?php

use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierSnippets;

?>
<p>Hallo <?= $team->getName() ?>, </p>
<p>
    dein Team ist auf dem Turnier in <?= TurnierSnippets::ortDatumBlock($turnier) ?>
    von der Warteliste auf die Setzliste aufgerückt. Somit spielt ihr auf diesem Turnier!
</p>
<p>
    <a href='<?= TurnierLinks::details($turnier) ?>'>Link zum Turnier</a>
</p>
