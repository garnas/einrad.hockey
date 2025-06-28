<?php

final class Spielplan_JgJ extends Spielplan {

    /**
     * Tabellen
     */
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
     * DirekterVergleich constructor.
     * @param nTurnier $turnier
     * @param bool $penaltys Penaltys werden ignoriert. Dies ist für eine zweite Instanz der Klasse, aus welcher die
     * gesamt zu spielenden Penaltys in Erfahrung gebracht werden.
     */
    public function __construct(nTurnier $turnier, bool $penaltys = true, $skip_init = false)
    {
        parent:: __construct($turnier);

        $this->tore_tabelle = $this->get_toretabelle($penaltys);
        $this->turnier_tabelle = self::get_sorted_turniertabelle($this->tore_tabelle);
        $this->set_platzierungen($this->tore_tabelle);

        if (!$skip_init) {
            $this->set_wertigkeiten();
            $this->set_ligapunkte();
        }

        if (!empty($this->penaltys['kontrolle']) && $this->check_turnier_beendet()) {
            $this->out_of_scope = true;
        }
    }

    /**
     * Sortiert die Turniertabelle
     *
     * @param array $tore_tabelle Toretabelle aus get_toretabelle
     * @return array Sortierte Turniertabelle
     */
    public static function get_sorted_turniertabelle(array $tore_tabelle): array
    {
        $sort_function = static function ($ergebnis_a, $ergebnis_b) {
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
     * Entfernt Teams aus der Toretabelle, nicht jedoch aus Unter-Torebegegnungs-Tabelle
     *
     * @param array $tore_tabelle Toretabelle als Grundlage
     * @param array $team_ids Teams die Entfernt werden
     */
    private static function remove_team_ids(array &$tore_tabelle, array $team_ids): void
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
        $filter_function = static function ($team_id) use ($team_ids) {
            return in_array($team_id, $team_ids); // Alle Team-IDs, bis auf die Übergebenen, werden entfernt
        };
        foreach ($tore_tabelle as &$ergebnis) {
            $ergebnis = array_filter($ergebnis, $filter_function, ARRAY_FILTER_USE_KEY);
        }
        return array_filter($tore_tabelle, $filter_function, ARRAY_FILTER_USE_KEY);
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
        if ($art === 'erster_vergleich') {
            $callback = static function ($value) use ($match) {
                return $value['punkte'] === $match['punkte'];
            };
        } elseif ($art === 'direkter_vergleich') {
            $callback = static function ($value) use ($match) {
                return (
                    $value['punkte'] === $match['punkte']
                    && $value['tordifferenz'] === $match['tordifferenz']
                    && $value['tore'] === $match['tore']
                );
            };
        } else {
            $callback = static function ($value) use ($match) {
                return (
                    $value['penalty_punkte'] === $match['penalty_punkte']
                    && $value['penalty_diff'] === $match['penalty_diff']
                    && $value['penalty_tore'] === $match['penalty_tore']
                );
            };
        }
        return array_keys(array_filter($turnier_tabelle, $callback)); // Wenn es mehrere gleiche Teams gibt: false
    }

    /**
     * Sortiert die Turniertabelle und wendet ggf. den direkten Vergleich an.
     *
     * @param array $tore_tabelle
     */
    public function set_platzierungen(array $tore_tabelle): void
    {
        $turnier_tabelle = self::get_sorted_turniertabelle($tore_tabelle); // neue Turniertabelle erstellen
        // Mit dem ersten Team gleichplatzierte Teams suchen
        $first_team_id = array_key_first($turnier_tabelle);
        $gleichplatzierte_teams = $this->get_gleichplatzierte_teams($turnier_tabelle, $first_team_id);

        // Fall 1: Team ist eindeutig platzierbar, da das erste Team in der sortierten Turniertabelle
        // nur mit sich selbst gleichplatziert ist.
        if (count($gleichplatzierte_teams) === 1) {
            $this->set_platzierung($first_team_id);
            self::remove_team_ids($tore_tabelle, [$first_team_id]); // Werden aus der Toretabelle entfernt
            if (count($tore_tabelle) !== 0) {
                $this->set_platzierungen($tore_tabelle);
            }
        } else {
            // Direkter Vergleich mit nur den gleichplatzierten Teams in den nicht-ersten Vergleich
            $tore_tabelle_gleiche_teams = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
            $this->direkter_vergleich($tore_tabelle_gleiche_teams, true);

            // Forführung des ersten Vergleichs ohne die gleichplatzierten Teams
            self::remove_team_ids($tore_tabelle, $gleichplatzierte_teams);
            if (count($tore_tabelle) !== 0) {
                $this->set_platzierungen($tore_tabelle);
            }
        }
        if (count($tore_tabelle) === 0) { // Zuletzt werden die noch zu spielenden Penaltys ermittelt
            foreach ($this->penaltys['gesamt'] as $spiel_id) {
                if (
                    is_null($this->spiele[$spiel_id]['penalty_a'])
                    || is_null($this->spiele[$spiel_id]['penalty_b'])
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
    public function direkter_vergleich(array $tore_tabelle, bool $print = false): void
    {
        // Fall 0: Nur ein Team verblieben
        if (count($tore_tabelle) === 1) {
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
            if (count($tore_tabelle) !== 0) {
                $this->direkter_vergleich($tore_tabelle);
            }
            return;
        }

        // Fall 2: Team ist nicht eindeutig platzierbar, es muss ein neuer direkter Vergleich mit Untertabelle erstellt werden
        if (count($gleichplatzierte_teams) < count($turnier_tabelle)) {
            // Toretabelle mit nur den gleichplatzierten Teams in den nicht-ersten Vergleich
            $tore_tabelle_gleiche_teams = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
            $this->direkter_vergleich($tore_tabelle_gleiche_teams, true);
            // Toretabelle ohne die gleichplatzierten Teams
            self::remove_team_ids($tore_tabelle, $gleichplatzierte_teams);
            if (count($tore_tabelle) !== 0) {
                $this->direkter_vergleich($tore_tabelle);
            }
            return;
        }

        // Fall 3:
        // Tabelle besteht nur aus gleichplatzierten Teams also ab in den Penalty-Vergleich
        // Mit einer Tortabelle, in welcher nur die Spiele der gleichplatzierten Teams gezählt werden
        $tore_tabelle_gefiltert = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
        if ($tore_tabelle != $tore_tabelle_gefiltert) {
            $this->direkter_vergleich($tore_tabelle_gefiltert, true);
        } else {
            if ($this->check_ergebnis_fix($gleichplatzierte_teams)) {
                $this->penaltys['gesamt'] =
                    array_merge($this->penaltys['gesamt'], $this->get_spiel_ids($gleichplatzierte_teams));
            }
            $this->penalty_vergleich($tore_tabelle, true);
        }
    }

    /**
     * Direkter Vergleich der Penalty-Begegenungen
     *
     * @param array $tore_tabelle
     * @param bool $print
     */
    public function penalty_vergleich(array $tore_tabelle, bool $print = false): void
    {
        // Fall 0: Nur ein Team verblieben
        if (count($tore_tabelle) === 1) {
            $this->set_platzierung(array_key_first($tore_tabelle));
            return;
        }
        // neue Turniertabelle erstellen und ggf ausgeben
        $turnier_tabelle = self::get_sorted_turniertabelle($tore_tabelle);
        if ($print && $this->check_ergebnis_fix(array_keys($turnier_tabelle))) {
            $this->penalty_tabellen[] = $turnier_tabelle;
        }
        // Mit dem ersten Team gleichplatzierte Teams suchen
        $first_team_id = array_key_first($turnier_tabelle);
        $gleichplatzierte_teams = $this->get_gleichplatzierte_teams($turnier_tabelle, $first_team_id, "penalty_vergleich");
        if (count($gleichplatzierte_teams) === 1) {
            $this->set_platzierung($first_team_id);
            self::remove_team_ids($tore_tabelle, [$first_team_id]); // Werden aus der Toretabelle entfernt
            if (count($tore_tabelle) !== 0) {
                $this->penalty_vergleich($tore_tabelle);
            }
            return;
        }

        // Fall 2: Team ist nicht eindeutig platzierbar, es kann ein neuer Vergleich mit Untertabelle erstellt werden
        if (count($gleichplatzierte_teams) < count($turnier_tabelle)) {
            // Tabelle mit nur den gleichplatzierten Teams und deren Spiele
            $tore_tabelle_gleiche_teams = self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams);
            $this->penalty_vergleich($tore_tabelle_gleiche_teams, true);
            // Tabelle ohne die gleichplatzierten Teams
            self::remove_team_ids($tore_tabelle, $gleichplatzierte_teams);
            if (count($tore_tabelle) !== 0) {
                $this->penalty_vergleich($tore_tabelle);
            }
            return;
        }

        // Fall 3: Team ist nicht eindeutig platzierbar, ein neuer Vergleich ändert nichts
        if (self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams) != $tore_tabelle) {
            $this->penalty_vergleich(self::filter_team_ids($tore_tabelle, $gleichplatzierte_teams), true);
        } else {
            $this->penaltys['kontrolle'] = $this->get_spiel_ids($gleichplatzierte_teams);
            // Eine weitere Sortierung ist nicht mehr möglich, Penaltys müssen gespielt werden
            foreach ($gleichplatzierte_teams as $team_id) {
                $this->set_platzierung($team_id);
            }
        }
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
                if (in_array($vgl_team_id, $team_ids, true)) {
                    continue;
                } // Nicht mit sich selbst vergleichen
                // Penaltybegnung vermeidbar, da ein Team die Punktzahl des Penalty-Teams noch erreichen könnte?
                if (
                    (
                        $vergleich($vgl_team_id)['punkte_max'] !== $vergleich($vgl_team_id)['punkte_min']
                        && $vergleich($vgl_team_id)['punkte_max'] === $punkte_pen_team
                    )
                    && $punkte_pen_team <= $vergleich($vgl_team_id)['punkte_max']
                    && $punkte_pen_team >= $vergleich($vgl_team_id)['punkte_min']
                    && $punkte_pen_team != $vergleich($vgl_team_id)['nicht_erreichbar']
                ) {
                    return false;
                }
            } // foreach Teams auf dem Turnier
        } // foreach Penalty-Teams
        return true;
    }
    /**
     * Check, ob das Turnier beendet wurde
     *
     * @return bool true, wenn keine Spiele und Penalty-Begegnungen ausstehen.
     */
    public function check_turnier_beendet(): bool
    {
        if (!empty($this->penaltys['ausstehend'])) {
            return false;
        }
        $min_spiele = min(array_column($this->turnier_tabelle, 'spiele'));
        // kleinste Anzahl an beendeten Spielen eines Teams
        return $this->anzahl_spiele == $min_spiele;
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
                || $this->spiele[$spiel_id]['team_id_b'] == $team_id
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check, ob jedes Team ein Team gespielt hat.
     * Wenn ja, wird die Platzierung und das Turnierergebnis im Template angezeigt
     *
     * @return bool true, wenn alle mind. ein Spiel gespielt haben
     */
    public function check_tabelle_einblenden(): bool
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
        if (!$this->validate_penalty_ergebnisse()) {
            return true;
        }
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
            if ((!is_null($spiel['penalty_a']) || !is_null($spiel['penalty_b']))
                && !in_array($spiel_id, $this->penaltys['gesamt'])
            ) {
                return false;
            }
            // Es wurde also ein Penalty bei einem Spiel eingetragen, bei welchem kein Penalty vorgesehen ist.
        }
        return true;
    }
}