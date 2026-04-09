<?php

use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierSnippets;
use App\Repository\Team\TeamRepository;

?>
<p>Hallo <?= TeamRepository::get()->getTeamName($team) ?>,</p>
<p>
    es wurde ein neues Turnier eingetragen, für welches ihr euch anmelden könnt:
    <?= TurnierSnippets::ortDatumBlock($turnier) ?>
</p>
<p>
    <a href='<?= TurnierLinks::details($turnier) ?>'>
        Link zum Turnier
    </a>
</p>