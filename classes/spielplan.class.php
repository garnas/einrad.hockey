<?php

/**
 * Class Spielplan
 *
 * Verwaltet Spielpläne, berechnet Turniertabellen und schreibt Turnierergebnisse in die DB.
 */
class Spielplan
{
    /**
     * Allgemeine Daten
     */
    public int $turnier_id;
    public Turnier $turnier;
    public array $teamliste;
    public array $details;
    public int $anzahl_teams;
    public int $anzahl_spiele;
    public array $spiele;

    /**
     * Tabellen
     */
    public array $tore_tabelle;
    public array $turnier_tabelle;
    public array $platzierungstabelle = [];
    public array $direkter_vergleich_tabellen = [];
    public array $penalty_tabellen = [];

    /**
     * Penaltys
     *
     * gesamt => Alle Spiel-Ids für Penaltys
     * ausstehend => Ausstehende Spiel-IDs für Penaltys
     * gesamt => Team-ids der ausstehenden Penaltys
     */
    public array $penaltys = [
        'gesamt' => [],
        'ausstehend' => [],
        'kontrolle' => []
    ];

    /**
     * Zweite Runde Penaltys sind out of scope
     */
    public bool $out_of_scope = false;


    /**
     * Spielplan constructor.
     *
     * @param Turnier $turnier
     * @param bool $penaltys Penaltys werden ignoriert. Dies ist für eine zweite Instanz der Klasse, aus welcher die
     * gesamt zu spielenden Penaltys in Erfahrung gebracht werden.
     */
    function __construct(Turnier $turnier, bool $penaltys = true)
    {
        // Turnier
        $this->turnier_id = $turnier->id;
        $this->turnier = $turnier;

        // Spielplan
        $this->teamliste = $this->turnier->get_liste_spielplan();
        $this->anzahl_teams = count($this->teamliste);
        $this->anzahl_spiele = $this->anzahl_teams - 1;
        $this->details = $this->get_details();
        $this->spiele = $this->get_spiele();

        // Turniertabellen
        $this->tore_tabelle = $this->get_toretabelle($penaltys);
        $this->turnier_tabelle = self::get_sorted_turniertabelle($this->tore_tabelle);
        $this->set_platzierungen($this->tore_tabelle);
        $this->set_wertigkeiten();

        if (!empty($this->penaltys['kontrolle']) && $this->check_turnier_beendet()) {
            $this->out_of_scope = true;
        }
    }

    /**
     * Erstellt einen Spielplan in der Datenbank
     *
     * @param Turnier $turnier
     * @return bool Erfolgreich / Nicht erfolgreich estellt
     */
    public static function set_spielplan(Turnier $turnier): bool
    {
        if (Spielplan::check_exist($turnier->id)) {
            Form::error("Es existiert bereits ein Spielplan");
            return false;
        }
        $spielplan_art = $turnier->details["spielplan"];
        $teamliste = $turnier->get_liste_spielplan();
        $anzahl_teams = count($teamliste);

        // Teamlisten-Array mit 1 Beginnen lassen zum Ausfüllen der Spielplan-Vorlage
        $teamliste = array_values($teamliste);
        array_unshift($teamliste, '');
        unset($teamliste[0]);

        // Spielplanvorlage aus der Datenbank
        $sql = "
                SELECT * 
                FROM spielplan_paarungen 
                WHERE plaetze = ?
                AND spielplan = ?
                ";
        $paarungen = dbi::$db->query($sql, $anzahl_teams, $spielplan_art)->fetch();
        foreach ($paarungen as $spiel){
            $sql_inserts[] = "("
                . $turnier->id . "," . $spiel["spiel_id"] . ","
                . $teamliste[$spiel["team_a"]]["team_id"] . ","
                . $teamliste[$spiel["team_b"]]["team_id"] . ","
                . $teamliste[$spiel["schiri_a"]]["team_id"] . ","
                . $teamliste[$spiel["schiri_b"]]["team_id"] . ", "
                . "NULL, NULL, NULL, NULL)";
        }
        if (!isset($sql_inserts)) {
            Form::error("Es konnte keine Spielreihenfolge aus dem Spielplan ermittelt werden");
            return false;
        }

        // Spielplan erstellen
        $sql = "
                INSERT INTO spiele 
                VALUES " . implode(', ', $sql_inserts) . "
                ";
        dbi::$db->query($sql)->log();

        // Turnierlog
        $turnier->log("Automatischer " . $anzahl_teams . "er-JgJ-Spielplan erstellt.");
        $turnier->set_phase('spielplan');
        return true;
    }

    /**
     * Löscht einen bisher erstellten Spielplan
     *
     * @param Turnier $turnier
     */
    public static function delete_spielplan(Turnier $turnier)
    {
        // Es existiert kein dynamischer Spielplan
        if (!self::check_exist($turnier->id)) return;

        // Spielplan löschen
        $sql = "
                DELETE FROM spiele 
                WHERE turnier_id = $turnier->id
                ";
        dbi::$db->query($sql)->log();
        $turnier->log("Automatischer JgJ-Spielplan gelöscht.");
        $turnier->set_phase('melde');
    }

    /**
     * Holt sich die Spielplandetails aus der DB
     *
     * @return array Array der Details
     */
    public function get_details(): array
    {
        $sql = "
                SELECT * 
                FROM spielplan_details 
                WHERE plaetze = ?
                AND spielplan = ?
                ";
        return dbi::$db->query($sql, $this->anzahl_teams, $this->turnier->details["spielplan"])->esc()->fetch_row();
    }

    /**
     * Gibt ein Array der Spiele aus dem in der Datenbank hinterlegten Spielplan
     *
     * @return array
     */
    public function get_spiele(): array
    {
        $startzeit = strtotime($this->turnier->details["startzeit"]);
        $spielzeit = ($this->details["anzahl_halbzeiten"] * $this->details["halbzeit_laenge"]
                + $this->details["pause"]) * 60; // In Sekunden für Unixzeit
        $sql = "
                SELECT spiel_id, team_id_a, t1.teamname AS teamname_a, team_id_b, t2.teamname AS teamname_b,
                schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b
                FROM spiele sp, teams_liga t1, teams_liga t2
                WHERE turnier_id = $this->turnier_id
                AND team_id_a = t1.team_id
                AND team_id_b = t2.team_id
                ORDER BY spiel_id
                ";
        $spiele = dbi::$db->query($sql)->esc()->fetch('spiel_id');
        foreach($spiele as $spiel_id => $spiel) {
            $spiele[$spiel_id]["zeit"] = date("H:i", $startzeit);
            // 4er Spielplan Extrapause nach geraden Spielen
            $extra_pause = ($this->details['plaetze'] == 4 && !($spiel['spiel_id'] % 2)) ? (30 * 60) : 0;
            $startzeit += $spielzeit + $extra_pause;
        }
        return $spiele;
    }

    /**
     * Gibt den String der Penaltywarnung der austehenden Penaltys aus.
     *
     * @return string
     */
    public function get_penalty_warnung(): string
    {
        foreach ($this->penaltys['ausstehend'] as $spiel_id) {
            $penaltys[] = $this->spiele[$spiel_id]['teamname_a'] . ' | ' . $this->spiele[$spiel_id]['teamname_b'];
        }
        return implode('<br>', $penaltys ?? []);
    }

    /**
     * Schreibt ein Spielergebnis in die Datenbank
     *
     * @param int $spiel_id
     * @param string $tore_a
     * @param string $tore_b
     * @param string $penalty_a
     * @param string $penalty_b
     */
    public function set_tore(int $spiel_id, string $tore_a, string $tore_b, string $penalty_a, string $penalty_b)
    {
        // Damit die nicht eingetragene Tore nicht als 0 : 0 gewertet werden, müssen sie NULL sein
        $tore_a = !is_numeric($tore_a) ? 'NULL' : $tore_a;
        $tore_b = !is_numeric($tore_b) ? 'NULL' : $tore_b;
        $penalty_a = !is_numeric($penalty_a) ? 'NULL' : $penalty_a;
        $penalty_b = !is_numeric($penalty_b) ? 'NULL' : $penalty_b;

        $sql = "
                UPDATE spiele 
                SET tore_a = ?, tore_b = ?, penalty_a = ?, penalty_b = ?
                WHERE turnier_id = $this->turnier_id AND spiel_id = ?
                ";
        $params = [$tore_a, $tore_b, $penalty_a, $penalty_b, $spiel_id];
        dbi::$db->query($sql, $params)->log();
    }

    /**
     * Gibt eine Tabelle der Spielergebnisse der Teams untereinander aus.
     * Für den direkten Vergleich wichtig.
     *
     * @param bool $penaltys Mit oder ohne Penalty-Ergebnissen
     * @return array Torematrix aller Teams untereinander
     */
    public function get_toretabelle($penaltys = true): array
    {
        foreach ($this->spiele as $spiel) {

            if (!$penaltys) $spiel['penalty_a'] = $spiel['penalty_b'] = NULL;

            $tore_tabelle[$spiel['team_id_a']][$spiel['team_id_b']] =
                [
                    'tore' => $spiel['tore_a'],
                    'gegentore' => $spiel['tore_b'],
                    'penalty_tore' => $spiel['penalty_a'],
                    'penalty_gegentore' => $spiel['penalty_b'],
                ];
            $tore_tabelle[$spiel['team_id_b']][$spiel['team_id_a']] =
                [
                    'tore' => $spiel['tore_b'],
                    'gegentore' => $spiel['tore_a'],
                    'penalty_tore' => $spiel['penalty_b'],
                    'penalty_gegentore' => $spiel['penalty_a'],
                ];
        }
        return $tore_tabelle ?? [];
    }

    /**
     * Sortiert die Turniertabelle
     *
     * @param array $tore_tabelle Toretabelle aus get_toretabelle
     * @return array Sortierte Turniertabelle
     */
    public static function get_sorted_turniertabelle(array $tore_tabelle): array
    {
        $sort_function = function ($ergebnis_a, $ergebnis_b) {
            if ($ergebnis_a['punkte'] > $ergebnis_b['punkte']) return -1;
            if ($ergebnis_a['punkte'] < $ergebnis_b['punkte']) return 1;
            if ($ergebnis_a['tordifferenz'] > $ergebnis_b['tordifferenz']) return -1;
            if ($ergebnis_a['tordifferenz'] < $ergebnis_b['tordifferenz']) return 1;
            if ($ergebnis_a['tore'] > $ergebnis_b['tore']) return -1;
            if ($ergebnis_a['tore'] < $ergebnis_b['tore']) return 1;
            if ($ergebnis_a['penalty_punkte'] > $ergebnis_b['penalty_punkte']) return -1;
            if ($ergebnis_a['penalty_punkte'] < $ergebnis_b['penalty_punkte']) return 1;
            if ($ergebnis_a['penalty_diff'] > $ergebnis_b['penalty_diff']) return -1;
            if ($ergebnis_a['penalty_diff'] < $ergebnis_b['penalty_diff']) return 1;
            if ($ergebnis_a['penalty_tore'] > $ergebnis_b['penalty_tore']) return -1;
            if ($ergebnis_a['penalty_tore'] < $ergebnis_b['penalty_tore']) return 1;
            return -1; // Team welches links steht kommt nach oben, also das Team mit der höheren Rangtabellenwertung
        };
        $turnier_tabelle = self::get_turniertabelle($tore_tabelle);
        uasort($turnier_tabelle, $sort_function);
        return $turnier_tabelle;
    }

    /**
     * Erstellt eine Turniertabelle mit Punkten, Tordifferenz, etc.
     *
     * @param array $tore_tabelle Toretabelle aus get_toretabelle()
     * @return array unsortierte Turniertabelle
     */
    private static function get_turniertabelle(array $tore_tabelle): array
    {
        // Punkte zählen
        foreach ($tore_tabelle as $team_id => $team_spiele) {
            $punkte = $tordifferenz = $gegentore = $tore = $penalty_diff = $penalty_tore = $penalty_gegentore = $penalty_punkte = NULL;
            $spiele = $penalty_spiele = 0;
            foreach ($team_spiele as $spiel) {
                if (is_null($spiel['tore']) or is_null($spiel['gegentore'])) continue;
                $punkte += ($spiel['tore'] > $spiel['gegentore']) ? 3 : 0;
                $punkte += ($spiel['tore'] == $spiel['gegentore']) ? 1 : 0;
                $tordifferenz += $spiel['tore'] - $spiel['gegentore'];
                $tore += $spiel['tore'];
                $gegentore += $spiel['gegentore'];
                $spiele += 1;

                if (is_null($spiel['penalty_tore']) or is_null($spiel['penalty_gegentore'])) continue;
                $penalty_punkte += ($spiel['penalty_tore'] > $spiel['penalty_gegentore']) ? 3 : 0;
                $penalty_punkte += ($spiel['penalty_tore'] == $spiel['penalty_gegentore']) ? 1 : 0;
                $penalty_diff += $spiel['penalty_tore'] - $spiel['penalty_gegentore'];
                $penalty_tore += $spiel['penalty_tore'];
                $penalty_gegentore += $spiel['penalty_gegentore'];
                $penalty_spiele += 1;
            }
            // Turniertabelle beschreiben
            $turnier_tabelle[$team_id] =
                [
                    'spiele' => $spiele,
                    'punkte' => $punkte,
                    'penalty_spiele' => $penalty_spiele,
                    'tordifferenz' => $tordifferenz,
                    'tore' => $tore,
                    'gegentore' => $gegentore,
                    'penalty_punkte' => $penalty_punkte,
                    'penalty_diff' => $penalty_diff,
                    'penalty_tore' => $penalty_tore,
                    'penalty_gegentore' => $penalty_gegentore
                ];
        }
        return $turnier_tabelle ?? [];
    }

    /**
     * Gibt die Spiele-IDs zurück, in denen die Team-IDs gegeneinander spielen.
     * @param array $team_ids
     * @return array
     */
    public function get_spiel_ids(array $team_ids): array
    {
        foreach ($this->spiele as $spiel_id => $spiel) {
            if (
                in_array($spiel['team_id_a'], $team_ids)
                && in_array($spiel['team_id_b'], $team_ids)
            ) $return[] = $spiel_id;
        }
        return $return ?? [];
    }

    /**
     * Gibt ein Array der team_ids von mit einem Team gleichplatzierten Teams.
     * Gibt nur eine team_id aus, wenn das übergebene Team nur mit sich selbst gleichplatziert ist,
     * also eindeutig Platzierbar ist.
     *
     * @param array $turnier_tabelle Turniertabelle als Grundlage für Gleichplatzierung
     * @param int $team_id Team-ID des Teams, nach dem gleichplatzierte Teams gesucht werden sollen
     * @param string $art Um welche art des Vergleiches handelt es sich?
     * @return array Array der team_ids
     */
    private function get_gleichplatzierte_teams(array $turnier_tabelle, int $team_id, $art = 'erster_vergleich'): array
    {
        $match = $turnier_tabelle[$team_id];
        unset($match['spiele'], $match['penalty_spiele']);
        // Anzahl der Spiele und Anzahl der Penaltys sollen nicht berücksichtigt werden.
        if ($art == 'erster_vergleich') {
            $function = function ($value) use ($match) {
                return $value['punkte'] == $match['punkte'];
            };
        } elseif ($art == 'direkter_vergleich') {
            $function = function ($value) use ($match) {
                return ($value['punkte'] == $match['punkte']
                    && $value['tordifferenz'] == $match['tordifferenz']
                    && $value['tore'] == $match['tore']);
            };
        } else {
            $function = function ($value) use ($match) {
                return ($value['penalty_punkte'] == $match['penalty_punkte']
                    && $value['penalty_diff'] == $match['penalty_diff']
                    && $value['penalty_tore'] == $match['penalty_tore']);
            };
        }
        return array_keys(array_filter($turnier_tabelle, $function)); // Wenn es mehrere gleiche Teams gibt: false
    }


    /**
     * Sortiert die Turniertabelle und wendet ggf. den direkten Vergleich an.
     *
     * @param array $tore_tabelle
     */
    public function set_platzierungen(array $tore_tabelle)
    {
        $turnier_tabelle = self::get_sorted_turniertabelle($tore_tabelle); // neue Turniertabelle erstellen
        // Mit dem ersten Team gleichplatzierte Teams suchen
        $first_team_id = array_key_first($turnier_tabelle);
        $gleichplatzierte_teams = $this->get_gleichplatzierte_teams($turnier_tabelle, $first_team_id, "erster_vergleich");

        // Fall 1: Team ist eindeutig platzierbar, da das erste Team in der sortierten Turniertabelle
        // nur mit sich selbst gleichplatziert ist.
        if (count($gleichplatzierte_teams) === 1) {
            $this->set_platzierung($first_team_id);
            self::remove_team_ids($tore_tabelle, [$first_team_id]); // Werden aus der Toretabelle entfernt
            if (count($tore_tabelle) != 0) self::set_platzierungen($tore_tabelle);
        } else {
            // Direkter Vergleich mit nur den gleichplatzierten Teams in den nicht-ersten Vergleich
            $tore_tabelle_gleiche_teams = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
            self::direkter_vergleich($tore_tabelle_gleiche_teams, true);

            // Forführung des ersten Vergleichs ohne die gleichplatzierten Teams
            self::remove_team_ids($tore_tabelle, $gleichplatzierte_teams);
            if (count($tore_tabelle) != 0) self::set_platzierungen($tore_tabelle);
        }
        if (count($tore_tabelle) == 0) { // Zuletzt werden die noch zu spielenden Penaltys ermittelt
            foreach ($this->penaltys['gesamt'] as $spiel_id) {
                if (
                    is_null($this->spiele[$spiel_id]['penalty_a'])
                    or is_null($this->spiele[$spiel_id]['penalty_b'])
                ) {
                    $this->penaltys['ausstehend'][] = $spiel_id;
                }
            }
        }
    }

    /**
     * Direkter Vergleich
     *
     * @param $tore_tabelle
     * @param false $print Soll er ausgegeben werden?
     */
    public function direkter_vergleich(array $tore_tabelle, bool $print = false)
    {
        // Fall 0: Nur ein Team verblieben
        if (count($tore_tabelle) == 1) {
            $this->set_platzierung(array_key_first($tore_tabelle));
            return;
        }
        $turnier_tabelle = self::get_sorted_turniertabelle($tore_tabelle); // neue Turniertabelle erstellen
        // Direktervergleich Tabelle ausgeben
        if ($print && $this->check_ergebnis_fix(array_keys($tore_tabelle)))
            $this->direkter_vergleich_tabellen[] = $turnier_tabelle;

        // Mit dem ersten Team gleichplatzierte Teams suchen
        $first_team_id = array_key_first($turnier_tabelle);
        $gleichplatzierte_teams = $this->get_gleichplatzierte_teams($turnier_tabelle, $first_team_id, "direkter_vergleich");

        // Fall 1: Team ist eindeutig platzierbar, da das erste Team in der sortierten Turniertabelle
        // nur mit sich selbst gleichplatziert ist.
        if (count($gleichplatzierte_teams) === 1) {
            $this->set_platzierung($first_team_id);
            self::remove_team_ids($tore_tabelle, [$first_team_id]); // Werden aus der Toretabelle entfernt
            if (count($tore_tabelle) != 0) self::direkter_vergleich($tore_tabelle);
            return;
        }

        // Fall 2: Team ist nicht eindeutig platzierbar, es muss ein neuer direkter Vergleich mit Untertabelle erstellt werden
        if (count($gleichplatzierte_teams) < count($turnier_tabelle)) {
            // Toretabelle mit nur den gleichplatzierten Teams in den nicht-ersten Vergleich
            $tore_tabelle_gleiche_teams = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
            self::direkter_vergleich($tore_tabelle_gleiche_teams, true);
            // Toretabelle ohne die gleichplatzierten Teams
            self::remove_team_ids($tore_tabelle, $gleichplatzierte_teams);
            if (count($tore_tabelle) != 0) self::direkter_vergleich($tore_tabelle);
            return;
        }

        // Fall 3:
        // Tabelle besteht nur aus gleichplatzierten Teams also ab in den Penalty-Vergleich
        // Mit einer Tortabelle, in welcher nur die Spiele der gleichplatzierten Teams gezählt werden
        $tore_tabelle_gefiltert = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
        if ($tore_tabelle != $tore_tabelle_gefiltert) {
            self::direkter_vergleich($tore_tabelle_gefiltert, true);
        } else {
            if ($this->check_ergebnis_fix($gleichplatzierte_teams))
                $this->penaltys['gesamt'] =
                    array_merge($this->penaltys['gesamt'], $this->get_spiel_ids($gleichplatzierte_teams));
            self::penalty_vergleich($tore_tabelle, true);
        }
    }

    /**
     * Direkter Vergleich der Penalty-Begegenungen
     *
     * @param array $tore_tabelle
     * @param bool $print
     */
    public function penalty_vergleich(array $tore_tabelle, bool $print = false)
    {
        // Fall 0: Nur ein Team verblieben
        if (count($tore_tabelle) == 1) {
            $this->set_platzierung(array_key_first($tore_tabelle));
            return;
        }
        // neue Turniertabelle erstellen und ggf ausgeben
        $turnier_tabelle = self::get_sorted_turniertabelle($tore_tabelle);
        if ($print && $this->check_ergebnis_fix(array_keys($turnier_tabelle)))
            $this->penalty_tabellen[] = $turnier_tabelle;
        // Mit dem ersten Team gleichplatzierte Teams suchen
        $first_team_id = array_key_first($turnier_tabelle);
        $gleichplatzierte_teams = $this->get_gleichplatzierte_teams($turnier_tabelle, $first_team_id, "penalty_vergleich");
        if (count($gleichplatzierte_teams) === 1) {
            $this->set_platzierung($first_team_id);
            self::remove_team_ids($tore_tabelle, [$first_team_id]); // Werden aus der Toretabelle entfernt
            if (count($tore_tabelle) != 0) self::penalty_vergleich($tore_tabelle);
            return;
        }

        // Fall 2: Team ist nicht eindeutig platzierbar, es kann ein neuer Vergleich mit Untertabelle erstellt werden
        if (count($gleichplatzierte_teams) < count($turnier_tabelle)) {
            // Tabelle mit nur den gleichplatzierten Teams und deren Spiele
            $tore_tabelle_gleiche_teams = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
            self::penalty_vergleich($tore_tabelle_gleiche_teams, true);
            // Tabelle ohne die gleichplatzierten Teams
            self::remove_team_ids($tore_tabelle, $gleichplatzierte_teams);
            if (count($tore_tabelle) != 0) self::penalty_vergleich($tore_tabelle);
            return;
        }

        // Fall 3: Team ist nicht eindeutig platzierbar, ein neuer Vergleich ändert nichts
        if (self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams) != $tore_tabelle) {
            $this->penalty_vergleich(self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams), true);
        } else {
//            if ($this->anzahl_spiele == min(array_column($this->turnier_tabelle, 'spiele')))
            $this->penaltys['kontrolle'] = $this->get_spiel_ids($gleichplatzierte_teams);
            // Eine weitere Sortierung ist nicht mehr möglich, Penaltys müssen gespielt werden
            foreach ($gleichplatzierte_teams as $team_id) {
                $this->set_platzierung($team_id);
            }
        }
    }

    /**
     * Platziert ein Team in $this->platzierungstabelle
     *
     * @param int $team_id Team-ID des Teams, welches platziert werden soll
     */
    private function set_platzierung(int $team_id)
    {
        $this->platzierungstabelle[$team_id] =
            [
                'platz' => count($this->platzierungstabelle) + 1,
                'teamname' => $this->teamliste[$team_id]['teamname'],
                'ligapunkte' => 0,
                'statistik' => $this->turnier_tabelle[$team_id],
            ];
    }

    /**
     * Entfernt Teams aus der Toretabelle, nicht jedoch aus Unter-Torebegegnungs-Tabelle
     *
     * @param array $tore_tabelle Toretabelle als Grundlage
     * @param array $team_ids Teams die Entfernt werden
     */
    private static function remove_team_ids(array &$tore_tabelle, array $team_ids)
    {
        foreach ($team_ids as $team_id) {
            unset($tore_tabelle[$team_id]);
        }
    }

    /**
     * Filtert eine Toretabelle nach Teams und erstellt so eine Untertabelle für den direkten Vergleich mit nur
     * noch diesen Teams und Begegnungen dieser Teams untereinander.
     *
     * @param array $tore_tabelle Toretabelle als Grundlage
     * @param array $team_ids Liste an TeamIDs nach welchen
     * @return array Neue Toretabelle mit noch den Team-IDs wird zurückgegeben.
     */
    private static function filter_team_ids(array $tore_tabelle, array $team_ids): array
    {
        $filter_function = function ($team_id) use ($team_ids) {
            return in_array($team_id, $team_ids); // Alle Team-IDs, bis auf die Übergebenen, werden entfernt
        };
        foreach ($tore_tabelle as &$ergebnis) {
            $ergebnis = array_filter($ergebnis, $filter_function, ARRAY_FILTER_USE_KEY);
        }
        return array_filter($tore_tabelle, $filter_function, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Fügt die Teamwertigkeiten in die Platzierungstabelle ein.
     */
    public function set_wertigkeiten()
    {
        $reverse_tabelle = array_reverse($this->platzierungstabelle, true);

        $highest_ligateam = function () use ($reverse_tabelle) {
            foreach ($reverse_tabelle as $team_id => $eintrag) {
                if ($this->teamliste[$team_id]['wertigkeit'] !== 'NL') return $this->teamliste[$team_id]['wertigkeit'];
            }
            return 0;
        };

        $ligapunkte = 0;
        foreach ($reverse_tabelle as $team_id => $eintrag) {
            $wert = $this->teamliste[$team_id]['wertigkeit'];
            $wert = ($wert === 'NL') ? max($werte ?? [max(round($highest_ligateam() / 2) - 1, 14)]) + 1 : $wert;
            $werte[] = $wert;
            $ligapunkte += $wert;
            $this->platzierungstabelle[$team_id]['ligapunkte'] = round($ligapunkte * 6 / $this->details['faktor']);
        }
    }

    /**
     * Existiert ein automatisch oder manuell erstellter Spielplan in der Datenbank?
     *
     * @param int $turnier_id
     * @return bool
     */
    public static function check_exist(int $turnier_id): bool
    {
        // Automatischer Spielplan existiert
        $sql = "
                SELECT *
                FROM spiele
                WHERE turnier_id = ?;
                ";
        return dbi::$db->query($sql, $turnier_id)->num_rows() > 0;
    }

    /**
     * Check, ob das Turnier beendet wurde
     *
     * @return bool true, wenn keine Spiele und Penalty-Begegnungen ausstehen.
     */
    function check_turnier_beendet(): bool
    {
        if (!empty($this->penaltys['ausstehend'])) return false;
        $min_spiele = min(array_column($this->turnier_tabelle, 'spiele'));
        // kleinste Anzahl an beendeten Spielen eines Teams
        return $this->anzahl_spiele == $min_spiele;
    }

    /**
     * Ist die Penaltybegegnung unvermeidbar?
     * @param array $team_ids Array der Penalty-Teams
     * @return bool
     */
    public function check_ergebnis_fix(array $team_ids): bool
    {
        // Hilfsfunktion für erreichbare Punkte eines Teams
        $vergleich = function ($team_id) {
            $return['punkte_min'] = $this->turnier_tabelle[$team_id]['punkte'];
            $return['punkte_max'] =
                $return['punkte_min'] + ($this->anzahl_spiele - $this->turnier_tabelle[$team_id]['spiele']) * 3;
            $return['nicht_erreichbar'] = $return['punkte_max'] - 1;
            return $return;
        };

        foreach ($team_ids as $team_id) {
            if ($this->turnier_tabelle[$team_id]['spiele'] < $this->anzahl_spiele) {
                return false; // Penaltybegegnung  vermeidbar, da noch nicht alle Spiele vom Team absolviert
            }
            $punkte_pen_team = $this->turnier_tabelle[$team_id]['punkte'];

            foreach (array_keys($this->turnier_tabelle) as $vgl_team_id) {
                if (in_array($vgl_team_id, $team_ids)) continue; // Nicht mit sich selbst vergleichen
                // Penaltybegnung vermeidbar, da ein Team die Punktzahl des Penalty-Teams noch erreichen könnte?
                if (
                    (
                        $vergleich($vgl_team_id)['punkte_max'] != $vergleich($vgl_team_id)['punkte_min']
                        && $vergleich($vgl_team_id)['punkte_max'] == $punkte_pen_team
                    )
                    && $punkte_pen_team <= $vergleich($vgl_team_id)['punkte_max']
                    && $punkte_pen_team >= $vergleich($vgl_team_id)['punkte_min']
                    && $punkte_pen_team != $vergleich($vgl_team_id)['nicht_erreichbar']
                ) return false;
            } // foreach Teams auf dem Turnier
        } // foreach Penalty-Teams
        return true;
    }

    /**
     * Überprüft ob Penaltyfelder zum Eintragen freigegeben werden.
     *
     * @param int $spiel_id
     * @param bool $ausstehend Ausstehende oder allgemeine Penalty-Teams?
     * @return bool True, wenn ein Penalty gespielt werden muss.
     */
    public function check_penalty_spiel(int $spiel_id, bool $ausstehend = false): bool
    {
        $penaltys = ($ausstehend) ? $this->penaltys['ausstehend'] : $this->penaltys['gesamt'];
        return in_array($spiel_id, $penaltys);
    }

    /**
     * Muss das Team einen Penalty spielen?
     *
     * @param int $team_id
     * @return bool
     */
    public function check_penalty_team(int $team_id): bool
    {
        foreach ($this->penaltys['ausstehend'] as $spiel_id) {
            if (
                $this->spiele[$spiel_id]['team_id_a'] == $team_id
                or $this->spiele[$spiel_id]['team_id_b'] == $team_id
            ) return true;
        }
        return false;
    }

    /**
     * Check, ob jedes Team ein Team gespielt hat.
     * Wenn ja, wird die Platzierung und das Turnierergebnis im Template angezeigt
     *
     * @return bool true, wenn alle mind. ein Spiel gespielt haben
     */
    function check_tabelle_einblenden(): bool
    {
        // Team mit der kleinsten Anzahl an Spielen hat mehr als 0 Spiele vollendet
        return 0 < min(array_column($this->turnier_tabelle, 'spiele'));
    }

    /**
     * Penaltyspalte nur Anzeigen, wenn auch Penaltys gespielt werden müssen
     *
     * @return bool True, wenn Penaltys vorhanden sind.
     */
    public function check_penalty_anzeigen(): bool
    {
        if (!$this->validate_penalty_ergebnisse()) return true;
        return !empty($this->penaltys['gesamt']);
    }

    /**
     * Wurde der Penalty vom Spiel richtig eingetragen?
     *
     * @param array $spiel
     * @return bool
     */
    public function validate_penalty_spiel(array $spiel): bool
    {
        return (
                !is_null($spiel["penalty_a"])
                or !is_null($spiel["penalty_b"])
            )
            && !$this->check_penalty_spiel($spiel["spiel_id"]);
    }

    /**
     * Überprüft, ob Penalty-Ergebnisse bei den richtigen Teams eingetragen worden sind.
     *
     * @return bool
     */
    public function validate_penalty_ergebnisse(): bool
    {
        foreach ($this->spiele as $spiel_id => $spiel) {
            if ((!is_null($spiel['penalty_a']) or !is_null($spiel['penalty_b']))
                && !in_array($spiel_id, $this->penaltys['gesamt'])
            ) return false;
            // Es wurde also ein Penalty bei einem Spiel eingetragen, bei welchem kein Penalty vorgesehen ist.
        }
        return true;
    }

    /**
     * Findet die beste Trikotfarbenkombination für ein Spiel
     *
     * @param array $spiel
     * @return array
     */
    public function get_trikot_colors(array $spiel): array
    {
        if ($this->turnier->details['phase'] == 'ergebnis') return [];
        $team_id_a = $spiel['team_id_a'];
        $team_id_b = $spiel['team_id_b'];
        $farben = [
            $team_id_a  => [
                1 => $this->teamliste[$spiel['team_id_a']]['trikot_farbe_1'],
                2 => $this->teamliste[$spiel['team_id_a']]['trikot_farbe_2']
            ],
            $team_id_b => [
                1 => $this->teamliste[$spiel['team_id_b']]['trikot_farbe_1'],
                2 => $this->teamliste[$spiel['team_id_b']]['trikot_farbe_2']
            ]
        ];

        // Nicht hinterlegte Farben entfernen
        $farben[$team_id_a] = array_filter($farben[$team_id_a]);
        $farben[$team_id_b] = array_filter($farben[$team_id_b]);

        if (
            empty($farben[$team_id_a])
            or empty($farben[$team_id_b])
        ) return [];

        // Hexfarbe in RGB umwandeln
        $get_delta_e = function ($hex_color_1, $hex_color_2) {
            [$r_1, $g_1, $b_1] = sscanf($hex_color_1, "#%02x%02x%02x");
            [$r_2, $g_2, $b_2] = sscanf($hex_color_2, "#%02x%02x%02x");
            $r_m = ($r_1 + $r_2) / 2;
            $r_d = $r_1 - $r_2;
            $g_d = $g_1 - $g_2;
            $b_d = $b_1 - $b_2;
            return ((2 + $r_m / 256) * $r_d ** 2 + 4 * $g_d ** 2 + (2 + (255 - $r_m) / 256) * $b_d ** 2) ** 0.5;
        };

        foreach($farben[$team_id_a] as $farbe_a){
            foreach ($farben[$team_id_b] as $farbe_b){
                $delta_e = $get_delta_e($farbe_a, $farbe_b);
                if ($delta_e > ($max_delta_e ?? 0)){
                    if (($max_delta_e ?? 0) > 550) continue; // 550 Threshold inwiefern Trikotfarbe 1 ausreichend ist
                    $max_delta_e = $delta_e;

                    $return[$team_id_a] = "<span class='w3-card-4' style='height:11px;width:11px;background-color:$farbe_a;border-radius:50%;display:inline-block;'></span>";
                    $return[$team_id_b] = "<span class='w3-card-4' style='height:11px;width:11px;background-color:$farbe_b;border-radius:50%;display:inline-block;'></span>";
                }
            }
        }

        return $return ?? [];
    }


}
