<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
$anzahl_teams = static function ($id) {
    $query = "SELECT COUNT(*) FROM turniere_liste WHERE turnier_id = $id AND liste = 'setz'";
    return db::$db->query($query)->fetch_one();
};
echo ($anzahl_teams(723));
die();
$sql = "SELECT * FROM teams_name_historic";
$historic_names = Db::$db->query($sql)->fetch();
$sql = "SELECT * FROM teams_liga";
$teams =  Db::$db->query($sql)->fetch("team_id");

$team_ids = $ids_exists = $ids_not_exists = $check = [];
foreach ($historic_names as $historic_name) {
    $team_ids[] = $historic_name["team_id"];
    if (array_key_exists($historic_name["team_id"], $teams)) {
        $ids_exists[] = $historic_name["team_id"];
    } else {
        $ids_not_exists[] = $historic_name["team_id"];
        if (array_key_exists($historic_name["team_id"], $check)) {
            $check[$historic_name["team_id"]][] =  $historic_name["name"];
        } else {
            $check[$historic_name["team_id"]] = [$historic_name["name"]];
        }
    }
}
foreach ($check as $id => $_) {
    $sql = "DELETE FROM teams_name_historic WHERE team_id = $id";
    Db::$db->query($sql)->log();
}

Db::debug($check);
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Impressum | Deutsche Einradhockeyliga";
Html::$content = "Das Impressum der Deutschen Einradhockeyliga findet sich hier.";
include '../../templates/header.tmp.php'; ?>

    <div class="w3-panel w3-center">
        <h1 class="w3-text-grey">Impressum</h1>
        <h2 class="w3-text-primary">Deutsche Einradhockeyliga</h2>

        <p class="w3-text-grey">Postanschrift</p>
        <p>Ansgar Pölking<br>Karlstraße 1<br>64283 Darmstadt</p>

        <p class="w3-text-grey">Kontakt</p>
        <p><?= Html::mailto(Env::LAMAIL) ?></p>

        <h3>Du hast Lust an der Website mitzuwirken?</h3>
        <p><?= Html::link(Nav::LINK_GIT, 'Github-Account', true, 'launch') ?></p>
        <p><?= Html::mailto(Env::TECHNIKMAIL) ?></p>
    </div>

<?php include '../../templates/footer.tmp.php';