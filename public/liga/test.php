<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session


dbi::debug($RANG_TO_BLOCK, true);
function umsch (){
    $sql ="SELECT * FROM turniere_details WHERE format='jgj'";
    $return = dbi::$db->query($sql)->fetch();
    foreach ($return as $t){
        $sql = "SELECT count(*) FROM turniere_liste WHERE turnier_id = ? AND liste='spiele'";
        $ret = dbi::$db->query($sql, $t['turnier_id'])->fetch_one();

        if ($ret < 4) {
            continue;
        }
        $sql ="UPDATE turniere_details 
                INNER JOIN turniere_liga on turniere_details.turnier_id = turniere_liga.turnier_id
                SET spielplan_vorlage = ? 
                WHERE turniere_details.turnier_id = ? AND turniere_liga.saison = 26
                AND (turniere_liga.phase = 'spielplan' or turniere_liga.phase = 'ergebnis')
                ";
        $params = [$ret . "er_jgj_default", $t['turnier_id']];
        dbi::$db->query($sql, $params)->log();
    }
}
//umsch();
//Ligaleitung::umzug2('ausschuss_schiri', "schiriausschuss");
//Ligaleitung::umzug2('ausschuss_liga', "ligaausschuss");
//Ligaleitung::umzug2('ausschuss_oeffi', "oeffentlichkeitsausschuss");
//Ligaleitung::umzug2('ausschuss_technik', "technikausschuss");
//Ligaleitung::umzug3(); //Ausbilder
//$sql = 'DROP TABLE ausschuss_liga, ausschuss_oeffi, ausschuss_schiri, ausschuss_technik';
//dbi::$db->query($sql)->log();

function test_neue_tabellen($counter = 0, $fehler = 0, $penalty = 0)
{
    $turnier_id = 860;
    if ($counter + $fehler + $penalty > 100) {
        Html::notice($penalty . " Penalty");
        Html::info($counter . " Erfolgreiche Durchgänge");
        Html::error($fehler . " Fehler");
        $delta_load_time = microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"];
        Html::notice(round($delta_load_time, 3) . ' Sekunden');
        dbi::debug(memory_get_peak_usage());
        return;
    }

    dbi::$db->query("UPDATE `spiele` SET `tore_a` = 4*ROUND(RAND(),0) ,`tore_b`= 4*ROUND(RAND(),0) WHERE turnier_id = $turnier_id");
    dbi::$db->query("UPDATE `spiele` SET `penalty_a` = NULL ,`penalty_b`= NULL WHERE turnier_id = $turnier_id");

    // Neu
    $spielplan = new Spielplan((new Turnier($turnier_id)));

    // Penaltys hinzufügen
    foreach ($spielplan->penaltys['gesamt'] as $spiel_id) {
        $penalty++;
        $team_id_a = $spielplan->spiele[$spiel_id]['team_id_a'];
        $team_id_b = $spielplan->spiele[$spiel_id]['team_id_b'];
        $tore_a = ($team_id_a > 127) ? round($team_id_a / 10) : $team_id_a;
        $tore_b = ($team_id_b > 127) ? round($team_id_b / 10) : $team_id_b;
        $tore_a = random_int(0, 3);
        $tore_b = random_int(0, 3);
        $tore_b = (abs($tore_b - $tore_a) > 2) ? max($tore_a + 1, 0) : $tore_b;
        $tore_b = ($tore_a == $tore_b) ? $tore_a + 1 : $tore_b;
        dbi::$db->query("
                        UPDATE `spiele` SET `penalty_a` = $tore_a, `penalty_b`= $tore_b
                        WHERE turnier_id = $turnier_id
                        AND ((team_id_a = $team_id_a AND team_id_b = $team_id_b) OR (team_id_a = $team_id_b AND team_id_b = $team_id_a))
                        ");
    }
    $spielplan = new Spielplan((new Turnier($turnier_id)));
    if ($spielplan->out_of_scope) {
        Html::notice($counter . " Durchgänge");
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
            Html::error(Team::id_to_name($team_id) . " | " . Team::id_to_name($tabelle_alt[$platz_neu - 1]['team_id_a']));
            Html::notice($counter . " Durchgänge");
            db::debug($tabelle_alt);
            header('Location: spielplan.php?turnier_id=' . $turnier_id);
            die();
        }
    }
    unset ($spielplan);
    unset ($spielplan_alt);
    test_neue_tabellen($counter + 1, $fehler, $penalty);
}
include '../../templates/header.tmp.php';

?>

<?php include '../../templates/footer.tmp.php';

