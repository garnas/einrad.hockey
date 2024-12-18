<?php

use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierSnippets;

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