<?php

use App\Service\Turnier\TurnierSnippets;

?>
<p>Hallo <?= $team->getName() ?>,</p>
<p>
    das <?= TurnierSnippets::ortDatumBlock($turnier) ?> ist in die Setzphase übergegangen und hat noch
    <b>freie Plätze, für welche ihr spielberechtigt seid.</b>
</p>
<p>
    <a href='<?= Env::BASE_URL . "/liga/turnier_details?turnier_id=" . $turnier->id() ?>'>Link zum Turnier</a>
</p>
<p>
    Ihr erhaltet diese automatische E-Mail, weil Ihr einen passenden Turnierblock habt.
</p>
