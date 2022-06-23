<?php

use App\Service\Turnier\TurnierSnippets;

?>
<p>Hallo <?= $team->getName() ?>, </p>
<p>
    dein Team ist auf dem Turnier in <?= TurnierSnippets::ortDatumBlock($turnier) ?>
    von der Warteliste auf die <b>Setzliste</b> aufgerückt. Somit spielt ihr auf diesem Turnier!
</p>
<p>
    <a href='<?= TurnierLinks::details($turnier) ?>'>Link zum Turnier</a>
</p>
