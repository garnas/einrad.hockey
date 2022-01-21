<p>Hallo <?= Team::id_to_name($team_id) ?>, </p>
<p>
    dein Team ist auf dem <?= $turnier->get_tblock() ?>-Turnier in <?= $turnier->get_ort() ?>
    am <?= date("d.m.Y", strtotime($turnier->get_datum())) ?> von der Warteliste auf die
    <b>Spielen-Liste</b> aufgerückt.
</p>
<p>
    <a href='<?= Env::BASE_URL . "/liga/turnier_details?turnier_id=" . $turnier->get_turnier_id() ?>'>Link zum Turnier</a>
</p>
