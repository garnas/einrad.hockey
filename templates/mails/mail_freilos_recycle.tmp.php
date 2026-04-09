<?php

use App\Repository\Team\TeamRepository;

?>

<p>Hallo <?= TeamRepository::get()->getTeamName($team) ?>, </p>
<p>
    da ihr euer Freilos acht Wochen vor Turnierbeginn gesetzt habt, wurde euch ein neues Freilos gutgeschrieben.
    Eine Übersicht über deine Freilose findest du <a href='<?= Env::BASE_URL ?>/teamcenter/tc_teamdaten.php'>hier</a>.
</p>