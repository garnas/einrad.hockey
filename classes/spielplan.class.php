<?php

/**
 * Class Spielplan
 *
 * Verwaltet Spielpläne, berechnet Turniertabellen und schreibt Turnierergebnisse in die DB.
 */
class Spielplan
{
    public int $turnier_id;
    public Turnier $turnier;
    public array $teamliste;
    public int $anzahl_teams;
    public int $anzahl_spiele;
    public array $platzierungstabelle = [];
    public array $direkter_vergleich_tabellen = [];
    public array $tore_tabelle;
    public array $turnier_tabelle;
    public array $details;
    public array $ausstehende_penaltys = [];
    public array $gesamt_penaltys = [];
    public array $spiele;

    /**
     * Spielplan constructor.
     *
     * @param Turnier $turnier
     * @param bool $with_penaltys Es wird ein zweites Objekt Spielplan erstellt, um Penalty-Begegnungen festzustellen.
     */
    function __construct(Turnier $turnier, $with_penaltys = true)
    {
        $this->turnier_id = $turnier->turnier_id;
        $this->turnier = $turnier;
        $this->teamliste = $this->turnier->get_liste_spielplan();
        $this->anzahl_teams = count($this->teamliste);
        $this->anzahl_spiele = $this->anzahl_teams - 1;
        $this->details = self::get_details();
        $this->spiele = $this->get_spiele();
        $this->tore_tabelle = $this->get_toretabelle();
        $this->turnier_tabelle = self::get_sorted_turniertabelle($this->tore_tabelle);
        if ($with_penaltys) $this->gesamt_penaltys = $this->get_gesamt_penaltys();
        $this->direkter_vergleich($this->tore_tabelle);
        $this->filter_ausstehende_penalty_begegnungen(); //$this->ausstehende_penaltys wird gesetzt
        $this->set_wertigkeiten(); // $this->platziierungstabelle wird gesetzt
    }

    /**
     * Holt sich die Spielplandetails aus der DB
     *
     * @return array Array der Details
     */
    public function get_details(): array
    {
        $plaetze = $this->anzahl_teams;
        $spielplan = $this->turnier->details["spielplan"];
        $sql = "
                SELECT * 
                FROM spielplan_details 
                WHERE plaetze = '$plaetze' 
                AND spielplan = '$spielplan'
                ";
        $result = db::read($sql);
        return db::escape(mysqli_fetch_assoc($result));
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
        $result = db::read($sql);
        while ($spiel = mysqli_fetch_assoc($result)) {
            $spiel["zeit"] = date("H:i", $startzeit);
            $spiele[$spiel['spiel_id']] = $spiel;
            $extra_pause = ($this->details['plaetze'] == 4 && !($spiel['spiel_id'] % 2)) ? (30 * 60) : 0;
                // 4er Spielplan Extrapause nach geraden Spielen
            $startzeit += $spielzeit + $extra_pause;
        }
        return db::escape($spiele ?? []);
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
    public function set_spiele(int $spiel_id, string $tore_a, string $tore_b, string $penalty_a, string $penalty_b)
    {
        // Damit die nicht eingetragene Tore nicht als 0 : 0 gewertet werden, müssen sie NULL sein
        $tore_a = !is_numeric($tore_a) ? 'NULL' : $tore_a;
        $tore_b = !is_numeric($tore_b) ? 'NULL' : $tore_b;
        $penalty_a = !is_numeric($penalty_a) ? 'NULL' : $penalty_a;
        $penalty_b = !is_numeric($penalty_b) ? 'NULL' : $penalty_b;

        $sql = "
                UPDATE spiele 
                SET tore_a = $tore_a, tore_b = $tore_b, penalty_a = $penalty_a, penalty_b = $penalty_b
                WHERE turnier_id = $this->turnier_id AND spiel_id = $spiel_id
                ";
        db::write($sql);
    }

    /**
     * Gibt eine Tabelle der Spielergebnisse der Teams untereinander aus.
     * Für den direkten Vergleich wichtig.
     *
     * @return array Torematrix aller Teams untereinander
     */
    public function get_toretabelle(): array
    {
        foreach ($this->spiele as $spiel) {
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
            $punkte = $tordifferenz = $gegentore = $tore = $penalty_diff = $penalty_tore = $penalty_punkte = NULL;
            $spiele = 0;
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
            }
            // Turniertabelle beschreiben
            $turnier_tabelle[$team_id] =
                [
                    'spiele' => $spiele,
                    'punkte' => $punkte,
                    'tordifferenz' => $tordifferenz,
                    'tore' => $tore,
                    'gegentore' => $gegentore,
                    'penalty_punkte' => $penalty_punkte,
                    'penalty_diff' => $penalty_diff,
                    'penalty_tore' => $penalty_tore
                ];
        }
        return $turnier_tabelle ?? [];
    }

    /**
     * Es wird ein weiteres Objekt Spielplan instantiert, jedoch ohne die Berücksichtigung der Penalty-Ergebnisse
     * Somit bekommt man Teams, welche ein Penaltyschießen absolvieren mussten und kann falsch eingetragenen Penaltys
     * vorbeugen.
     *
     * @return array
     */
    public function get_gesamt_penaltys(): array
    {
        // Penalty-Tore auf NULL setzen
        $spielplan = new Spielplan($this->turnier, false);
        foreach ($spielplan->tore_tabelle as $team_id => $spiele) {
            foreach ($spiele as $team_id_gegner => $spiel) {
                // Zuerst wurden die Variablen per Referenz übergeben, dies führte in der Ausführung vom späteren
                // direkten Vergleich zu fehlern. Dieser Bug hat mich >3h gekostet und viele Nerven gekostet. Zur
                // Würdigung meiner Zeit wurde dieser Kommentar hier geschrieben.
                $spielplan->tore_tabelle[$team_id][$team_id_gegner]
                    = array_replace($spiel, ['penalty_tore' => NULL, 'penalty_gegentore' => NULL]);
            }
        }
        $spielplan->ausstehende_penaltys = []; //TODO das geht besser
        $spielplan->direkter_vergleich($spielplan->tore_tabelle);
        $spielplan->filter_ausstehende_penalty_begegnungen();
        return $spielplan->ausstehende_penaltys; // Alle Penaltyteams ohne schon ausgeführte Penaltys
    }

    /**
     * Ausführung des direkten Vergleiches. Das Endresultat wird in $this->platzeriungs_tabelle platziert.
     *
     * @param array $tore_tabelle Toretabelle, auf welcher Grundlage der Vergleich ausgeführt wird
     * @param bool $print Der direkte Vergleich soll im Spielplan angezeigt werden.
     * @param bool $direkter_vergleich Es ist ein direkter Vergleich, und die Erstsortierung nur nach erreichten Turnierpunkten
     */
    public function direkter_vergleich(array $tore_tabelle, bool $print = true, bool $direkter_vergleich = false)
    {
        $turnier_tabelle = self::get_sorted_turniertabelle($tore_tabelle); // neue Turniertabelle erstellen
        // Den direkten Vergleich drucken1
        if ($print && $direkter_vergleich && count($tore_tabelle) > 1) $this->print_direkter_vergleich($turnier_tabelle);
        // Mit dem ersten Team gleichplatzierte Teams suchen
        $first_team_id = array_key_first($turnier_tabelle);
        $gleichplatzierte_teams = $this->get_gleichplatzierte_teams($turnier_tabelle, $first_team_id, $direkter_vergleich);

        // Fall 1: Team ist eindeutig platzierbar, da das erste Team in der sortierten Turniertabelle
        // nur mit sich selbst gleichplatziert ist.
        if (count($gleichplatzierte_teams) === 1) {
            $this->set_platzierung($first_team_id);
            self::remove_team_ids($tore_tabelle, [$first_team_id]); // Werden aus der Toretabelle entfernt
            if (count($tore_tabelle) != 0) self::direkter_vergleich($tore_tabelle, false, $direkter_vergleich);
            return;
        }

        // Fall 2: Team ist nicht eindeutig platzierbar, es muss ein neuer direkter Vergleich mit Untertabelle erstellt werden
        if (count($gleichplatzierte_teams) < count($turnier_tabelle)) {
            // Toretabelle mit nur den gleichplatzierten Teams
            $tore_tabelle_gleiche_teams = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
            self::direkter_vergleich($tore_tabelle_gleiche_teams, true, true);
            // Toretabelle ohne die gleichplatzierten Teams
            self::remove_team_ids($tore_tabelle, $gleichplatzierte_teams);
            if (count($tore_tabelle) != 0) self::direkter_vergleich($tore_tabelle, true, $direkter_vergleich);
            return;
        }

        // Fall 3: Alle Teams in der Tabelle sind gleichplatziert, ein erneutes Anwenden des direkten Vergleichs hätte keine Wirkung mehr.
        if ($direkter_vergleich && self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams) == $tore_tabelle) {
            // Penalty-Schießen
            $penalty_teams = [];
            foreach ($gleichplatzierte_teams as $team_id) {
                $penalty_teams[$team_id] = Team::teamid_to_teamname($team_id);
                $this->set_platzierung($team_id, true);
            }
            $this->ausstehende_penaltys[] = $penalty_teams;
        } else {
            // Da wir nicht im direkten Vergleich waren, werden sie jetzt hineingeschickt mit den Begegnungen untereinander
            $tore_tabelle = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
            self::direkter_vergleich($tore_tabelle, true, true);
        }
    }

    /**
     * Gibt eine Untertabelle des direkten Vergleichs aus
     * Schreibt den html-String in $this->direkter_vergleich_tabellen
     *
     * @param array $turnier_tabelle Die dem direkten vergleich zugrundeliegende Untertabelle
     */
    function print_direkter_vergleich(array $turnier_tabelle)
    {
        $gleichplatzierte_teams =
            $this->get_gleichplatzierte_teams($turnier_tabelle, array_key_first($turnier_tabelle), true);
        $direkter_vergleich['penalty'] = ((count($gleichplatzierte_teams) == count($turnier_tabelle)));
        $direkter_vergleich['tabelle'] = $turnier_tabelle;

        $this->direkter_vergleich_tabellen[] = $direkter_vergleich;
    }

    /**
     * Gibt ein Array der team_ids von mit einem Team gleichplatzierten Teams.
     * Gibt nur eine team_id aus, wenn das übergebene Team nur mit sich selbst gleichplatziert ist,
     * also eindeutig Platzierbar ist.
     *
     * @param array $turnier_tabelle Turniertabelle als Grundlage für Gleichplatzierung
     * @param int $team_id Team-ID des Teams, nach dem gleichplatzierte Teams gesucht werden sollen
     * @param bool $direkter_vergleich Handelt sich um die Erstsortierung, oder um einen direkten Vergleich?
     * @return array Array der team_ids
     */
    private function get_gleichplatzierte_teams(array $turnier_tabelle, int $team_id, bool $direkter_vergleich = false): array
    {
        $match = $turnier_tabelle[$team_id];
        $function = function ($value) use ($match, $direkter_vergleich) {
            if ($direkter_vergleich) return $value == $match; // Teams mit identischer Direkter-Vergleich-Tabelle
            return $value['punkte'] == $match['punkte']; // Nur Punkte vergleichen, wenn nicht im direkten Vergleich
        };
        return array_keys(array_filter($turnier_tabelle, $function)); // Wenn es mehrere gleiche Teams gibt: false
    }

    /**
     * Platziert ein Team in $this->platzierungstabelle
     *
     * @param int $team_id Team-ID des Teams, welches platziert werden soll
     * @param bool $penalty Steht noch ein Penalty aus?
     */
    private function set_platzierung(int $team_id, bool $penalty = false)
    {
        $this->platzierungstabelle[$team_id] =
            [
                'platz' => count($this->platzierungstabelle) + 1,
                'teamname' => $this->teamliste[$team_id]['teamname'],
                'ligapunkte' => 0,
                'statistik' => $this->turnier_tabelle[$team_id],
                'penalty' => $penalty
            ];
    }

    /**
     * Fügt die Teamwertigkeiten in die Platzeriungstabelle ein.
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
     * Löscht alle Penalty-Begegnungen, außer denjenigen, welche unvermeidbar sind.
     */
    public function filter_ausstehende_penalty_begegnungen()
    {
        $vergleich = function ($team_id) {
            $return['punkte_min'] = $this->turnier_tabelle[$team_id]['punkte'];
            $return['punkte_max'] =
                $return['punkte_min'] + ($this->anzahl_spiele - $this->turnier_tabelle[$team_id]['spiele']) * 3;
            $return['nicht_erreichbar'] = $return['punkte_max'] - 1;
            return $return;
        };

        foreach ($this->ausstehende_penaltys as $key => $penalty_teams) {
            foreach (array_keys($penalty_teams) as $penalty_team_id) {
                if ($this->turnier_tabelle[$penalty_team_id]['spiele'] < $this->anzahl_spiele) {
                    unset($this->ausstehende_penaltys[$key]);
                    // Penaltybegnung wird gelöscht, da noch nicht alle Spiele von den Penaltyteams gespielt worden sind.
                    break;
                }
                $punkte_pen_team = $this->turnier_tabelle[$penalty_team_id]['punkte'];
                foreach (array_keys($this->turnier_tabelle) as $vgl_team_id) {
                    if ($penalty_team_id == $vgl_team_id) continue;
                    if ($punkte_pen_team < $vergleich($vgl_team_id)['punkte_max']
                        && $punkte_pen_team > $vergleich($vgl_team_id)['punkte_min']
                        && $punkte_pen_team != $vergleich($vgl_team_id)['nicht_erreichbar']) {
                        unset($this->ausstehende_penaltys[$key]);
                        // Penaltybegnung wird gelöscht, da ein Team die Punktzahl des Penalty-Teams noch erreichen könnte.
                        break;
                    } // if Punktzahl erreichbar
                } // foreach Teams auf dem Turnier
            } // foreach Penalty-Teams
        } // foreach Penalty-Begegnungen
    }

    /**
     * Erstellt einen Spielplan in der Datenbank
     *
     * @param Turnier $turnier
     * @return bool Erfolgreich / Nicht erfolgreich estellt
     */
    public static function set_spielplan(Turnier $turnier): bool
    {
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
                WHERE plaetze = '$anzahl_teams' 
                AND spielplan = '$spielplan_art'
                ";
        $result = db::read($sql);

        while ($spiel = mysqli_fetch_assoc($result)) {
            $sql_inserts[] = "("
                . $turnier->turnier_id . "," . $spiel["spiel_id"] . ","
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

        // Eventuell alten Spielpläne löschen
        Spielplan::delete_spielplan($turnier);

        // Neuen Spielplan erstellen
        $sql = "
                INSERT INTO spiele 
                VALUES " . implode(', ', $sql_inserts) . "
                ";
        db::write($sql);

        // Turnierlog
        $turnier->log("Dynamischer" . $anzahl_teams . "er JgJ-Spielplan erstellt.");
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
        if (!self::check_exist($turnier->turnier_id)) return;

        // Spielplan löschen
        $sql = "
                DELETE FROM spiele 
                WHERE turnier_id = $turnier->turnier_id
                ";
        db::write($sql);
        $turnier->log("Dynamischer JgJ-Spielplan gelöscht.");
        $turnier->set_phase('melde');
    }

    /**
     * Existiert ein automatisch erstellter Spielplan in der Datenbank?
     *
     * @param int $turnier_id
     * @return bool
     */
    public static function check_exist(int $turnier_id): bool
    {
        $sql = "
                SELECT *
                FROM spiele
                WHERE turnier_id = $turnier_id;
                ";
        return db::read($sql)->num_rows > 0;
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
        $penaltys = ($ausstehend) ? $this->ausstehende_penaltys : $this->gesamt_penaltys;
        foreach ($penaltys as $team_ids) {
            if (array_key_exists($this->spiele[$spiel_id]['team_id_a'], $team_ids)
                && array_key_exists($this->spiele[$spiel_id]['team_id_b'], $team_ids)) return true;
            return false;
        }
        return false;
    }
    public function check_penalty_anzeigen(): bool
    {
        if(!$this->check_penalty_ergebnisse()) return true;
        return !empty($this->gesamt_penaltys);
    }

    /**
     * Überprüft, ob Penalty-Ergebnisse bei den richtigen Teams eingetragen worden sind.
     *
     * @return bool
     */
    public function check_penalty_ergebnisse(): bool
    {
        $team_ids = [];
        foreach ($this->gesamt_penaltys as $penalty) {
            $team_ids += $penalty;
        }
        foreach ($this->spiele as $spiel) {
            if (is_null($spiel['penalty_a']) && is_null($spiel['penalty_b'])) continue;
            if (!array_key_exists($spiel['team_id_a'], $team_ids)
                or !array_key_exists($spiel['team_id_b'], $team_ids)) return false;
        }
        return true;
    }
    public function check_penalty_team(int $team_id): bool
    {
        $team_ids = [];
        foreach ($this->ausstehende_penaltys as $penalty) {
            $team_ids += $penalty;
        }
        return array_key_exists($team_id, $team_ids);
    }

    /**
     * Check, ob das Turnier beendet wurde
     *
     * @return bool true, wenn keine Spiele und Penalty-Begegnungen ausstehen.
     */
    function check_turnier_beendet(): bool
    {
        if (!empty($this->ausstehende_penaltys)) return false;
        return $this->anzahl_spiele == min(array_column($this->turnier_tabelle, 'spiele'));
    }

    /**
     * Check, ob jedes Team ein Team gespielt hat.
     * Wenn ja, wird die Platzierung und das Turnierergebnis im Template angezeigt
     *
     * @return bool true, wenn alle ein Spiel gespielt haben
     */
    function check_tabelle_einblenden(): bool
    {
        return 0 != min(array_column($this->turnier_tabelle, 'spiele'));
    }

    /**
     * Überträgt das Turnierergebnis der Platzierungstabelle in die Datenbank
     */
    public function set_ergebnis()
    {
        // Ergebnis eintragen
        $this->turnier->set_phase('ergebnis');
        $this->turnier->delete_ergebnis();
        foreach ($this->platzierungstabelle as $team_id => $ergebnis) {
            $this->turnier->set_ergebnis($team_id, $ergebnis['ligapunkte'], $ergebnis['platz']);
        }
        $this->turnier->log("Turnierergebnis wurde in die Datenbank eingetragen");
        Form::affirm("Das Turnierergebnis wurde dem Ligaausschuss übermittelt und wird jetzt in den Ligatabellen angezeigt.");
    }

}
