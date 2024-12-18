<?php

use App\Service\Turnier\TurnierSnippets;

?>
<p>Hallo <?= $anmeldung->getTeam()->getName() ?>,</p>
<p>
    das Turnier in <?= TurnierSnippets::ortDatumBlock($turnier) ?> ist in die Setzphase übergegangen und
    die freien Plätze wurden vergeben. Euer Team steht nun auf der <?= TurnierSnippets::translate($anmeldung->getListe()) ?>
</p>
<?php if ($anmeldung->isWarteliste()) {?>
    <p>
        <b>Hinweis:</b> Auf der Warteliste rückt ihr automatisch nach, wenn noch ein Platz für euch frei wird.
        Wenn der Spielplan schon erstellt wurde, dann geschieht dies manuell vom Ligaausschuss.
        Bitte meldet euch von der Warteliste ab, wenn euer Team nicht als Nachrücker zur Verfügung steht.
    </p>
<?php } ?>
<p>
    <a href="<?= Env::BASE_URL . "/liga/turnier_details.php?turnier_id=" . $turnier->id() ?>">
        Link des Turnieres
    </a>
</p>