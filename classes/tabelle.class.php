<?php

/**
 * Class Tabelle
 *
 * Alles zum Anzeigen der Tabelle
 */
class Tabelle
{

    /**
     * Speichert die Erstellten Rangtabellen, damit diese nicht mehrfach erstellt werden müssen.
     */
    public static array $cache_rangtabellen = [];
    public static array $cache_meisterschaftstabelle;

    /**
     * Übergibt den Spieltag, der als naechstes gespielt wird
     * 
     *
     * @param int $saison
     * @return int
     */
    public static function get_aktuellen_spieltag(int $saison = Config::SAISON): int
    {
        $sql = "
                SELECT max(spieltag) + 1 
                FROM turniere_liga 
                WHERE saison = ?
                AND (art = 'I' OR art = 'II' OR art = 'III')
                AND phase = 'ergebnis'
                ";
        return db::$db->query($sql, $saison)->fetch_one() ?? 1;
    }
    public static function is_spieltag_beendet(int $spieltag): bool
    {
        $sql = "
                SELECT phase
                FROM turniere_liga 
                WHERE spieltag = ?
                AND (art = 'I' OR art = 'II') 
                AND saison = ?
                AND canceled != 1
                GROUP BY phase
                ";
        $result = db::$db->query($sql, $spieltag, Config::SAISON)->list("phase");
        return $result == ["ergebnis"];
    }

    /**
     * Schaut ob der aktuelle Spieltag live ist, also ein unvollständiger Spieltag, welcher teilweise ausgespielt wurde.
     *
     * @param int $spieltag
     * @param int $saison
     * @return bool
     */
    public static function check_spieltag_live(int $spieltag, int $saison = Config::SAISON): bool
    {
        $sql = "
                SELECT phase, count(phase)
                FROM turniere_liga 
                WHERE spieltag = ?
                AND (art = 'I' OR art = 'II' OR art = 'III') 
                AND saison = ?
                GROUP BY phase
                ";
        $result = db::$db->query($sql, $spieltag, $saison)->list('count(phase)','phase');
        return (
                isset($result['spielplan']) or isset($result['melde']) or isset($result['offen'])
                )
                && isset($result['ergebnis']);
    }

    /**
     * Gibt die Platzierung eines Teams in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @param int $saison
     * @return int|null
     */
    public static function get_team_rang(int $team_id, NULL|int $spieltag = NULL, int $saison = Config::SAISON): ?int
    {
        // Default: Aktueller Spieltag - 1 = Spieltag mit allen eingetragenen Ergebnissen
        $spieltag = $spieltag ?? (self::get_aktuellen_spieltag($saison) - 1);

        // Rangtabelle soll nicht jedes mal neu berechnet werden müssen
        if (!isset(self::$cache_rangtabellen[$spieltag])){
            self::$cache_rangtabellen[$spieltag] = self::get_rang_tabelle($spieltag, $saison);
        }
        // Nichtligateam haben den Rang NULL
        return self::$cache_rangtabellen[$spieltag][$team_id]['rang'] ?? NULL;
    }

    /**
     * Gibt die Platzierung eines Teams in der Meisterschaftstabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return int|null
     */
    public static function get_team_meister_platz(int $team_id, NULL|int $spieltag = NULL): ?int
    {
        // Default: Aktueller Spieltag - 1 = Spieltag mit allen eingetragenen Ergebnissen
        $spieltag = $spieltag ?? (self::get_aktuellen_spieltag() - 1);
        if (!isset(self::$cache_meisterschaftstabelle)){
            self::$cache_meisterschaftstabelle = self::get_meisterschafts_tabelle($spieltag);
        }
        // Nichtligateam haben den Platz NULL
        return self::$cache_meisterschaftstabelle[$team_id]['platz'] ?? NULL;
    }

    /**
     * Gibt den Block eines Teams auf Grundlage der Platzierung in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return string|null
     */
    public static function get_team_block(int $team_id, NULL|int $spieltag = NULL): ?string
    {
        $rang = self::get_team_rang($team_id, $spieltag);
        return self::rang_to_block($rang);
    }

    /**
     * Gibt die Wertung eines Teams auf Grundlage der Platzierung in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return int|null
     */
    public static function get_team_wertigkeit(int $team_id, null|int $spieltag = null, int $saison = Config::SAISON): ?int
    {
        $rang = self::get_team_rang($team_id, $spieltag, $saison);
        return self::rang_to_wertigkeit($rang);
    }

    /**
     * Weist dem Platz in der Rangtabelle einen Block zu
     *
     * @param int|null $rang
     * @return string|null
     */
    public static function rang_to_block(?int $rang): ?string
    {
        // Nichtligateam
        if (is_null($rang)) return NULL;

        // Blockzuordnung
        foreach (Config::RANG_TO_BLOCK as $block => $range) {
            if ($range[0] <= $rang && $range[1] >= $rang){
                return $block;
            }
        }
        trigger_error("Aus der Rangtabelle konnte kein Block abgeleitet werden.", E_USER_ERROR);
    }

    /**
     * Weist dem Platz in der Rangtabelle eine Wertung zu
     *
     * @param int|null $rang
     * @return int|null
     */
    public static function rang_to_wertigkeit(?int $rang): ?int
    {
        // Nichtligateam
        if (is_null($rang)){
            return NULL;
        }

        // Platz 1 bis 43;
        if (1 <= $rang && 43 >= $rang){
            return round(250 * 0.955 ** ($rang - 1));
        }

        // Platz 44 bis Rest
        return max([round(250 * 0.955 ** (43) * 0.97 ** ($rang - 1 - 43)), 15]);
    }

    /**
     * Get alle Ergebnisse der Saison
     *
     * @param int $saison
     * @return array
     */
    public static function get_all_ergebnisse(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_ergebnisse.*, teams_liga.teamname , teams_liga.ligateam, teams_liga.team_id
                FROM turniere_ergebnisse
                LEFT JOIN teams_liga
                ON teams_liga.team_id = turniere_ergebnisse.team_id
                LEFT JOIN turniere_liga
                ON turniere_liga.turnier_id = turniere_ergebnisse.turnier_id
                WHERE turniere_liga.saison = $saison
                AND phase = 'ergebnis'
                ORDER BY turniere_liga.datum DESC, platz
                ";
        $result = db::$db->query($sql)->esc()->fetch();
        if ($saison !== Config::SAISON) {
            foreach ($result as $key => $ergebnis) {
                $result[$key]['teamname'] = Team::id_to_name($ergebnis['team_id'], $saison);
            }
        }
        foreach ($result as $ergebnis){
            $return[$ergebnis['turnier_id']][] = $ergebnis;
        }
        return $return ?? [];
    }

    public static function get_meisterschafts_tabelle_templates(int $saison = Config::SAISON): array
    {        
        return array(
            'desktop' => 'templates/tabellen/desktop_meistertabelle.tmp.php',
            'mobil' => 'templates/tabellen/mobil_meistertabelle.tmp.php',
        );
    }

    public static function get_rang_tabelle_templates(int $saison = Config::SAISON): array
    {
        return array(
            'desktop' => 'templates/tabellen/desktop_rangtabelle.tmp.php',
            'mobil' => 'templates/tabellen/mobil_rangtabelle.tmp.php',
        );
    }
    
    /**
     * Gibt das Array der Meisterschaftstabelle aus
     *
     * @param int $spieltag
     * @param int $saison
     * @return array
     */
    public static function get_meisterschafts_tabelle(int $spieltag, int $saison = Config::SAISON): array
    {

        $sql = "
            WITH tournaments as (
                SELECT te.team_id, teams.teamname, te.turnier_id, tl.datum, te.ergebnis, td.ort, tl.tblock, te.platz, dense_rank() over (PARTITION BY te.team_id order by te.ergebnis DESC) AS `rank`
                FROM turniere_ergebnisse te
                INNER JOIN turniere_liga tl ON tl.turnier_id = te.turnier_id
                INNER JOIN teams_liga teams ON teams.team_id = te.team_id
                INNER JOIN turniere_details td ON td.turnier_id = te.turnier_id
                WHERE teams.ligateam = 'Ja'
                AND tl.art != 'final' 
                AND (tl.saison = ?) 
                AND (tl.spieltag <= ?)
            ), num_of_teams as (
                SELECT turnier_id, count(*) as teilnehmer
                FROM turniere_ergebnisse te
                GROUP BY turnier_id
            )

            SELECT team_id, teamname, t.turnier_id, datum, ergebnis, ort, tblock, platz, teilnehmer
            FROM tournaments t
            INNER JOIN num_of_teams n ON n.turnier_id = t.turnier_id
            WHERE `rank` <= 4 ORDER BY team_id, ergebnis DESC
         ";
        $result = db::$db->query($sql, $saison, $spieltag)->esc()->fetch();
        $return = [];
        foreach ($result as $eintrag) {
            $team_id = $eintrag['team_id'];

            if (!isset($return[$team_id])) {
                $return[$team_id]['team_id'] = $team_id;
                $return[$team_id]['teamname'] = $eintrag['teamname'];
                $return[$team_id]['einzel_ergebnisse'] = [];
                $return[$team_id]['details'] = [];
                $return[$team_id]['summe'] = 0;
                $return[$team_id]['hat_strafe'] = false;
            }

            $return[$team_id]['summe'] += $eintrag['ergebnis'];        
            $return[$team_id]['einzel_ergebnisse'][] = $eintrag['ergebnis'];
            $return[$team_id]['details'][] = $eintrag;
        }

        // Tabelle mit aktiven Teams ohne Ergebnis auffüllen
        // In vergangenen Saisons werden nur Teams mit Ergebnissen gelistet
        if ($saison == Config::SAISON) {
            $list_of_teamids = Team::get_liste_ids();
            shuffle($list_of_teamids);
            foreach ($list_of_teamids as $team_id) {
                if (!array_key_exists($team_id, $return)) {
                    $return[$team_id] = [];
                    $return[$team_id]['teamname'] = Team::id_to_name($team_id); //Ansonsten doppel dbi::escape --> fehler in der Darstellung
                    $return[$team_id]['team_id'] = $team_id;
                    $return[$team_id]['string'] = '';
                    $return[$team_id]['summe'] = 0;
                    $return[$team_id]['einzel_ergebnisse'] = [0];
                    $return[$team_id]['details'] = [];
                    $return[$team_id]['hat_strafe'] = false;
                }
            }
        }

        // Hinzufügen der Strafen:
        $strafen = Team::get_strafen($saison);
        foreach ($strafen as $strafe) {
            # Ist die Strafe überhaupt in den Ergebnissen enthalten?
            if (!isset($return[$strafe['team_id']])) {
                continue;
            }

            $return[$strafe['team_id']]['hat_strafe'] = true;
            
            // Addieren der Prozentstrafen
            if ($strafe['verwarnung'] == 'Nein' && !empty($strafe['prozentsatz'])) {
                $return[$strafe['team_id']]['strafe'] = ($return[$strafe['team_id']]['strafe'] ?? 0) + $strafe['prozentsatz'] / 100;
            }
        }

        // Kumulierte Strafe mit der Summe der Turnierergebnisse des Teams verrechnen
        foreach ($return as $team_id => $team) {
            if (isset($team['strafe'])) {
                $return[$team_id]['summe'] = round($team['summe'] * (1 - $team['strafe']));
            }
        }

        // Nach Summe der Ergebnisse sortieren mit der Funktion "sortieren_summe" die eine public static function in dieser Klasse Tabelle ist
        uasort($return, ["Tabelle", "sortieren_summe"]);

        // Zuordnen der Plätze
        // Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $platz = 1;
        $zeile_vorher['platz'] = 1;
        $zeile_vorher['summe'] = 0;
        $zeile_vorher['max_einzel'] = 0;
        foreach ($return as $key => $zeile) {
            $zeile['max_einzel'] = max($zeile['einzel_ergebnisse'] ?? [0]);
            if (
                $zeile_vorher['summe'] === $zeile['summe']
                && $zeile_vorher['max_einzel'] === $zeile['max_einzel']
            ) {
                $return[$key]['platz'] = $zeile_vorher['platz'];
            } else {
                $return[$key]['platz'] = $platz;
            }
            $zeile_vorher['summe'] = $zeile['summe'];
            $zeile_vorher['max_einzel'] = $zeile['max_einzel'];
            $zeile_vorher['platz'] = $return[$key]['platz'];
            $platz++;
        }

        if ($saison !== Config::SAISON) {
            foreach ($return as $team_id => $team) {
                $return[$team_id]['teamname'] = Team::id_to_name($team_id, $saison);
            }
        }

        return $return;
    }

    /**
     * Gibt die Rangtabelle als Array aus
     *
     * @param int $spieltag
     * @param int $saison
     * @return array
     */
    public static function get_rang_tabelle(int $spieltag, int $saison = Config::SAISON): array
    {

        $ausnahme = match($saison) {
            26 => 'OR tl.saison = 24',
            27 => 'OR tl.saison = 24 OR tl.saison = 25',
            default => '',
        };

        $sql = "
            WITH tournaments as (
                SELECT te.team_id, teams.teamname, te.turnier_id, tl.saison, tl.datum, te.ergebnis, te.platz, tl.tblock, td.ort, row_number() over (PARTITION BY te.team_id order by tl.datum DESC) AS `turnier_rang`
                FROM turniere_ergebnisse te
                INNER JOIN turniere_liga tl ON tl.turnier_id = te.turnier_id
                INNER JOIN teams_liga teams ON teams.team_id = te.team_id
                INNER JOIN turniere_details td ON td.turnier_id = te.turnier_id
                WHERE teams.ligateam = 'Ja'
                AND teams.aktiv = 'Ja'
                AND tl.art != 'final' 
                AND ((tl.spieltag <= ? AND tl.saison = ?) OR tl.saison = ? - 1 $ausnahme)
            ), num_of_teams as (
                SELECT turnier_id, count(*) as teilnehmer
                FROM turniere_ergebnisse te
                GROUP BY turnier_id
            )

            SELECT t.saison, t.turnier_id, t.datum, t.team_id, t.teamname, t.platz, t.ergebnis, t.ort, t.tblock, t.saison, n.teilnehmer
            FROM tournaments t
            LEFT JOIN num_of_teams n ON n.turnier_id = t.turnier_id
            WHERE `turnier_rang` <= 5
            ORDER BY t.datum DESC
        ";
        $result = db::$db->query($sql, $spieltag, $saison, $saison)->esc()->fetch();
        $return = [];

        foreach($result as $row) {
            $team_id = $row['team_id'];

            if (!isset($return[$team_id])) {
                $return[$team_id]['team_id'] = $team_id; //Wichtig, da bei Sortierung die $row['team_id] überschrieben wird
                $return[$team_id]['teamname'] = $row['teamname'];
                $return[$team_id]['summe'] = 0;
                $return[$team_id]['ergebnisse'] = [];
                $return[$team_id]['details'] = [];
            }
            
            $return[$team_id]['summe'] += $row['ergebnis'];
            $return[$team_id]['ergebnisse'][] = $row['ergebnis'];
            $return[$team_id]['details'][] = $row;
            $return[$team_id]['avg'] = round($return[$team_id]['summe'] / count($return[$team_id]['ergebnisse']), 1);
        }

        // Tabelle mit aktiven Teams ohne Ergebnis auffüllen
        // TODO ? In vergangenen Saisons werden nur Teams mit Ergebnissen gelistet, ist das gut so?
        if ($saison == Config::SAISON) {
            $list_of_teamids = Team::get_liste();
            foreach ($list_of_teamids as $team_id => $teamname) {
                if (!array_key_exists($team_id, $return)) {
                    $return[$team_id] = [];
                    $return[$team_id]['teamname'] = $teamname;
                    $return[$team_id]['team_id'] = $team_id;
                    $return[$team_id]['string'] = '';
                    $return[$team_id]['summe'] = 0;
                    $return[$team_id]['avg'] = 0;
                    $return[$team_id]['ergebnisse'] = [0];
                    $return[$team_id]['details'] = [];
                }
            }
        }
        if ($saison !== Config::SAISON) {
            foreach ($return as $team_id => $team) {
                $return[$team_id]['teamname'] = Team::id_to_name($team_id, $saison);
            }
        }

        // Nach Summe der Ergebnisse sortieren mit der Funktion "sortieren_avg"
        uasort($return, ["Tabelle", "sortieren_avg"]); //Sortieren nach der static function sortieren_avg in der Klasse Tabelle...

        // Zuordnen der Blöcke
        // Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $rang = 1;
        $zeile_vorher['rang'] = 1;
        $zeile_vorher['summe'] = 0;
        $zeile_vorher['max_einzel'] = 0;
        foreach ($return as $key => $zeile) {
            $zeile['max_einzel'] = max($zeile['ergebnisse']);
            if (
                $zeile_vorher['summe'] == $zeile['summe']
                && $zeile_vorher['max_einzel'] == $zeile['max_einzel']
            ) {
                $return[$key]['rang'] = $zeile_vorher['rang'];
            } else {
                $return[$key]['rang'] = $rang;
            }
            $zeile_vorher['summe'] = $zeile['summe'];
            $zeile_vorher['max_einzel'] = $zeile['max_einzel'];
            $zeile_vorher['rang'] = $return[$key]['rang'];
            $rang++;
        }
        return $return;
    }

    /**
     * Individuelle Sortierfunktion für die Meisterschaftstabelle für usort
     *
     * @param array $value1
     * @param array $value2
     * @return int
     */
    public static function sortieren_summe(array $value1, array $value2): int
    {
        if ($value1['summe'] < $value2['summe']) return 1;
        if ($value1['summe'] > $value2['summe']) return -1;
        return max($value2['einzel_ergebnisse']) <=> max($value1['einzel_ergebnisse']);
    }

    /**
     * Individuelle Sortierfunktion für die Rangtabelle
     *
     * @param array $value1
     * @param array $value2
     * @return int
     */
    public static function sortieren_avg(array $value1, array $value2): int
    {
        if ($value1['avg'] < $value2['avg']) return 1;
        if ($value1['avg'] > $value2['avg']) return -1;
        return max($value2['ergebnisse']) <=> max($value1['ergebnisse']);
    }
}