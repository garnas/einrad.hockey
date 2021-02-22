<p>Hallo <?= Team::id_to_name($team_id) ?>,</p>
<p>
    das <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?> am
    <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?> ist in die Meldephase übergegangen und hat noch
    <b>freie Spielen-Plätze.</b>
</p>
<p>
    <a href='<?= Env::BASE_URL . "/liga/turnier_details?turnier_id=" . $turnier->id ?>'>Link zum Turnier</a>
</p>
<p>
    Ihr erhaltet diese automatische E-Mail, weil Ihr einen passenden Turnierblock habt.
</p>
