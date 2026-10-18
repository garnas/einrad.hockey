<?php

use App\Turnier\Service\TurnierLinks;
use App\Turnier\Service\TurnierSnippets;

?>
<p>Hallo <?= $team->getName() ?>,</p>
<p>
    es wurde ein neues Turnier eingetragen, für welches ihr euch anmelden könnt:
    <?= TurnierSnippets::ortDatumBlock($turnier) ?>
</p>
<p>
    <a href='<?= TurnierLinks::details($turnier) ?>'>
        Link zum Turnier
    </a>
</p>