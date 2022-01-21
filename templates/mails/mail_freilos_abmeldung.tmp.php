<p>Hallo <?= Team::id_to_name($team_id) ?></p>
<p>
    Ihr hattet für das <?= $turnier->get_tblock() ?>-Turnier in <?= $turnier->get_ort() ?>
    am <?= date("d.m.Y", strtotime($turnier->get_datum())) ?>
</p>
<p>
    <a href='<?= Env::BASE_URL ?>/liga/turnier_details.php?turnier_id=<?= $turnier->get_turnier_id() ?>'>
        Link zum Turnier
    </a>
</p>
<p>
    ein Freilos gesetzt. Da euer Teamblock höher war als der des Turnierblocks, wurdet ihr von der Spielen-Liste
    abgemeldet und seid nun auf der Warteliste. Das Freilos wurde euch erstattet.
</p>