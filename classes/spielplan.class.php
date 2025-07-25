<?php /** @noinspection IssetArgumentExistenceInspection */

use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\DoctrineWrapper;
use App\Repository\SpielplanPaarungenRepository;
use App\Repository\Team\TeamRepository;
use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Entity\Spielplan\SpielplanDetails;

/**
 * Class Spielplan
 *
 * Verwaltet Spielpläne, berechnet Turniertabellen und schreibt Turnierergebnisse in die DB.
 */
class Spielplan
{
    public static function spielplan_erstellen(Turnier $turnier): bool
    {
        $anzahl_teams = count($turnier->getSetzliste());
        $error = false;

        if ($turnier->isLigaturnier() && $turnier->getPhase() != "setz") {
            Html::error("Das Turnier muss in der Setzphase sein.");
            $error = true;
        }
        if ($turnier->isLigaturnier() && ($anzahl_teams < 3 || $anzahl_teams > 8)) {
            Html::error("Falsche Anzahl an Teams. Nur 4er - 8er Jeder-gegen-Jeden Spielpläne können erstellt werden.");
            $error = true;
        }
        if (!empty($turnier->getSpielplanDatei())) {
            Html::error("Spielplan konnte nicht erstellt werden. Es existiert ein manuell hochgeladener Spielplan.");
            $error = true;
        }
        if (!$error) {
            return self::fill_vorlage($turnier);
        }
        return false;
    }

    /**
     * Allgemeine Daten
     */
    public int $turnier_id;
    public Turnier $turnier;
    public ArrayCollection $teamliste;
    public SpielplanDetails $details;
    protected array $spiele;
    protected array $pausen;
    public int $anzahl_spiele; // Anzahl der Spiele in einem Jeder-gegen-Jeden Turnier pro Team

    /**
     * Tabellen
     */
    public array $tore_tabelle;
    public array $turnier_tabelle;
    public array $platzierungstabelle = [];

    /**
     * Zweite Runde Penaltys im JgJ sind zB out of scope
     */
    private bool $out_of_scope = false;

    /**
     * Spielplan constructor.
     *
     * @param Turnier $turnier
     */
    public function __construct(Turnier $turnier)
    {
        $this->turnier = $turnier;
        $this->teamliste = $this->turnier->getSetzliste();
        $this->details = DoctrineWrapper::manager()->find(SpielplanDetails::class, self::get_vorlage($turnier, count($this->teamliste)));

        $this->anzahl_spiele = count($this->teamliste) - 1; // Anzahl der Spiele in einem Jeder-gegen-Jeden Turnier pro Team
    }

    /**
     * Liest in der Datenbank hinterlegte Pausen aus
     *
     * @return array
     */
    public function get_pausen(): array
    {
        if (empty($this->details->getPausen())) {
            return [];
        }

        foreach (explode('#', $this->details->getPausen()) as $pause) {
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
        if (self::check_exist($turnier->id())) {
            Html::error("Es existiert bereits ein Spielplan");
            return false;
        }

        $teamliste = $turnier->getSetzliste(); //TODO Array mit 1 beginnen lassen
        // Teamlisten-Array mit 1 Beginnen lassen zum Ausfüllen der Spielplan-Vorlage //TODO Array mit 1 beginnen lassen
        $teamliste = array_values($teamliste);
        array_unshift($teamliste, '');
        unset($teamliste[0]);

        $vorlage = self::get_vorlage($turnier);

        if ($vorlage === false) {
            Html::error("Es konnte keine Spielplanvorlage ermittelt werden.");
            return false;
        }

        // Gebraucht wird die Vorlage der Paarungen und nicht die Vorlage, die im Turnier hinterlegt ist.
        $paarungen = SpielplanPaarungenRepository::findByString($vorlage);

        // Wurde eine Paarung gefunden?
        if (empty($paarungen)) {
            Html::error("Es konnte keine Spielreihenfolge aus dem Spielplan ermittelt werden");
            return false;
        }

        // Spielplan erstellen
        foreach ($paarungen as $spiel) {
            $sql = "
                    INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b)
                    VALUES (?,?,?,?,?,?)
                    ";
            $params = [
                $turnier->id(),
                $spiel["spiel_id"],
                $teamliste[$spiel["team_a"]]->id,
                $teamliste[$spiel["team_b"]]->id,
                $teamliste[$spiel["schiri_a"]]->id,
                $teamliste[$spiel["schiri_b"]]->id
            ];
            db::$db->query($sql, $params)->log();
        }

        // Turnierlog
        $turnier->setPhase('spielplan');
        $turnier->setSpielplanVorlage($vorlage);

        return true;
    }

    /**
     * Welche Spielplanvorlage soll für das Turnier verwendet werden?
     *
     * @param Turnier $turnier
     * @param int|null $anzahl_teams
     * @return false|string
     */
    public static function get_vorlage(Turnier $turnier, ?int $anzahl_teams = NULL): string|false
    {
        if ($turnier->id() == 1191) {
            return "12er_jgj";
        }

        // Existiert ein manuell hochgeladener Spielplan?
        if (!empty($turnier->getSpielplanDatei())) {
            return false;
        }

        // Wurde schon ein Spielplan gesetzt?
        if (!empty($turnier->getSpielplanVorlage())) {
            return $turnier->getSpielplanVorlage()->getSpielplanPaarung();
        }

        // Wie viele Teams sind angemeldet?
        if (is_null($anzahl_teams)) {
            $anzahl_teams = count($turnier->getSetzliste());
        }

        // Nur JgJ-Spielpläne sind in der Datenbank hinterlegt.
        if ($turnier->details->getFormat() !== 'jgj' && $anzahl_teams == 8) {
            return false;
        }

        // Richtigen Spielplan ermitteln, wenn keiner vorhanden, dann false
        return match ($anzahl_teams) {
            4 => '4er_jgj_default',
            5 => '5er_jgj_default',
            6 => '6er_jgj_default',
            7 => '7er_jgj_default',
            8 => '8er_jgj_default',
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
        if (!empty($turnier->getSpielplanVorlage())) {
            $turnier->getSpielplanVorlage(null);
        }
        
        // Es existiert kein dynamischer Spielplan
        if (!self::check_exist($turnier->id())) {
            return;
        }

        // Spielplan löschen
        $sql = "
                DELETE FROM spiele 
                WHERE turnier_id = ?
                ";
        db::$db->query($sql, $turnier->id())->log();
        $turnier->setSpielplanVorlage(null);
        $turnier->setPhase('setz');
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
        return db::$db->query($sql, self::get_vorlage($this->turnier, $this->anzahl_teams))->esc()->fetch_row();
    }

    /**
     * Gibt ein Array der Spiele aus dem in der Datenbank hinterlegten Spielplan
     *
     * @return array
     */
    public function get_spiele(): array
    {
        
        if (isset($this->spiele)) {
            return $this->spiele;
        }
        
        $sql = "
                SELECT spiel_id, team_id_a, t1.teamname AS teamname_a, team_id_b, t2.teamname AS teamname_b,
                schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b
                FROM spiele AS sp
                LEFT JOIN teams_liga as t1 on t1.team_id = sp.team_id_a
                LEFT JOIN teams_liga as t2 on t2.team_id = sp.team_id_b
                WHERE turnier_id = ?
                ORDER BY spiel_id
                ";
        $spiele = db::$db->query($sql, $this->turnier->id())->esc()->fetch('spiel_id');

        // Uhrzeiten berechnen
        $spielzeit = (
                $this->details->getAnzahlHalbzeiten()
                * $this->details->getHalbzeitLaenge()
                + $this->details->getPuffer()
            ) * 60; // In Sekunden für Unixzeit

        $startzeit = $this->turnier->getDetails()->getStartzeit();

        foreach ($spiele as $spiel_id => $spiel) {
            $spiele[$spiel_id]["zeit"] = $startzeit->format("H:i");
            $spielzeitSekunden = $spielzeit + $this->get_pause($spiel_id) * 60;
            $interval = new DateInterval('PT' . $spielzeitSekunden . 'S');
            $startzeit->add($interval);
        }
        
        $this->spiele = $spiele;
        return $this->spiele;
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
                WHERE turnier_id = ? AND spiel_id = ?
                ";

        // Damit die nicht eingetragene Tore nicht als 0 : 0 gewertet werden, müssen '' --> NULL werden
        $params = [
            !is_numeric($tore_a) ? NULL : (int)$tore_a,
            !is_numeric($tore_b) ? NULL : (int)$tore_b,
            !is_numeric($penalty_a) ? NULL : (int)$penalty_a,
            !is_numeric($penalty_b) ? NULL : (int)$penalty_b,
            $this->turnier_id,
            $spiel_id
        ];
        db::$db->query($sql, $params)->log();
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
        return db::$db->query($sql, $turnier_id)->num_rows() > 0;
    }

    /**
     * Findet die beste Trikotfarbenkombination für ein Spiel
     *
     * @param array $spiel
     * @return array
     *
     */
    public function get_trikot_colors(array $spiel, $is_html = true): array
    {
        if ($this->turnier->isErgebnisPhase()) {
            return [];
        }

        $team_id_a = $spiel['team_id_a'];
        $team_id_b = $spiel['team_id_b'];

        $farben = [
            $team_id_a => [
                1 => $this->teamliste[$team_id_a]->details['trikot_farbe_1'] ?? null,
                2 => $this->teamliste[$team_id_a]->details['trikot_farbe_2'] ?? null
            ],
            $team_id_b => [
                1 => $this->teamliste[$team_id_b]->details['trikot_farbe_1'] ?? null,
                2 => $this->teamliste[$team_id_b]->details['trikot_farbe_2'] ?? null
            ]
        ];

        // Nicht hinterlegte Farben entfernen
        $farben[$team_id_a] = array_filter($farben[$team_id_a]);
        $farben[$team_id_b] = array_filter($farben[$team_id_b]);

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
                    $return[$team_id_a] = $is_html ? Html::trikot_punkt($farbe_a) : $farbe_a;
                    $return[$team_id_b] = $is_html ? Html::trikot_punkt($farbe_b) : $farbe_b;
                }
            }
        }
        if (!$farben[$team_id_a] && $farben[$team_id_b]) {
            $farbe = array_values($farben[$team_id_b])[0];
            $return[$team_id_b] = $is_html ? Html::trikot_punkt($farbe) : $farbe;
        }
        if ($farben[$team_id_a] && !$farben[$team_id_b]) {
            $farbe = array_values($farben[$team_id_a])[0];
            $return[$team_id_a] = $is_html ? Html::trikot_punkt($farbe) : $farbe;
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
    public function get_torematrix($penaltys = true): array
    {
        foreach ($this->get_spiele() as $spiel) {

            if (!$penaltys) {
                $spiel['penalty_a'] = $spiel['penalty_b'] = NULL;
            }

            // lege ein leeres array an, wenn das spiel zwischen den teams noch nicht existiert
            if (!isset($tore_tabelle[$spiel['team_id_a']][$spiel['team_id_b']])) {
                $tore_tabelle[$spiel['team_id_a']][$spiel['team_id_b']] = [];
            }
            
            // pushe das spiel in die toretabelle fuer dieses aufeinandertreffen
            $tore_tabelle[$spiel['team_id_a']][$spiel['team_id_b']][] = [
                'tore'              => $spiel['tore_a'],
                'gegentore'         => $spiel['tore_b'],
                'penalty_tore'      => $spiel['penalty_a'],
                'penalty_gegentore' => $spiel['penalty_b'],
            ];
            
            // pushe das spiel in die toretabelle fuer dieses aufeinandertreffen
            $tore_tabelle[$spiel['team_id_b']][$spiel['team_id_a']][] = [
                'tore'              => $spiel['tore_b'],
                'gegentore'         => $spiel['tore_a'],
                'penalty_tore'      => $spiel['penalty_b'],
                'penalty_gegentore' => $spiel['penalty_a'],
            ];
            
        }

        return $tore_tabelle ?? [];
    }

    /**
     * Berechnet fuer das Turnier die Punkte, Tore, Gegentore, ... fuer jedes Team.
     * tore_matrix[team_id][gegner_id] = [[spiel1], [spiel2], ...]
     *
     * @param array $tore_tabelle Matrix der Tore zwischen den Teams aus den einzelnen Spielen
     * @return array *unsortierte* Turniertabelle
     */
    protected static function get_turniertabelle(array $tore_matrix): array
    {
        
        foreach ($tore_matrix as $team_id => $gegner) {
            // in dieser schleife finden sich die informationen zu dem team im gesamten turnier
            $punkte = $tordifferenz = $gegentore = $tore = $penalty_diff = $penalty_tore = $penalty_gegentore = $penalty_punkte = NULL;
            $normale_spiele = $penalty_spiele = 0;
            
            foreach ($gegner as $gegner_id => $spiele) {
                // gehe durch das array der spiele zwischen team_id und gegner_id
                
                foreach ($spiele as $spiel) {
                    // schaue dir jedes einzelne spiel an
                    
                    // liegen keine tore vor, dann ueberspringe das spiel
                    if (is_null($spiel['tore']) || is_null($spiel['gegentore'])) {
                        continue;
                    }

                    $punkte += ($spiel['tore'] > $spiel['gegentore']) ? 3 : 0; // spiel gewonnen
                    $punkte += ($spiel['tore'] === $spiel['gegentore']) ? 1 : 0; // spiel unentschieden
                    $tordifferenz += $spiel['tore'] - $spiel['gegentore'];
                    $tore += $spiel['tore'];
                    $gegentore += $spiel['gegentore'];
                    $normale_spiele++;

                    // liegt kein penalty-ergebnis vor, dann ueberspringe
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
            }

            // turniertabelle mit den informationen fuer das team fuellen
            $turnier_tabelle[$team_id] = [
                'spiele' => $normale_spiele,
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
                'teamname' => $this->teamliste->get($team_id)->getTeam()->getName(),
                'ligapunkte' => 0,
                'statistik' => $this->turnier_tabelle[$team_id],
            ];
    }

    /**
     * Fügt die Teamwertigkeiten in die Platzierungstabelle ein.
     */
    public function set_wertigkeiten(): void
    {
        // Gibt die Wertung des schlechtplatziertesten Ligateams aus
        $reverse_tabelle = array_reverse($this->platzierungstabelle, true);
        $last_ligateam = function () use ($reverse_tabelle) {
            foreach ($reverse_tabelle as $team_id => $eintrag) {
                $wertigkeit = $this->teamliste->get($team_id)->getTeam()->getWertigkeit($team_id, $this->turnier->getSpieltag(), $this->turnier->getSaison());
                if ($wertigkeit !== NULL) {
                    return $wertigkeit;
                }
            }
            return NULL;
        };

        foreach ($reverse_tabelle as $team_id => $eintrag) {
            $wertigkeit = $this->teamliste->get($team_id)->getTeam()->getWertigkeit($team_id, $this->turnier->getSpieltag(), $this->turnier->getSaison());
            if (is_null($wertigkeit)) {
                // Es handelt sich um ein Nichtligateam // max($werte) + 1 wenn nicht Letzter.
                $wert = max($werte ?? [round($last_ligateam() / 2 - 1), 14]) + 1;
            } else {
                // Normales Ligateam
                $wert = $wertigkeit;
            }
            $this->platzierungstabelle[$team_id]['wertigkeit'] = $wert;
        }
    }

    /**
     * Berechnet und fügt die Ligapunkte in die Platzierungstabelle ein.
     */
    public function set_ligapunkte(): void
    {
        $plaetze = $this->details['plaetze'];
        
        // Invertiere die Tabelle um die Punkte addieren zu können
        $reverse_tabelle = array_reverse($this->platzierungstabelle, true);
        if ($plaetze > 3) {
            // Addiere die Wertigkeiten der Teams zu den Ligapunkten und verrechne den Faktor
            $ligapunkte = 0;
            foreach ($reverse_tabelle as $team_id => $eintrag) {
                $ligapunkte += $this->platzierungstabelle[$team_id]['wertigkeit'];
                $this->platzierungstabelle[$team_id]['ligapunkte'] = round($ligapunkte * $this->details['faktor']);
            }
        
        } else {
            // Erhalte die Wertigkeit des erstplatzierten Teams
            $wertung = 0;
            foreach ($reverse_tabelle as $team_id => $eintrag) {
                $wertung = $this->platzierungstabelle[$team_id]['wertigkeit'];
            }
            
            // Berechne die Punkte für jedes Team anhand der Wertigkeit des ersten Teams
            $counter = 3;
            $faktoren = [1.5, 0.75, 0.5];
            foreach ($reverse_tabelle as $team_id => $eintrag) {
                $this->platzierungstabelle[$team_id]['ligapunkte'] = round($wertung * $faktoren[$counter - 1]);
                $counter--;
            }
        }

    }

    /**
     * True, wenn das Turnierergebnis eingetragen werden darf. Also jedes vorherige Turnier in der Ergebnisphase ist.
     *
     * @return bool
     */
    public function is_ergebnis_eintragbar(): bool
    {
        if (!in_array($this->art, ['I', 'II', 'III', 'final'])) {
            Html::error("Für diesen Turniertyp können keine Ergebnisse eingetragen werden.");
            // TODO ist der Check hier an der besten Stelle?
            return false;
        }
        
        $sql = "
            SELECT * 
            FROM turniere_liga 
            WHERE spieltag < ? 
            AND spieltag != 0 
            AND (art='I' OR art = 'II' OR art='III' OR art='final') 
            AND saison = ?
            AND canceled = 0
            AND phase != 'ergebnis'
        ";
        
        return db::$db->query($sql, $this->spieltag, $this->saison)->num_rows() === 0;
    }

    public function is_out_of_scope(): bool
    {
        return $this->out_of_scope;
    }
}

