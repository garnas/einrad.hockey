<p>Hallo <?= Team::id_to_name($team_id) ?>,</p>
<p>
    das <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?> am
    <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?> ist in die Meldephase übergegangen und
    die freien Spielen-Plätze wurden nach Modus 4.5.2 verteilt. Euer Team steht nun auf der <b><?= $liste ?></b>
</p>
<?php if ($liste == "Warteliste") {?>
<p>
    Eine Abmeldung von der Warteliste ist jederzeit mmöglich und sollte vorgenommen werden, wenn das Team nicht als Nachrücker zur Verfügung steht. (Modus 4.7)
</p>
<?php } ?>
<p>
    <a href="<?= Env::BASE_URL . "/liga/turnier_details.php?turnier_id=" . $turnier->id ?>">
        Link des Turnieres
    </a>
</p>