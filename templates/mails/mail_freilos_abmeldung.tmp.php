<p>Hallo <?= Team::teamid_to_teamname($team_id) ?></p>
<p>
    Ihr hattet für das <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?>
    am <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?>
    <a href='<?= Config::BASE_LINK ?>/liga/turnier_details.php?turnier_id=<?= $turnier->id ?>'>
        (Link zum Turnier)
    </a>
    ein Freilos gesetzt. Da euer Teamblock höher war als der des Turnierblocks, wurdet ihr von der Spielen-Liste
    abgemeldet und seid nun auf der Warteliste. Das Freilos wurde euch erstattet.
</p>