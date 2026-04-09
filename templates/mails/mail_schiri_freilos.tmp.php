<?php

use App\Repository\Team\TeamRepository;

?>
<p>Hallo <?= TeamRepository::get()->getTeamName($team) ?>,</p>
<p>
    da ihr zwei ausgebildete Schiedsrichter mit gültigen Lizenzen im Kader eingetragen habt, wurde euch ein Freilos gutgeschrieben.
</p>

