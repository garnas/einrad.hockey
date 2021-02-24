<p>Hallo <?= Team::id_to_name($team_id) ?>,</p>
<p>
    es wurde ein neues Turnier eingetragen, für welches ihr euch anmelden könnt:
    <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?>
    am <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?>
</p>
<p>
    <a href='<?= Env::BASE_URL ?>/liga/turnier_details.php?turnier_id=<?= $turnier->id ?>'>
        Link zum Turnier
    </a>
</p>