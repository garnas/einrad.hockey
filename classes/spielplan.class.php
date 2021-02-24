<?php /** @noinspection IssetArgumentExistenceInspection */

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
    public int $anzahl_spiele; // Anzahl der Spiele die ein Team spielen muss
    public array $spiele;
    protected array $pausen;

    /**
     * Tabellen
     */
    public array $tore_tabelle;
    public array $turnier_tabelle;
    public array $platzierungstabelle = [];

    /**
     * Zweite Runde Penaltys im JgJ sind zB out of scope
     */
    public bool $out_of_scope = false;

    /**
     * Spielplan constructor.
     *
     * @param Turnier $turnier
     */
    public function __construct(Turnier $turnier)
    {
        // Turnier
        $this->turnier_id = $turnier->id;
        $this->turnier = $turnier;

        // Spielplan
        $this->teamliste = $this->turnier->get_liste_spielplan();
        $this->anzahl_teams = count($this->teamliste);

        $this->details = $this->get_details();
        if (empty($this->details)) {
            trigger_error("Spielplan konnte nicht ermittelt werden.", E_USER_ERROR);
        }

        $this->pausen = $this->get_pausen();
        $this->spiele = $this->get_spiele();
        $this->anzahl_spiele = $this->anzahl_teams - 1;

        // Sollte bei JgJ-Spielplänen der Fall sein
        if ($this->anzahl_spiele * $this->anzahl_teams/2 !== count($this->spiele)) {
            trigger_error("Teams und Spielplan passen nicht zusammen.", E_USER_ERROR);
        }

        // Passen die angemeldeten Teams zu den im Spielplan hinterlegten Teams?
        foreach ($this->spiele as $spiel) {
            if (
                !array_key_exists($spiel['team_id_a'], $this->teamliste)
                || !array_key_exists($spiel['team_id_b'], $this->teamliste)
            ) {
                trigger_error("Teams und Spielplan passen nicht zusammen.", E_USER_ERROR);
            }
        }
    }

    /**
     * Liest in der Datenbank hinterlegte Pausen aus
     *
     * @return array
     */
    public function get_pausen(): array
    {
        if (empty($this->details['pausen'])) {
            return [];
        }

        foreach (explode('#', $this->details['pausen']) as $pause) {
            $pause = explode(',', $pause);
            $pausen[$pause[0]] = $pause[1]; // Spiel-ID => Minuten an Pause nach dieser Spiel-ID
        }
        return $pausen ?? [];
    }

    /**
     * Gibt die Länge der Pause nach dem Spiel der Spiel-ID aus
     *
     * @param int $spiel_id
     * @return int
     */
    public function get_pause(int $spiel_id): int
    {
        return $this->pausen[$spiel_id] ?? 0;
    }

    /**
     * Erstellt einen Spielplan in der Datenbank
     *
     * @param Turnier $turnier
     * @return bool Erfolgreich / Nicht erfolgreich estellt
     */
    public static function fill_vorlage(Turnier $turnier): bool
    {
        if (self::check_exist($turnier->id)) {
            Form::error("Es existiert bereits ein Spielplan");
            return false;
        }

        $teamliste = $turnier->get_liste_spielplan(); //TODO Array mit 1 beginnen lassen
        // Teamlisten-Array mit 1 Beginnen lassen zum Ausfüllen der Spielplan-Vorlage //TODO Array mit 1 beginnen lassen
        $teamliste = array_values($teamliste);
        array_unshift($teamliste, '');
        unset($teamliste[0]);

        $vorlage = self::get_vorlage($turnier);

        if ($vorlage === false) {
            Form::error("Es konnte keine Spielplanvorlage ermittelt werden.");
            return false;
        }

        // Spielplanvorlage aus der Datenbank
        $sql = "
                SELECT * 
                FROM spielplan_paarungen 
                WHERE spielplan_paarung = ?
                ";
        $paarungen = dbi::$db->query($sql, $vorlage)->fetch();

        // Wurde eine Paarung gefunden?
        if (empty($paarungen)) {
            Form::error("Es konnte keine Spielreihenfolge aus dem Spielplan ermittelt werden");
            return false;
        }

        // Spielplan erstellen
        foreach ($paarungen as $spiel) {
            $sql = "
                    INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b)
                    VALUES (?,?,?,?,?,?)
                    ";
            $params = [
                $turnier->id,
                $spiel["spiel_id"],
                $teamliste[$spiel["team_a"]]["team_id"],
                $teamliste[$spiel["team_b"]]["team_id"],
                $teamliste[$spiel["schiri_a"]]["team_id"],
                $teamliste[$spiel["schiri_b"]]["team_id"]
            ];
            dbi::$db->query($sql, $params)->log();
        }

        // Turnierlog
        $turnier->log("Automatischer Jgj-Spielplan erstellt.");
        $turnier->set_phase('spielplan');
        $turnier->set('spielplan_vorlage', $vorlage);

        return true;
    }

    /**
     * Welche Spielplanvorlage soll für das Turnier verwendet werden?
     *
     * @param Turnier $turnier
     * @param int|null $anzahl_teams
     * @return false|string
     */
    public static function get_vorlage(Turnier $turnier, ?int $anzahl_teams = NULL): false|string
    {
        // Existiert ein manuell hochgeladener Spielplan?
        if (!empty($turnier->details['spielplan_datei'])) {
            return false;
        }

        // Wurde schon ein Spielplan gesetzt?
        if (!empty($turnier->details['spielplan_vorlage'])) {
            return $turnier->details['spielplan_vorlage'];
        }

        // Wie viele Teams sind angemeldet?
        if (is_null($anzahl_teams)) {
            $anzahl_teams = count($turnier->get_liste_spielplan());
        }

        // Nur JgJ-Spielpläne sind in der Datenbank hinterlegt.
        if ($turnier->details['format'] !== 'jgj') {
            return false;
        }

        // Richtigen Spielplan ermitteln, wenn keiner vorhanden, dann false
        return match ($anzahl_teams) {
            4 => '4er_jgj_default',
            5 => '5er_jgj_default',
            6 => '6er_jgj_default',
            7 => '7er_jgj_default',
            8 => '8er_jgj_versetzt',
            default => false,
        };
    }

    /**
     * Löscht einen bisher erstellten Spielplan
     *
     * @param Turnier $turnier
     */
    public static function delete(Turnier $turnier): void
    {
        if (!empty($turnier->details['spielplan_vorlage'])) {
            $turnier->set('spielplan_vorlage', null);
        }
        // Es existiert kein dynamischer Spielplan
        if (!self::check_exist($turnier->id)) {
            return;
        }

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
                WHERE spielplan = ?
                ";
        return dbi::$db->query($sql, self::get_vorlage($this->turnier, $this->anzahl_teams))->esc()->fetch_row();
    }

    /**
     * Gibt ein Array der Spiele aus dem in der Datenbank hinterlegten Spielplan
     *
     * @return array
     */
    public function get_spiele(): array
    {
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

        // Uhrzeiten berechnen
        $spielzeit = (
                $this->details["anzahl_halbzeiten"]
                * $this->details["halbzeit_laenge"]
                + $this->details["puffer"]
            ) * 60; // In Sekunden für Unixzeit

        $startzeit = strtotime($this->turnier->details["startzeit"]);

        foreach ($spiele as $spiel_id => $spiel) {
            $spiele[$spiel_id]["zeit"] = date("H:i", $startzeit);
            $startzeit += $spielzeit + $this->get_pause($spiel_id) * 60;
        }

        return $spiele;
    }

    /**
     * Schreibt ein Spielergebnis in die Datenbank
     * String, da $_POST immer Strings überträgt
     * @param int $spiel_id
     * @param string $tore_a
     * @param string $tore_b
     * @param string $penalty_a
     * @param string $penalty_b
     */
    public function set_tore(int $spiel_id, string $tore_a, string $tore_b, string $penalty_a, string $penalty_b): void
    {
        $sql = "
                UPDATE spiele 
                SET tore_a = ?, tore_b = ?, penalty_a = ?, penalty_b = ?
                WHERE turnier_id = $this->turnier_id AND spiel_id = ?
                ";

        // Damit die nicht eingetragene Tore nicht als 0 : 0 gewertet werden, müssen '' --> NULL werden
        $params = [
            !is_numeric($tore_a) ? NULL : (int)$tore_a,
            !is_numeric($tore_b) ? NULL : (int)$tore_b,
            !is_numeric($penalty_a) ? NULL : (int)$penalty_a,
            !is_numeric($penalty_b) ? NULL : (int)$penalty_b,
            $spiel_id
        ];
        dbi::$db->query($sql, $params)->log();
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
            ) {
                $return[] = $spiel_id;
            }
        }
        return $return ?? [];
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
     * Findet die beste Trikotfarbenkombination für ein Spiel
     *
     * @param array $spiel
     * @return array
     *
     */
    public function get_trikot_colors(array $spiel): array
    {
        if ($this->turnier->details['phase'] === 'ergebnis') {
            return [];
        }
        $team_id_a = $spiel['team_id_a'];
        $team_id_b = $spiel['team_id_b'];
        $farben = [
            $team_id_a => [
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
            || empty($farben[$team_id_b])
        ) {
            return [];
        }

        // Hexfarbe in RGB umwandeln und Farbunterschied berechnen und ausgeben
        $get_delta_e = static function ($hex_color_1, $hex_color_2) {
            [$r_1, $g_1, $b_1] = sscanf($hex_color_1, "#%02x%02x%02x");
            [$r_2, $g_2, $b_2] = sscanf($hex_color_2, "#%02x%02x%02x");
            $r_m = ($r_1 + $r_2) / 2;
            $r_d = $r_1 - $r_2;
            $g_d = $g_1 - $g_2;
            $b_d = $b_1 - $b_2;
            return ((2 + $r_m / 256) * $r_d ** 2 + 4 * $g_d ** 2 + (2 + (255 - $r_m) / 256) * $b_d ** 2) ** 0.5;
        };

        $max_delta_e = 0;
        foreach ($farben[$team_id_a] as $farbe_a) {
            foreach ($farben[$team_id_b] as $farbe_b) {
                $delta_e = $get_delta_e($farbe_a, $farbe_b);
                if ($delta_e > $max_delta_e) {
                    if ($max_delta_e > 400) { // 400 Threshold inwiefern Trikotfarbe 1 ausreichend ist
                        continue;
                    }
                    $max_delta_e = $delta_e;
                    $return[$team_id_a] = Form::trikot_punkt($farbe_a);
                    $return[$team_id_b] = Form::trikot_punkt($farbe_b);
                }
            }
        }
        return $return ?? [];
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

            if (!$penaltys) {
                $spiel['penalty_a'] = $spiel['penalty_b'] = NULL;
            }

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
     * Erstellt eine Turniertabelle mit Punkten, Tordifferenz, etc.
     *
     * @param array $tore_tabelle Toretabelle aus get_toretabelle()
     * @return array unsortierte Turniertabelle
     */
    protected static function get_turniertabelle(array $tore_tabelle): array
    {
        // Punkte zählen
        foreach ($tore_tabelle as $team_id => $team_spiele) {
            $punkte = $tordifferenz = $gegentore = $tore = $penalty_diff = $penalty_tore = $penalty_gegentore = $penalty_punkte = NULL;
            $spiele = $penalty_spiele = 0;
            foreach ($team_spiele as $spiel) {
                // Spielbegegnungen
                if (is_null($spiel['tore']) || is_null($spiel['gegentore'])) {
                    continue;
                }
                $punkte += ($spiel['tore'] > $spiel['gegentore']) ? 3 : 0;
                $punkte += ($spiel['tore'] === $spiel['gegentore']) ? 1 : 0;
                $tordifferenz += $spiel['tore'] - $spiel['gegentore'];
                $tore += $spiel['tore'];
                $gegentore += $spiel['gegentore'];
                $spiele++;

                // Penaltybegegnungen
                if (is_null($spiel['penalty_tore']) || is_null($spiel['penalty_gegentore'])) {
                    continue;
                }
                $penalty_punkte += ($spiel['penalty_tore'] > $spiel['penalty_gegentore']) ? 3 : 0;
                $penalty_punkte += ($spiel['penalty_tore'] === $spiel['penalty_gegentore']) ? 1 : 0;
                $penalty_diff += $spiel['penalty_tore'] - $spiel['penalty_gegentore'];
                $penalty_tore += $spiel['penalty_tore'];
                $penalty_gegentore += $spiel['penalty_gegentore'];
                $penalty_spiele++;
            }

            // Turniertabelle erstellen
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
     * Platziert ein Team in $this->platzierungstabelle
     *
     * @param int $team_id Team-ID des Teams, welches platziert werden soll
     */
    protected function set_platzierung(int $team_id): void
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
     * Fügt die Teamwertigkeiten in die Platzierungstabelle ein.
     */
    public function set_wertigkeiten(): void
    {
        // Nur Turniere der I,II,II art vergeben Ligapunkte
//        if (!in_array($this->turnier->details['art'], ['I', 'II', 'III'], true)){
//            foreach ($this->platzierungstabelle as $team_id => $eintrag){
//                $this->platzierungstabelle[$team_id]['ligapunkte'] = '--';
//            }
//        }

        $reverse_tabelle = array_reverse($this->platzierungstabelle, true);

        $highest_ligateam = function () use ($reverse_tabelle) {
            foreach ($reverse_tabelle as $team_id => $eintrag) {
                if ($this->teamliste[$team_id]['wertigkeit'] !== 'NL') {
                    return $this->teamliste[$team_id]['wertigkeit'];
                }
            }
            return 0;
        };

        $ligapunkte = 0;
        foreach ($reverse_tabelle as $team_id => $eintrag) {
            $wert = $this->teamliste[$team_id]['wertigkeit'];
            $wert = ($wert === 'NL')
                ? max($werte ?? [max(round($highest_ligateam() / 2) - 1, 14)]) + 1
                : $wert; //TODO NL BLOCK UND WERT AUF NULL
            $werte[] = $wert;
            $ligapunkte += $wert;
            $this->platzierungstabelle[$team_id]['ligapunkte'] = round($ligapunkte * 6 / $this->details['faktor']);
        }
    }

}
