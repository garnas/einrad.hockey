<p>Hallo <?= $team->getName() ?>,</p>
<p>
    es wurde ein neues Turnier eingetragen, für welches ihr euch anmelden könnt:
    <?= $turnier->getBlock() ?>-Turnier in <?= $turnier->getDetails()->getOrt() ?>
    am <?= $turnier->getDatum()->format('d.m.Y') ?>
</p>
<p>
    <a href='<?= Env::BASE_URL ?>/liga/turnier_details.php?turnier_id=<?= $turnier->id() ?>'>
        Link zum Turnier
    </a>
</p>