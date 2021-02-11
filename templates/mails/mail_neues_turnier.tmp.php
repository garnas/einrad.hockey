<p>Hallo <?= Team::teamid_to_teamname($team_id) ?>,</p>
<p>
    es wurde ein neues Turnier eingetragen, für welches ihr euch anmelden könnt:
    <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?>
    am <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?>
</p>
<p>
    <a href='<?= Config::BASE_LINK ?>/liga/turnier_details.php?turnier_id=<?= $turnier->id ?>'>
        Link des neuen Turniers
    </a>
</p>