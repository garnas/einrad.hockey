<?php
ini_set('max_execution_time', '300');

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session


function test_neue_tabellen($counter = 0, $fehler = 0, $penalty = 0)
{
    $turnier_id = 860;
    if ($counter + $fehler + $penalty > 1) {
        Form::attention($penalty . " Penalty");
        Form::affirm($counter . " Erfolgreiche Durchgänge");
        Form::error($fehler . " Fehler");
        $delta_load_time = microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"];
        Form::attention(round($delta_load_time, 3) . ' Sekunden');
        db::debug(memory_get_peak_usage());
        return;
    }

    db::writedb("UPDATE `spiele` SET `tore_a` = ROUND(4*RAND(),0) ,`tore_b`= ROUND(4*RAND(),0) WHERE turnier_id = $turnier_id");
    db::writedb("UPDATE `spiele` SET `penalty_a` = NULL ,`penalty_b`= NULL WHERE turnier_id = $turnier_id");

    // Neu
    $spielplan = new Spielplan((new Turnier($turnier_id)));

    // Penaltys hinzufügen
    foreach ($spielplan->penaltys['gesamt'] as $spiel_id) {
        $team_id_a = $spielplan->spiele[$spiel_id]['team_id_a'];
        $team_id_b = $spielplan->spiele[$spiel_id]['team_id_b'];
        $tore_a = ($team_id_a > 127) ? round($team_id_a / 10) : $team_id_a;
        $tore_b = ($team_id_b > 127) ? round($team_id_b / 10) : $team_id_b;
        $tore_a = random_int(0, 3);
        $tore_b = random_int(0, 3);
        $tore_b = (abs($tore_b - $tore_a) > 2) ? max($tore_a + 1, 0) : $tore_b;
        $tore_b = ($tore_a == $tore_b) ? $tore_a + 1 : $tore_b;
        db::writedb("
                        UPDATE `spiele` SET `penalty_a` = $tore_a, `penalty_b`= $tore_b
                        WHERE turnier_id = $turnier_id
                        AND ((team_id_a = $team_id_a AND team_id_b = $team_id_b) OR (team_id_a = $team_id_b AND team_id_b = $team_id_a))
                        ");
    }
    $spielplan = new Spielplan((new Turnier($turnier_id)));
    if ($spielplan->out_of_scope) {
        Form::attention($counter . " Durchgänge");
        header('Location: spielplan.php?turnier_id=' . $turnier_id);
        die();
    }
    $tabelle_neu = $spielplan->platzierungstabelle;

    // Alt
    $spielplan_alt = new SpielplanAlt($turnier_id);
    $tabelle_alt = $spielplan_alt->get_turnier_tabelle();

    // Ligapunkte
    #db::debug($tabelle_alt);
    #db::debug($tabelle_neu);
    foreach ($tabelle_neu as $team_id => $eintrag_neu) {
        $platz_neu = $eintrag_neu['platz'];
        if (
            $tabelle_alt[$platz_neu - 1]['team_id_a'] != $team_id
            or abs($eintrag_neu['ligapunkte'] - $tabelle_alt[$platz_neu - 1]['ligapunkte']) > 1
            or $tabelle_alt[$platz_neu - 1]['punkte'] != $eintrag_neu['statistik']['punkte']
        ) {
            Form::error(Team::teamid_to_teamname($team_id) . " | " . Team::teamid_to_teamname($tabelle_alt[$platz_neu - 1]['team_id_a']));
            Form::attention($counter . " Durchgänge");
            db::debug($tabelle_alt);
            header('Location: spielplan.php?turnier_id=' . $turnier_id);
            die();
        }
    }
    unset ($spielplan);
    unset ($spielplan_alt);
    test_neue_tabellen($counter + 1, $fehler, $penalty);
}

function db_test()
{
    $is = range(1, 27);
    foreach ($is as $i) {
        foreach (Ligabot::get_turnier_ids($i) as $turnier_id) {
            $sql = "
                SELECT *
                FROM turniere_liga
                LEFT JOIN turniere_details
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                WHERE turniere_liga.turnier_id = $turnier_id
                ";
            $result = db::readdb($sql);
            $x = mysqli_fetch_assoc($result);
            $turniere[] = $x;
        }
    }
    foreach (Team::get_ligateams_id() as $team_id) {
        $sql = "
            SELECT *
            FROM teams_liga
            LEFT JOIN teams_details
            on teams_liga.team_id = teams_details.team_id
            WHERE teams_liga.team_id = $team_id
            ";
        $result = db::readdb($sql);
        $x = mysqli_fetch_assoc($result);
        $teams[] = $x;
    }
    db::debug($delta_load_time = microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"]);
}

function internet()
{
    $is = range(1, 27);

    $db = new dbi(Config::HOST_NAME, Config::USER_NAME, Config::PASSWORD, Config::DATABASE);
    foreach ($is as $i) {
        foreach (Ligabot::get_turnier_ids($i) as $turnier_id) {
            $sql = "
                SELECT *
                FROM turniere_liga
                LEFT JOIN turniere_details
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                WHERE turniere_liga.turnier_id = ?
                ";
            $turniere_n[] = $db->query($sql, $turnier_id)->fetchALL();
        }
    }
    foreach (Team::get_ligateams_id() as $team_id) {
        $sql = "
            SELECT *
            FROM teams_liga
            LEFT JOIN teams_details
            on teams_liga.team_id = teams_details.team_id
            WHERE teams_liga.team_id = ?
            ";
        $teams_n[] = $db->query($sql, $team_id)->fetchArray();
    }
    db::debug($delta_load_time = microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"]);
}

function ansgar()
{
    $is = range(1, 27);

    foreach ($is as $i) {
        foreach (Ligabot::get_turnier_ids($i) as $turnier_id) {
            $sql = "
                SELECT *
                FROM turniere_liga
                LEFT JOIN turniere_details
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                WHERE turniere_liga.turnier_id = ?
                ";
            $turniere_nn[] = dbi::$db->query($sql, $turnier_id)->fetch('turnier_id');
        }
    }
    foreach (Team::get_ligateams_id() as $team_id) {
        $sql = "
            SELECT *
            FROM teams_liga
            LEFT JOIN teams_details
            on teams_liga.team_id = teams_details.team_id
            WHERE teams_liga.team_id = ?
            ";
        $teams_nn[] = dbi::$db->query($sql, $team_id)->fetch('team_id');
    }
    db::debug($teams_nn);
    db::debug($turniere_nn);
    db::debug($delta_load_time = microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"]);
}
//ansgar();
//db_test();
//internet();
//test("hallo",2,4,5,[1234]);

$sql = "
        SELECT * FROM turniere_liste
        INNER JOIN teams_liga tl on turniere_liste.team_id = tl.team_id
        WHERE liste = ?
        AND tl.team_id = ?
        ORDER BY RAND();
        ";
//db::debug(adb::$link->query($sql, "spiele", "Ja")->fetch());
//db::debug(adb::$link->query($sql, "spiele", 16));
//$sql =  "
//        UPDATE teams_liga SET freilose = ?
//";
//dbi::$db->query($sql, 0);


$sql =  "
        SELECT inhalt FROM neuigkeiten
";
$team_id = 16;
//db::debug(adb::$link->query($sql)->esc()->fetch_one());
//db::debug(adb::$link->query($sql)->fetch_one());
//db::debug(adb::$link->query($sql)->esc()->fetch_row());
//db::debug(adb::$link->query($sql)->fetch_row());
//db::debug(adb::$link->query($sql)->esc()->fetch()[0]);
//db::debug(adb::$link->query($sql)->fetch()[0]);
db::debug(dbi::$db->query($sql)->log()->fetch());


//db::debug(adb::$link->query($sql)->result->num_rows);
//db::debug(adb::$link->query($sql, "spiele", 16)->fetch());
//db::debug(adb::$link->result, true);
//db::debug(adb::$link->stmt->num_rows);
//db::debug(adb::$link->query_count);
//db::debug(adb::$link);
function test($string, ...$test){
    db::debug($string);
    db::debug($test);
    if (empty($test)){
        db::debug("empty");
    }
    if(is_array($test)){
        db::debug("array");
    }
}
//db::debug(adb::$link->query_count);
include '../../templates/header.tmp.php'; ?>
    <h1>Test Skript</h1>
<?php include '../../templates/footer.tmp.php';

