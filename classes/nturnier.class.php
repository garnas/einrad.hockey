<?php

/**
 * Class Turnier
 *
 * Alles für die Verwaltung und zum Anzeigen von Turnieren
 */
class nTurnier
{

    // turniere_liga
    public int $turnier_id;
    private ?string $tname;
    private ?int $ausrichter;
    private ?string $art;
    private ?string $tblock;
    private ?string $tblock_fixed;
    private ?string $datum;
    private ?string $datum_bis;
    private ?int $spieltag;
    private ?string $phase;
    private ?string $spielplan_vorlage;
    private ?string $spielplan_datei;
    private ?int $saison;

    // turniere_details
    private ?string $hallenname;
    private ?string $strasse;
    private ?string $plz;
    private ?string $ort;
    private ?string $haltestellen;
    private ?int $plaetze;

    private ?int $min_teams;
    private ?string $format;
    private ?string $startzeit;
    private ?string $besprechung;
    private ?string $hinweis;
    private ?string $organisator;
    private ?string $handy;
    private ?string $startgebuehr;

    // weitere
    private ?array $details;
    private string $log = '';
    private bool $error = false;

    private int $freie_plaetze;
    private string $freie_plaetze_status;

    private array $meldeliste;
    private int $anz_meldeliste;

    private array $warteliste;
    private int $anz_warteliste;

    private array $spielenliste;
    private int $anz_spielenliste;

    # Update für fetchObject der "alten" DB-Klasse, ansonsten werden diese dynamisch gesetzt mit Deprecated-Warning
    public ?string $erstellt_am;
    public ?string $canceled_grund;
    public ?string $sofort_oeffnen;
    public ?string $canceled;
    public ?string $block_erweitert_hoch;
    public ?string $block_erweitert_runter;
    public ?string $teamname;

    /**
     * Turnier constructor.
     */
    public function __construct(bool $esc = true, $skip_init = false)
    {
        if (!$skip_init) {
            if ($esc) {
                foreach (get_object_vars($this) as $name => $value) {
                    $this->$name = db::escape($value);
                }
            }
            $this->meldeliste = $this->set_meldeliste();
            $this->anz_meldeliste = $this->set_anz_meldeliste();

            $this->warteliste = $this->set_warteliste();
            $this->anz_warteliste = $this->set_anz_warteliste();

            $this->spielenliste = $this->set_spielenliste();
            $this->anz_spielenliste = $this->set_anz_spielenliste();

            $this->freie_plaetze = $this->set_freie_plaetze();
            $this->freie_plaetze_status = $this->set_freie_plaetze_status();
        } else {
            $this->turnier_id = 0;
            $this->spielplan_datei = "";
        }

    }

    /**
     * Turnier deconstructor.
     */
    public function __destruct()
    {
        if (!empty($this->log) && !$this->error) {
            $sql = "
                INSERT INTO turniere_log (turnier_id, log_text, autor) 
                VALUES ($this->turnier_id, ?, ?);
                ";
            $autor = Helper::get_akteur(true);

            $this->log = trim($this->log);
            db::$db->query($sql, $this->log, $autor)->log();

            Discord::send_with_turnier($this->log, $this);
        }
    }

    /**
     * @return int
     */
    public function get_turnier_id(): int
    {
        return $this->turnier_id;
    }

    /**
     * @return string
     */
    public function get_tname(): string
    {
        return $this->tname;
    }

    /**
     * @return int
     */
    public function get_ausrichter(): int
    {
        return $this->ausrichter;
    }

    /**
     * @return string
     */
    public function get_art(): string
    {
        return $this->art;
    }

    /**
     * @return string|null
     */
    public function get_tblock(): ?string
    {
        return $this->tblock;
    }

    /**
     * @return string
     */
    public function get_datum(): string
    {
        return $this->datum;
    }

    /**
     * @return int
     */
    public function get_spieltag(): int
    {
        return $this->spieltag;
    }

    /**
     * @return string
     */
    public function get_phase(): string
    {
        return $this->phase;
    }

    /**
     * @return ?|string
     */
    public function get_spielplan_vorlage(): null|string
    {
        return $this->spielplan_vorlage;
    }

    /** 
     * @return ?|string
     */
    public function get_spielplan_datei(): null|string
    {
        return $this->spielplan_datei;
    }

    /**
     * @return int
     */
    public function get_saison(): int
    {
        return $this->saison;
    }

    /**
     * @return string
     */
    public function get_ort(): string
    {
        return $this->ort;
    }

    /**
     * @return string
     */
    public function get_format(): string
    {
        return $this->format ?? "jgj";
    }

    /**
     * @return string
     */
    public function get_startzeit(): string
    {
        return $this->startzeit;
    }

    /**
     * @return string
     */
    public function get_besprechung(): string
    {
        return $this->besprechung;
    }

    /**
     * @return array
     */
    public function get_spielenliste(): array
    {
        return $this->spielenliste;
    }

    /**
     * @return int
     */
    public function get_anz_spielenliste(): int
    {
        return $this->anz_spielenliste;
    }

    /**
     * @return int
     */
    public function get_freie_plaetze(): int
    {
        return $this->freie_plaetze;
    }

    /**
     * @param int $id
     * @return nTurnier
     */
    public static function get(int $turnier_id): ?nTurnier
    {
        $sql = "
            SELECT turniere_liga.*, turniere_details.*
            FROM turniere_liga
            LEFT JOIN turniere_details ON turniere_liga.turnier_id = turniere_details.turnier_id
            WHERE turniere_liga.turnier_id = $turnier_id
        ";
        return db::$db->query($sql)->fetch_object(__CLASS__);
    }

    /**
     * Erhalte alle Turniere, die sich (nicht) in der angegebenen Phase befinden
     * Zudem der Teamname des Ausrichters
     *
     * @param bool $asc
     * @param int $saison
     * @return nTurnier[]
     */
    public static function get_turniere(bool $asc = true, int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                AND saison = ?
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");

        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte alle Turniere eines Spieltages, immer nach Datum sortiert.
     *
     * @param int $spieltag
     * @param int $saison
     * @return nTurnier[]
     */
    public static function get_turniere_spieltag(int $spieltag, int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                WHERE spieltag = ? AND saison = ? AND canceled = 0 AND (art = 'I' OR art = 'II')
                ORDER BY turniere_liga.datum;
        ";

        return db::$db->query($sql, $spieltag, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte Turniere, die in der spielplan-Phase sind
     * @param $saison
     * @return nTurnier[]
     */
    public static function get_turniere_spielplan(bool $asc = true, int $saison = CONFIG::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                WHERE phase = 'spielplan'
                AND saison = ?
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");
        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte kommende Turniere
     * @param $saison
     * @return nTurnier[]
     */

    public static function get_turniere_kommend(bool $asc = true, int $saison = CONFIG::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                WHERE phase != 'ergebnis'
                AND saison = ?
                AND canceled = 0
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc")
                ;
        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte Turniere, die in der ergebnis-Phase sind
     * @param $saison
     * @return nTurnier[]
     */

    public static function get_turniere_ergebnis(bool $asc = true, int $saison = CONFIG::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                WHERE phase = 'ergebnis'
                AND saison = ?
                AND (art = 'I' OR art = 'II' OR art = 'III' OR art = 'final')
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");
        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Anmeldungen der Warte-, Melde-, Spielen-Liste des aktuellen Turniers
     *
     * @return array
     */
    public function get_anmeldungen(): array
    {
        $sql = "
                SELECT turniere_liste.*, teams_liga.teamname, teams_liga.ligateam
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                WHERE turniere_liste.turnier_id = ?
                ORDER BY turniere_liste.position_warteliste
                ";
        $anmeldungen = db::$db->query($sql, $this->turnier_id)->esc()->fetch();

        // Erstellung des Arrays mit den Teamnamen, Teamblöcken und Teamwertigkeiten
        $liste['team_ids'] = $liste['teamnamen'] = $liste['spiele'] = $liste['melde'] = $liste['warte'] = [];
        foreach ($anmeldungen as $a) {
            $liste[$a['liste']][$a['team_id']] = $a;
            $liste[$a['liste']][$a['team_id']]['tblock'] = Tabelle::get_team_block($a['team_id']);
            $liste[$a['liste']][$a['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($a['team_id']);
        }
        return $liste;
    }

    /**
     * Anmeldungen der Warte-, Melde-, Spielen-Liste aller Turniere der Saison
     *
     * @param int $saison
     * @return array
     */
    public static function get_all_anmeldungen(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liste.*, teams_liga.teamname, teams_liga.ligateam
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                INNER JOIN turniere_liga
                ON turniere_liga.turnier_id = turniere_liste.turnier_id
                WHERE turniere_liga.saison = ?
                AND turniere_liga.phase != 'ergebnis'
                ORDER BY turniere_liste.position_warteliste
                ";
        $anmeldungen = db::$db->query($sql, $saison)->esc()->fetch();

        // Erhalten den aktuellen Spieltag für die Ermittung des Teamblocks und der -wertigkeit
        $spieltag = Tabelle::get_aktuellen_spieltag();

        // Erstellung des Arrays mit der TurnierID, der -liste, Teamnamen, -blöcken und -wertigkeiten
        foreach ($anmeldungen as $a) {
            $turnier_listen[$a['turnier_id']][$a['liste']][$a['team_id']] = $a;
            $turnier_listen[$a['turnier_id']][$a['liste']][$a['team_id']]['tblock'] = Tabelle::get_team_block($a['team_id'], $spieltag);
            $turnier_listen[$a['turnier_id']][$a['liste']][$a['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($a['team_id'], $spieltag);
        }
        return $turnier_listen ?? [];
    }

    /**
     * Get alle Teams auf der Spielenliste des Turniers nach Wertung sortiert.
     *
     * @return array
     */
    public function set_spielenliste(): array
    {
        $sql = "
                SELECT turniere_liste.team_id, teams_liga.teamname, teams_liga.ligateam,
                    teams_details.ligavertreter, teams_details.trikot_farbe_1, teams_details.trikot_farbe_2, turniere_liste.freilos_gesetzt
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_details
                ON turniere_liste.team_id = teams_details.team_id
                WHERE turniere_liste.turnier_id = ? 
                AND (turniere_liste.liste = 'setzliste' 
                OR turniere_liste.liste = 'spiele') 
                ";
        $liste = db::$db->query($sql, $this->turnier_id)->esc()->fetch('team_id');

        // Prüfen ob Spielen-Liste gegeben
        if (!empty($liste)) {

            // Blöcke und Wertungen hinzufügen
            $spielenliste = array();
            foreach ($liste as $team) {
                $team_id = $team['team_id'];

                $temp = new Team($team_id);
                $temp->set_wertigkeit($this->spieltag, $this->saison);
                $temp->set_tblock($this->spieltag);
                $temp->set_freilos_gesetzt($team['freilos_gesetzt']);
                $spielenliste[$team_id] = $temp;
            }

            // A-Finals werden nach Meisterschaftstabelle sortiert, rest nach Rangtabelle
            if ($this->get_art() === "final" && str_contains($this->get_tblock() ?? "", "A")) {
                $spieltag = Tabelle::get_aktuellen_spieltag();
                uasort($spielenliste, static function ($team_a, $team_b) use ($spieltag) {
                    return (
                        (int)Tabelle::get_team_meister_platz($team_a->id, $spieltag)
                        <=>
                        (int)Tabelle::get_team_meister_platz($team_b->id, $spieltag)
                    );
                });
            } else {
                uasort($spielenliste, static function ($team_a, $team_b) {
                    return ((int)$team_b->get_wertigkeit() <=> (int)$team_a->get_wertigkeit());
                });
            }
            if ($this->saison !== Config::SAISON) {
                foreach ($spielenliste as $team_id => $team) {
                    $spielenliste[$team_id]->teamname = Team::id_to_name($team_id, $this->saison);
                }
            }
        }

        return $spielenliste ?? [];
    }

    /**
     * Get alle Teams auf der Warteliste des Turniers nach Wertung sortiert.
     *
     * @return array
     */
    public function set_warteliste(): array
    {
        $sql = "
                SELECT turniere_liste.team_id, teams_liga.teamname, teams_liga.ligateam,
                    teams_details.ligavertreter, teams_details.trikot_farbe_1, teams_details.trikot_farbe_2, turniere_liste.position_warteliste
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_details
                ON turniere_liste.team_id = teams_details.team_id
                WHERE turniere_liste.turnier_id = ? 
                AND turniere_liste.liste = 'warte'
                ORDER BY turniere_liste.position_warteliste
                ";
        $liste = db::$db->query($sql, $this->turnier_id)->esc()->fetch('team_id');

        // Prüfen ob Warte-Liste gegeben
        if (!empty($liste)) {

            // Blöcke und Wertungen hinzufügen
            $warteliste = array();
            foreach ($liste as $team) {
                $team_id = $team['team_id'];

                $temp = new Team($team_id);
                $temp->set_wertigkeit($this->spieltag);
                $temp->set_tblock($this->spieltag);
                $temp->set_position_warteliste($team['position_warteliste']);
                $warteliste[$team_id] = $temp;
            }
        }

        return $warteliste ?? [];
    }

    /**
     * Get alle Teams auf der Warteliste des Turniers nach Wertung sortiert.
     *
     * @return array
     */
    public function set_meldeliste(): array
    {
        $sql = "
                SELECT turniere_liste.team_id, teams_liga.teamname, teams_liga.ligateam,
                    teams_details.ligavertreter, teams_details.trikot_farbe_1, teams_details.trikot_farbe_2
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_details
                ON turniere_liste.team_id = teams_details.team_id
                WHERE turniere_liste.turnier_id = ? 
                AND turniere_liste.liste = 'melde'
                ";
        $liste = db::$db->query($sql, $this->turnier_id)->esc()->fetch('team_id');

        // Prüfen ob Melde-Liste gegeben
        if (!empty($liste)) {

            // Blöcke und Wertungen hinzufügen
            $meldeliste = array();
            foreach ($liste as $team) {
                $team_id = $team['team_id'];

                $temp = new Team($team_id);
                $temp->set_wertigkeit($this->spieltag);
                $temp->set_tblock($this->spieltag);
                $meldeliste[$team_id] = $temp;
            }

            // Sortierung nach Wertigkeit
            uasort($meldeliste, static function ($team_a, $team_b) {
                return ((int)$team_b->get_wertigkeit() <=> (int)$team_a->get_wertigkeit());
            });
        }

        return $meldeliste ?? [];
    }

    /**
     * Kaderliste für die Kaderkontrolle des Turniers
     *
     * @return array
     */
    public function get_kader(): array
    {
        foreach ($this->spielenliste as $team) {
            $return[$team->id] = nSpieler::get_kader($team->id);
        }

        return $return ?? [];
    }

    /**
     * Get Turnierergebnis des Turnieres
     * TODO: Umstellung zu einem Array mit nTeam?
     * 
     * @return array
     */
    public function get_ergebnis(): array
    {
        $sql = "
                SELECT * 
                FROM turniere_ergebnisse 
                WHERE turnier_id = ?
                ORDER BY platz
                ";

        return db::$db->query($sql, $this->turnier_id)->esc()->fetch('platz');
    }

    /**
     * Gibt den Link zum Liga-Spielplan aus, je nach dem ob er manuell hochgeladen oder automatisch erstellt wurde
     *
     * @param string $scope default: allgemein, lc => ligacenter tc => teamcenter
     * @return false|string
     */
    public function get_spielplan_link(string $scope = ''): false|string
    {
        // Es existiert ein manuell hochgeladener Spielplan
        if (!empty($this->spielplan_datei)) {
            return $this->spielplan_datei;
        }

        // Es existiert ein automatisch erstellter Spielplan
        if (!empty($this->spielplan_vorlage)) {
            return match ($scope) {
                'lc' => Env::BASE_URL . '/ligacenter/lc_spielplan.php?turnier_id=' . $this->turnier_id,
                'tc' => Env::BASE_URL . '/teamcenter/tc_spielplan.php?turnier_id=' . $this->turnier_id,
                default => Env::BASE_URL . '/liga/spielplan.php?turnier_id=' . $this->turnier_id
            };
        }

        return false;
    }

    /**
     * Erhalte die Liste, auf der sich ein Team bei diesem Turnier befindet
     *
     * @param int $team_id
     * @return string
     */
    public function get_liste(int $team_id): string
    {
        $sql = "
                SELECT liste 
                FROM turniere_liste 
                WHERE team_id = ? 
                AND turnier_id = ?
                ";
        return db::$db->query($sql, $team_id, $this->turnier_id)->esc()->fetch_one() ?? '';
    }

    /**
     * Set Anzahl der freien Plätze auf der Spielen-Liste
     * 
     * @return int
     */
    public function set_freie_plaetze(): int
    {
        return $this->plaetze - count($this->spielenliste);
    }

    /**
     * Erhalte den Status der freien Plätze als String im entsprechenden Color-Code
     * 
     * @return string
     */
    public function set_freie_plaetze_status()
    {
        if ($this->phase == 'spielplan') {
            return '<span class="w3-text-gray">geschlossen</span>';
        } elseif ($this->freie_plaetze > 0) {
            return '<span class="w3-text-green">frei</span>';
        } elseif ($this->phase == 'offen' && count($this->spielenliste) + count($this->meldeliste) > $this->freie_plaetze) {
            return '<span class="w3-text-yellow">losen</span>';
        } elseif ($this->freie_plaetze - count($this->spielenliste) <= 0) {
            return '<span class="w3-text-red">voll</span>';
        }
    }

    /**
     * Schreibt in den Turnierlog.
     *
     * Turnierlogs werden bei Zerstörung des Objektes in die DB geschrieben.
     *
     * @param string $log_text
     */
    public function set_log(string $log_text): void
    {
        $this->log .= "\r\n" . $log_text;
    }
    public function reset_log(): void
    {
        $this->log = "";
    }

    public function auto_log(string $name, mixed $alt, mixed $neu): void
    {
        if ($alt !== $neu){
            $this->set_log($name . ": " . $alt . " -> " . $neu );
        }
    }

    /**
     * Setzt die Turnierart
     * 
     * @return nTurnier
     */
    public function set_art(string $art): nTurnier
    {
        $this->auto_log("Turnierart", $this->art ?? "", $art);
        $this->art = $art;
        return $this;
    }

    /**
     * Setzt die Phase
     * 
     * @return nTurnier
     */
    public function set_phase(string $phase): nTurnier
    {
        $this->auto_log("Phase", $this->phase ?? "" , $phase);
        $this->phase = $phase;
        return $this;
    }

    /**
     * Setzt die Spielplanvorlage
     */
    public function set_spielplan_vorlage(null|string $vorlage): void
    {
        $sql = "
            UPDATE turniere_liga
            SET spielplan_vorlage = ?
            WHERE turnier_id = ?
        ";
        db::$db->query($sql, $vorlage, $this->turnier_id)->log();
    }
    public function set_spielplan_vorlage_object(null|string $vorlage): void
    {
        $this->spielplan_vorlage = $vorlage;
    }

    /**
     * Setzt die Anzahl der Teams auf der Melden-Liste
     * 
     * @return int
     */
    public function set_anz_meldeliste(): int
    {
        return count($this->meldeliste);
    }

    /**
     * Setzt die Anzahl der Teams auf der Warte-Liste
     * 
     * @return int
     */
    public function set_anz_warteliste(): int
    {
        return count($this->warteliste);
    }

    /**
     * Setzt die Anzahl der Tems auf der Spielen-Liste
     * 
     * @return int
     */
    public function set_anz_spielenliste(): int
    {
        return count($this->spielenliste);
    }

    /**
     * Löscht Turnierergebnisse des Turnieres aus der DB
     */
    public function delete_ergebnis(): void
    {
        $sql = "
                DELETE FROM turniere_ergebnisse 
                WHERE turnier_id = ?
                ";
        db::$db->query($sql, $this->turnier_id)->log();
        if (db::$db->affected_rows() > 0) $this->set_log("Turnierergebnisse wurden gelöscht.");
    }

    /**
     * Schreibt ein Ergebnis eines Teams in die Datenbank
     *
     * @param int $team_id
     * @param int|null $ergebnis
     * @param int $platz
     */
    public function set_ergebnis(int $team_id, int|null $ergebnis, int $platz): void
    {
        if (!in_array($this->art, Config::TURNIER_ARTEN)) {
            $ergebnis = null;
        }
        $sql = "
                INSERT INTO turniere_ergebnisse (turnier_id, team_id, ergebnis, platz) 
                VALUES (?, ?, ?, ?);
                ";
        db::$db->query($sql, $this->turnier_id, $team_id, $ergebnis, $platz)->log();
    }

    /**
     * Überträgt das Turnierergebnis der Platzierungstabelle in die Datenbank
     *
     * @param array $platzierungstabelle
     */
    public function set_ergebnisse(array $platzierungstabelle): void
    {
        // Löscht Ergebnisse, die bereits eingetragen sind
        if (!empty($this->get_ergebnis())) {
            $this->delete_ergebnis();
        }

        foreach ($platzierungstabelle as $team_id => $ergebnis) {
            $this->set_ergebnis($team_id, $ergebnis['ligapunkte'], $ergebnis['platz']);
        }

        $this->update_phase('ergebnis');
        $this->set_log("Turnierergebnis wurde in die Datenbank eingetragen");
    }

    /**
     * Hinterlegt zu einem Turnier einen Link zu einem manuelle hochgeladenen Spielplan
     *
     * Pfad zum Spielplan
     * @param string $link
     * @param string $phase
     */
    public function upload_spielplan(string $link, string $phase): void
    {
        $sql = "
                UPDATE turniere_liga
                SET spielplan_datei = ?
                WHERE turnier_id = ?;
                ";
        db::$db->query($sql, $link, $this->turnier_id)->log();
        $this->spielplan_datei = $link;
        $this->set_phase($phase);
        $this->set_log("Manuelle Spielplan- oder Ergebnisdatei wurde hochgeladen.");
    }

    /**
     * Ändert die Phase des Turniers, ohne dass das gesamte Turnier durch ein Update muss
     * 
     * @param string $phase
     */
    public function update_phase(string $phase): void
    {
        $sql = "
            UPDATE turniere_liga
            SET phase = ?
            WHERE turnier_id = ?;
        ";
        db::$db->query($sql, $phase, $this->turnier_id)->log();
        $this->auto_log("Phase (direkt in die DB)", $this->phase, $phase);
    }

    /**
     * Ermittelt, ob es sich um ein Ligaturnier handelt
     * 
     * @return bool
     */
    public function is_ligaturnier(): bool
    {
        return $this->art != 'spass';
    }

    /**
     * Ermittelt, ob es sich um ein Finalturnier handelt
     *
     * @return bool
     */
    public function is_finalturnier(): bool
    {
        return $this->art == 'final';
    }

    /**
     * Ermittelt, ob das Team auf diesem Turnier spielen darf
     *
     * @param int $team_id
     * @return bool
     */
    public function is_spielberechtigt(int $team_id): bool
    {
        $team_block = Tabelle::get_team_block($team_id);
        $turnier_block = $this->tblock;
        $turnier_art = $this->art;

        // NL-Teams sind immer spielberechtigt
        if ($team_block === null) {
            return true;
        }

        // Check ob es sich um ein Block-Turnier handelt (nicht spass oder finale)
        if (in_array($turnier_art, Config::TURNIER_ARTEN)) {

            // Block-String in Array auflösen
            $turnier_buchstaben = str_split($turnier_block);
            $team_buchstaben = str_split($team_block);

            // Check ob ein Buchstabe des Team-Blocks im Turnier-Block vorkommt
            foreach ($team_buchstaben as $buchstabe) {
                if (in_array($buchstabe, $turnier_buchstaben)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Ermittelt, ob das Team an gleichen Kalendertag auf einem anderen Turnier angemeldet ist
     *
     * @param int $team_id
     * @return bool
     */
    public function is_doppelmeldung(int $team_id): bool
    {
        $sql = "
               SELECT liste_id
               FROM turniere_liste
               INNER JOIN turniere_liga
               ON turniere_liste.turnier_id = turniere_liga.turnier_id
               WHERE team_id = ? 
               AND datum = ? 
               AND liste = 'spiele'
               AND (turniere_liga.art = 'I' OR turniere_liga.art = 'II' OR turniere_liga.art = 'III')
               ";
        return db::$db->query($sql, $team_id, $this->datum)->num_rows() > 0;
    }

    /**
     * Ermittelt, ob das Team bereits bei diesem Turnier angemeldet ist
     *
     * @param int $team_id
     * @return bool
     */
    public function is_angemeldet(int $team_id): bool
    {
        $sql = "
                SELECT liste 
                FROM turniere_liste 
                WHERE team_id = ? AND turnier_id = ?
                ";
        return (db::$db->query($sql, $team_id, $this->turnier_id)->num_rows() > 0);
    }

    /**
     * Ermittelt, ob das angegebene Team der Ausrichter ist
     * 
     * @return bool
     */
    public function is_ausrichter(int $team_id): bool
    {
        return $team_id == $this->ausrichter;
    }
}
