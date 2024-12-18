<p>Hallo liebe Teams,</p>
<p>
    das <?= $turnier->getBlock() ?>-Turnier in <?= $turnier->getDetails()->getOrt() ?>
    am <?= $turnier->getDatum()->format('d.m.Y') ?> wurde von Seiten des Ligaausschusses abgesagt.
</p>

    Grund: <?= $turnier->getCanceledGrund() ?>
<p>
    <a href='<?= Env::BASE_URL ?>/liga/turnier_details.php?turnier_id=<?= $turnier->id() ?>'>
        Link zum Turnier
    </a>
</p>
<p>
    Viele Grüße<br>
    euer Ligaausschuss
</p>
</body>
</html>