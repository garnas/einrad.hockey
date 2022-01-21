<p>Hallo <?= Team::id_to_name($team_id) ?>,</p>
<p>
    es wurde ein neues Turnier eingetragen, für welches ihr euch anmelden könnt:
    <?= $turnier->get_tblock() ?>-Turnier in <?= $turnier->get_ort() ?>
    am <?= date("d.m.Y", strtotime($turnier->get_datum())) ?>
</p>
<p>
    <a href='<?= Env::BASE_URL ?>/liga/turnier_details.php?turnier_id=<?= $turnier->get_turnier_id() ?>'>
        Link zum Turnier
    </a>
</p>