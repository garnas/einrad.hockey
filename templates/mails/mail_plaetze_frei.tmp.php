<p>Hallo <?= Team::id_to_name($team_id) ?>,</p>
<p>
    das <?= $turnier->get_tblock() ?>-Turnier in <?= $turnier->get_ort() ?> am
    <?= date("d.m.Y", strtotime($turnier->get_datum())) ?> ist in die Meldephase übergegangen und hat noch
    <b>freie Spielen-Plätze.</b>
</p>
<p>
    <a href='<?= Env::BASE_URL . "/liga/turnier_details?turnier_id=" . $turnier->get_turnier_id() ?>'>Link zum Turnier</a>
</p>
<p>
    Ihr erhaltet diese automatische E-Mail, weil Ihr einen passenden Turnierblock habt.
</p>
