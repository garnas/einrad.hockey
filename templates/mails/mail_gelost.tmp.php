<p>Hallo <?= Team::id_to_name($team_id) ?>,</p>
<p>
    das <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?> am
    <?= date("d.m.Y", strtotime($turnier->details['datum'])) ?> ist in die Meldephase übergegangen und
    die freien Spielen-Plätze wurden besetzt. Euer Team steht nun auf der <b><?= $liste ?></b>
</p>
<?php if ($liste == "Warteliste") {?>
    <p>
        <b>Hinweis:</b> Auf der Warteliste rückt ihr automatisch nach, wenn noch ein Platz für euch frei wird.
        Wenn der Spielplan schon erstellt wurde, dann geschieht dies manuell vom Ligaausschuss.
        Bitte meldet euch von der Warteliste ab, wenn euer Team nicht als Nachrücker zur Verfügung steht.
    </p>
<?php } ?>
<p>
    <a href="<?= Env::BASE_URL . "/liga/turnier_details.php?turnier_id=" . $turnier->id ?>">
        Link des Turnieres
    </a>
</p>