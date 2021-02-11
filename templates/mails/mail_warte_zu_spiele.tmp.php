<p>Hallo <?= Team::teamid_to_teamname($team_id) ?>, </p>
<p>
    dein Team ist auf dem <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?>
    am <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?> von der Warteliste auf die
    <b>Spielen-Liste</b> aufgerückt.
</p>
<p>
    <a href='<?= Config::BASE_LINK . "/liga/turnier_details?turnier_id=" . $turnier->id ?>'>Link zum Turnier</a>
</p>
