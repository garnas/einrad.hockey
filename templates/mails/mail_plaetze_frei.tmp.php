<p>Hallo <?= Team::teamid_to_teamname($team_id) ?>,</p>
<p>
    das <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?> am
    <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?> ist in die Meldephase übergegangen und hat noch
    <b>freie Spielen-Plätze.</b>
    <a href='<?= Config::BASE_LINK . "/liga/turnier_details?turnier_id=" . $turnier->id ?>'>(Link zum Turnier)</a>
    Ihr erhaltet diese automatische E-Mail, weil Ihr einen passenden Turnierblock habt.
</p>
