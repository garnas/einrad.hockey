<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>E-Mail der Deutschen Einradhockeyliga</title>
</head>
<body>
<p>Hallo Ligaausschuss,</p>
<p>
    <?= Team::id_to_name($turnier->get_ausrichter()) ?> hat als Ausrichter seine Turnierdaten vom
    <?= $turnier->get_tblock() ?>-Turnier in <?= $turnier->get_ort() ?> verändert.
</p>
<p>
    <a href='<?= Env::BASE_URL . "/ligacenter/lc_turnier_log?turnier_id=" . $turnier->get_turnier_id() ?> '>
        Link zum Turnier
    </a>
</p>
<p>Teams werden nicht mehr automatisch benachrichtigt.</p>
<p>Euer Mailbot</p>
</body>
</html>