<p>Hallo <?= Team::id_to_name($team_id) ?></p>
<p>
    Ihr hattet für das <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?>
    am <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?>
</p>
<p>
    <a href='<?= Env::BASE_URL ?>/liga/turnier_details.php?turnier_id=<?= $turnier->id ?>'>
        Link zum Turnier
    </a>
</p>
<p>
    ein Freilos gesetzt. Da euer Teamblock höher war als der des Turnierblocks, wurdet ihr von der Spielen-Liste
    abgemeldet und seid nun auf der Warteliste. Das Freilos wurde euch erstattet.
</p>