<?php

use App\Service\Turnier\TurnierSnippets;
use App\Service\Turnier\TurnierLinks;

?>
<p>Hallo <?= $team->getName() ?>, </p>
<p>
    dein Team ist auf dem Turnier in <?= TurnierSnippets::ortDatumBlock($turnier) ?>
    von der Warteliste auf die Setzliste aufgerückt. Somit spielt ihr auf diesem Turnier!
</p>
<p>
    <a href='<?= TurnierLinks::details($turnier) ?>'>Link zum Turnier</a>
</p>
