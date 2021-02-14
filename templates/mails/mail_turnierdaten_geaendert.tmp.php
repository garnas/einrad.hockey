<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>E-Mail der Deutschen Einradhockeyliga</title>
</head>
<body>
<p>Hallo Ligaausschuss,</p>
<p>
    <?= $turnier->details["teamname"] ?> hat als Ausrichter seine Turnierdaten vom
    <?= $turnier->details['tblock'] ?>-Turnier in <?= $turnier->details['ort'] ?> verändert.
</p>
<p>
    <a href='<?= Config::BASE_URL . "/ligacenter/lc_turnier_log?turnier_id=" . $turnier->id ?> '>
        Link zum Turnier
    </a>
</p>
<p>Teams werden nicht mehr automatisch benachrichtigt.</p>
<p>Euer Mailbot</p>
</body>
</html>