<p>Hallo <?= Team::teamid_to_teamname($team_id) ?>,</p>
<p>
    das <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?> am
    <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?> ist in die Meldephase übergegangen und
    die freien Spielen-Plätze wurden nach Modus 4.4.2 verteilt. Euer Team steht nun auf der <b><?= $liste ?></b>
</p>
<p>
    <a href="<?= Config::BASE_LINK . "/liga/turnier_details.php?turnier_id=" . $turnier->id ?>">
        Erfahre hier mehr.
    </a>
</p>