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
    public function get_tblock_fixed(): string
    {
        return $this->tblock_fixed;
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
    public function get_hallenname(): string
    {
        return $this->hallenname;
    }

    /**
     * @return string
     */
    public function get_strasse(): string
    {
        return $this->strasse;
    }

    /**
     * @return string
     */
    public function get_plz(): string
    {
        return $this->plz;
    }

    /**
     * @return string
     */
    public function get_ort(): string
    {
        return $this->ort;
    }

    /**
     * @return null|string
     */
    public function get_haltestellen(): null|string
    {
        return $this->haltestellen;
    }

    /**
     * @return int
     */
    public function get_plaetze(): int
    {
        return $this->plaetze;
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
     * @return string
     */
    public function get_hinweis(): string
    {
        return $this->hinweis;
    }

    /**
     * @return string
     */
    public function get_organisator(): string
    {
        return $this->organisator;
    }

    /**
     * @return string
     */
    public function get_handy(): string
    {
        return $this->handy;
    }

    /**
     * @return string
     */
    public function get_startgebuehr(): string
    {
        return $this->startgebuehr;
    }

    /**
     * @return array
     */
    public function get_warteliste(): array
    {
        return $this->warteliste;
    }

    /**
     * @return int
     */
    public function get_anz_warteliste(): int
    {
        return $this->anz_warteliste;
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
     * @return array
     */
    public function get_meldeliste(): array
    {
        return $this->meldeliste;
    }

    /**
     * @return int
     */
    public function get_anz_meldeliste(): int
    {
        return $this->anz_meldeliste;
    }

    /**
     * @return int
     */
    public function get_freie_plaetze(): int
    {
        return $this->freie_plaetze;
    }

    /**
     * @return string
     */
    public function get_freie_plaetze_status(): string
    {
        return $this->freie_plaetze_status;
    }

    /**
     * @return int
     */
    public function get_letzte_abmeldezeitpunkt(): int
    {
        return LigaBot::time_offen_melde($this->get_datum()) + 2 * 7 * 24 * 60 * 60;
    }

    /**
     * @param int $turnier_id
     * @return array
     */
    public static function get_log(int $turnier_id): array
    {
        $sql = "
                SELECT * 
                FROM turniere_log 
                WHERE turnier_id = ?
                ";
        return db::$db->query($sql, $turnier_id)->esc()->fetch();
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
     * @param string $phase
     * @param bool $equal
     * @param bool $asc
     * @param int $saison
     * @return nTurnier[]
     */
    public static function get_turniere(bool $asc = true, int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_name.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                INNER JOIN teams_name
                ON teams_name.team_id = teams_liga.team_id AND teams_name.saison = ?
                AND saison = ?
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");

        return db::$db->query($sql, $saison, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte Turniere, die in der offen-Phase sind
     * @param bool $asc
     * @param int $saison
     * @return nTurnier[]
     */

    public static function get_turniere_offen(bool $asc = true, int $saison = CONFIG::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_names.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                INNER JOIN teams_name
                ON teams_name.team_id = teams_liga.team_id AND teams_name.saison = ?
                WHERE phase = 'offen'
                AND saison = ?
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");
        return db::$db->query($sql, $saison, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte Turniere, die in der melden-Phase sind
     * @param bool $asc
     * @param int $saison
     * @return nTurnier[]
     */

    public static function get_turniere_melde(bool $asc = true, int $saison = CONFIG::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_name.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                INNER JOIN teams_name
                ON teams_name.team_id = teams_liga.team_id AND teams_name.saison = ?
                WHERE phase = 'melde'
                AND saison = ?
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");
        return db::$db->query($sql, $saison, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte Turniere, die in der spielplan-Phase sind
     * @param $saison
     * @return nTurnier[]
     */
    public static function get_turniere_spielplan(bool $asc = true, int $saison = CONFIG::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_name.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                INNER JOIN teams_name
                ON teams_name.team_id = teams_liga.team_id AND teams_name.saison = ?
                WHERE phase = 'spielplan'
                AND saison = ?
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");
        return db::$db->query($sql, $saison, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte kommende Turniere der Saison
     * @param $saison
     * @return nTurnier[]
     */

    public static function get_turniere_kommend(bool $asc = true, int $saison = CONFIG::SAISON): array
    {   
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_name.teamname
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_name
                ON turniere_liga.ausrichter = teams_name.team_id AND turniere_liga.saison = teams_name.saison
                WHERE phase != 'ergebnis'
                AND saison = ?
                AND canceled = 0
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc")
                ;
        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Erhalte Turniere, die in der ergebnis-Phase sind der saison
     * @param $saison
     * @return nTurnier[]
     */

    public static function get_turniere_ergebnis(bool $asc = true, int $saison = CONFIG::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_name.teamname
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_name
                ON turniere_liga.ausrichter = teams_name.team_id AND turniere_liga.saison = teams_name.saison
                WHERE phase = 'ergebnis'
                AND saison = ?
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");
        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Anmeldungen der Warte-, Melde-, Spielen-Liste des aktuellen Turniers
     * 
     * @return array
     */
    public function get_anmeldungen(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liste.*, teams_name.teamname, teams_liga.ligateam
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_name
                ON teams_name.team_id = teams_liga.team_id AND teams_name.saison = ?
                WHERE turniere_liste.turnier_id = ?
                ORDER BY turniere_liste.position_warteliste
                ";
        $anmeldungen = db::$db->query($sql, $saison, $this->turnier_id)->esc()->fetch();

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
                SELECT turniere_liste.*, teams_name.teamname, teams_liga.ligateam
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_name
                ON teams_liga.turnier_id = teams_name.turnier_id AND teams_name.saison = ?
                INNER JOIN turniere_liga
                ON turniere_liga.turnier_id = turniere_liste.turnier_id
                WHERE turniere_liga.saison = ?
                AND turniere_liga.phase != 'ergebnis'
                ORDER BY turniere_liste.position_warteliste
                ";
        $anmeldungen = db::$db->query($sql, $saison, $saison)->esc()->fetch();

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

    public function set_spielenliste_euhc($teamliste)
    {
        // Prüfen ob Spielen-Liste gegeben

            // Blöcke und Wertungen hinzufügen
            $spielenliste = array();
            foreach ($teamliste as $id => $teamname) {
                $temp = new Team($id, skip_init: true);
                $temp->set_name_object_only($teamname);
                $spielenliste[$id] = $temp;
            }

            uasort($spielenliste, static function ($team_a, $team_b) {
                return ($team_a->id <=> $team_b->id);
            });
        $this->spielenliste = $spielenliste;
        $this->anz_spielenliste = count($teamliste);
    }

    /**
     * Get alle Teams auf der Spielenliste des Turniers nach Wertung sortiert.
     *
     * @return array
     */
    public function set_spielenliste(): array
    {
        $sql = "
                SELECT turniere_liste.team_id, teams_name.teamname, teams_liga.ligateam,
                    teams_details.ligavertreter, teams_details.trikot_farbe_1, teams_details.trikot_farbe_2, turniere_liste.freilos_gesetzt
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_name
                ON teams_name.team_id = teams_liga.team_id AND teams_name.saison = ?
                LEFT JOIN teams_details
                ON turniere_liste.team_id = teams_details.team_id
                WHERE turniere_liste.turnier_id = ? 
                AND (turniere_liste.liste = 'setzliste' 
                OR turniere_liste.liste = 'spiele') 
                ";
        $liste = db::$db->query($sql, $this->saison, $this->turnier_id)->esc()->fetch('team_id');

        // Prüfen ob Spielen-Liste gegeben
        if (!empty($liste)) {
            
            // Blöcke und Wertungen hinzufügen
            $spielenliste = array();
            foreach ($liste as $team) {
                $team_id = $team['team_id'];
                
                $temp = new Team($team_id);
                $temp->set_wertigkeit($this->spieltag);
                $temp->set_tblock($this->spieltag);
                $temp->set_freilos_gesetzt($team['freilos_gesetzt']);
                $spielenliste[$team_id] = $temp;
            }

            // Sortierung nach Wertigkeit
            if ($this->get_art() === "final") {
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
                SELECT turniere_liste.team_id, teams_name.teamname, teams_liga.ligateam,
                    teams_details.ligavertreter, teams_details.trikot_farbe_1, teams_details.trikot_farbe_2, turniere_liste.position_warteliste
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_name
                ON teams_name.team_id = teams_liga.team_id AND teams_name.saison = ?
                LEFT JOIN teams_details
                ON turniere_liste.team_id = teams_details.team_id
                WHERE turniere_liste.turnier_id = ? 
                AND turniere_liste.liste = 'warte'
                ORDER BY turniere_liste.position_warteliste
                ";
        $liste = db::$db->query($sql, $this->saison, $this->turnier_id)->esc()->fetch('team_id');

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
                SELECT turniere_liste.team_id, teams_name.teamname, teams_liga.ligateam,
                    teams_details.ligavertreter, teams_details.trikot_farbe_1, teams_details.trikot_farbe_2
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_name
                ON teams_name.team_id = teams_liga.team_id AND teams_name.saison = ?
                LEFT JOIN teams_details
                ON turniere_liste.team_id = teams_details.team_id
                WHERE turniere_liste.turnier_id = ? 
                AND turniere_liste.liste = 'melde'
                ";
        $liste = db::$db->query($sql, $this->saison, $this->turnier_id)->esc()->fetch('team_id');

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
     * Erhalte die Finalturniere
     * 
     * @param int $saison
     * @return nTurnier[]
     */
    public static function get_finalturniere(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*
                FROM turniere_liga
                INNER JOIN turniere_details ON turniere_liga.turnier_id = turniere_details.turnier_id
                WHERE saison = ?
                AND art = 'final';
        ";
        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Get Turniere, welche von eine Team ausgerichtet werden.
     *
     * @param int $team_id
     * @param int $saison
     * @return nTurnier[]
     */
    public static function get_eigene_turniere(int $team_id, int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_name.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                LEFT JOIN teams_name
                ON teams_liga.team_id = teams_name.team_id AND teams_name.saison = ?
                AND ausrichter = ?
                AND saison = ? 
                ORDER BY turniere_liga.datum asc
                ";
        return db::$db->query($sql, $saison, $team_id, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
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
        if($this->get_art() == 'final') {
            return match ($scope) {
                'lc' => Env::BASE_URL . '/ligacenter/lc_spielplan.php?turnier_id=' . $this->turnier_id,
                'tc' => Env::BASE_URL . '/teamcenter/tc_spielplan.php?turnier_id=' . $this->turnier_id,
                default => Env::BASE_URL . '/liga/spielplan_finale.php?turnier_id=' . $this->turnier_id
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
     * Ändert den Ausrichter
     * @param int|null $ausrichter
     * @return nTurnier
     */
    public function set_ausrichter(?int $ausrichter): nTurnier
    {
        if (!Team::is_ligateam($ausrichter)){
            $this->error = true;
            Html::error("Ausrichter nicht gefunden");
        }

        if ($this->ausrichter != $ausrichter) {
            $this->auto_log("Ausrichter", Team::id_to_name($this->ausrichter), Team::id_to_name($ausrichter));
            $this->ausrichter = $ausrichter;
        }
        return $this;
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
     * Erstellt ein neues Turnier.
     *
     * @param int $ausrichter
     * @return nTurnier
     */
    public static function set_turnier(int $ausrichter): nTurnier
    {
        $sql = "
                INSERT INTO turniere_liga (ausrichter, phase) 
                VALUES (?, 'offen')
                ";
        db::$db->query($sql, $ausrichter)->log();

        $turnier_id = db::$db->get_last_insert_id();

        $sql = "
                INSERT INTO turniere_details (turnier_id) 
                VALUES (?)
                ";
        db::$db->query($sql, $turnier_id)->log();

        // Turnierlog beschreiben
        $turnier = self::get($turnier_id);
        $turnier->set_log("Turnier mit ID $turnier_id wurde erstellt. (Ausrichter-ID $ausrichter)");

        // Ausrichter auf dem Turnier melden
        if (!$turnier->is_finalturnier()) {
            $turnier->set_team($ausrichter, 'spiele');
        }

        return $turnier;
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
     * Setzt den Bool für fixierten Block
     * 
     * @return nTurnier
     */
    public function set_fixed_tblock(string $tblock_fixed): nTurnier
    {
        $this->auto_log("Fixer Block", $this->tblock_fixed, $tblock_fixed);
        $this->tblock_fixed = $tblock_fixed;
        return $this;
    }

    /**
     * Setzt den Turniernamen
     * 
     * @return nTurnier
     */
    public function set_tname(string|null $tname): nTurnier
    {
        $this->auto_log("Turniername", $this->tname , $tname);
        $this->tname = $tname;
        return $this;
    }

    /**
     * Setzt den Turnierblock
     * 
     * @return nTurnier
     */
    public function set_tblock(string $tblock): nTurnier
    {
        $this->auto_log("Turnierblock", $this->tblock , $tblock);
        $this->tblock = $tblock;
        return $this;
    }

    /**
     * Setzt das Turnierdatum
     * 
     * @return nTurnier
     */
    public function set_datum(string $datum): nTurnier
    {
        $this->auto_log("Datum", $this->datum , $datum);
        $this->datum = $datum;
        return $this;
    }

    /**
     * Setzt die Saison
     * 
     * @return nTurnier
     */
    public function set_saison(int $saison): nTurnier
    {
        $this->auto_log("Saison", $this->saison , $saison);
        $this->saison = $saison;
        return $this;
    }

    /**
     * Setzt die Startzeit
     * 
     * @return nTurnier
     */
    public function set_startzeit(string $startzeit): nTurnier
    {
        $this->auto_log("Startzeit", $this->startzeit ?? "" , $startzeit);
        $this->startzeit = $startzeit;
        return $this;
    }

    /**
     * Setzt die Besprechung
     * 
     * @return nTurnier
     */
    public function set_besprechung(string $besprechung): nTurnier
    {
        $this->auto_log("Turnierbesprechung", $this->besprechung ?? "" , $besprechung);
        $this->besprechung = $besprechung;
        return $this;
    }

    /**
     * Setzt den Hinweis
     * 
     * @return nTurnier
     */
    public function set_hinweis(string $hinweis): nTurnier
    {
        if ($this->hinweis !== $hinweis){
            $this->set_log("Hinweise geändert:\r\n" . $hinweis);
        }
        $this->hinweis = $hinweis;
        return $this;
    }

    /**
     * Setzt die Anzahl der Plätze
     * 
     * @return nTurnier
     */
    public function set_plaetze(int $plaetze): nTurnier
    {
        $this->auto_log("Plätze", $this->plaetze , $plaetze);
        $this->plaetze = $plaetze;
        return $this;
    }

    /**
     * Setzt das Format
     * 
     * @return nTurnier
     */
    public function set_format(string $format): nTurnier
    {
        $this->auto_log("Format", $this->format , $format);
        $this->format = $format;
        return $this;
    }

    /**
     * Setzt die PLZ
     * 
     * @return nTurnier
     */
    public function set_plz(string $plz): nTurnier
    {
        $this->auto_log("Plz", $this->plz , $plz);
        $this->plz = $plz;
        return $this;
    }

    /**
     * Setzt den Ort
     * 
     * @return nTurnier
     */
    public function set_ort(string $ort): nTurnier
    {
        $this->auto_log("Ort", $this->ort , $ort);
        $this->ort = $ort;
        return $this;
    }

    /**
     * Setzt die Strasse
     * 
     * @return nTurnier
     */
    public function set_strasse(string $strasse): nTurnier
    {
        $this->auto_log("Strasse", $this->strasse , $strasse);
        $this->strasse = $strasse;
        return $this;
    }

    /**
     * Setzt den Hallennamen
     * 
     * @return nTurnier
     */
    public function set_hallennamen(string $hallenname): nTurnier
    {
        $this->auto_log("Hallenname", $this->hallenname , $hallenname);
        $this->hallenname = $hallenname;
        return $this;
    }

    /**
     * Setzt die Haltestelle
     * 
     * @return nTurnier
     */
    public function set_haltestelle(string $haltestelle): nTurnier
    {
        $this->auto_log("Haltestelle", $this->haltestellen , $haltestelle);
        $this->haltestellen = $haltestelle;
        return $this;
    }

    /**
     * Setzt die Handynummer
     * 
     * @return nTurnier
     */
    public function set_handy(string $handy): nTurnier
    {
        $this->auto_log("Handy", $this->handy , $handy);
        $this->handy = $handy;
        return $this;
    }

    /**
     * Setzt den Organisator
     * 
     * @return nTurnier
     */
    public function set_organisator(string $organisator): nTurnier
    {
        $this->auto_log("Organisator", $this->organisator , $organisator);
        $this->organisator = $organisator;
        return $this;
    }

    /**
     * Setzt das Format
     * 
     * @return nTurnier
     */
    public function set_startgebuehr(string $startgebuehr): nTurnier
    {
        $this->auto_log("Startgebühr", $this->startgebuehr , $startgebuehr);
        $this->startgebuehr = $startgebuehr;
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
     * Setzt den Spieltag
     */
    public function set_spieltag(int $spieltag): void
    {
        $this->auto_log("Spieltag", $this->spieltag , $spieltag);
        $this->spieltag = $spieltag;
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
     * Setzt die Mail-Adresse
     * Wird bei den Turnierdetails benötigt 
     */
    public function set_email(string $email): void
    {
        $this->email = $email;
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
     * Setzt das Turnier in die Datenbank
     * 
     * @return nTurnier
     */
    public function set_database(): nTurnier
    {
        if ($this->error){
            Html::error("Turnier konnte nicht in die Datenbank geschrieben werden, da Fehler vorliegen");
            return $this;
        }

        $sql = "
            UPDATE turniere_liga SET 
            tname = ?,
            ausrichter = ?,
            art = ?,
            tblock = ?, 
            tblock_fixed = ?,
            datum = ?,
            spieltag = ?,
            phase = ?,
            spielplan_vorlage = ?,
            spielplan_datei = ?,
            saison = ?
            WHERE turnier_id = ?;
        ";
        db::$db->query(
            $sql,
            $this->tname,
            $this->ausrichter,
            $this->art,
            $this->tblock,
            $this->tblock_fixed,
            $this->datum,
            $this->spieltag,
            $this->phase,
            $this->spielplan_vorlage,
            $this->spielplan_datei,
            $this->saison,
            $this->turnier_id
        )->log();

        $sql = "
            UPDATE turniere_details SET
            hallenname = ?,
            strasse = ?, 
            plz = ?,
            ort = ?,
            haltestellen = ?,
            plaetze = ?,
            format = ?,
            startzeit = ?,
            besprechung = ?,
            hinweis = ?,
            organisator = ?,
            handy = ?,
            startgebuehr = ?
            WHERE turnier_id = ?
        ";
        db::$db->query(
            $sql,
            $this->hallenname,
            $this->strasse,
            $this->plz,
            $this->ort,
            $this->haltestellen,
            $this->plaetze,
            $this->format,
            $this->startzeit,
            $this->besprechung,
            $this->hinweis,
            $this->organisator,
            $this->handy,
            $this->startgebuehr,
            $this->turnier_id
        )->log();

        $this->meldeliste = $this->set_meldeliste();
        $this->anz_meldeliste = $this->set_anz_meldeliste();

        $this->warteliste = $this->set_warteliste();
        $this->anz_warteliste = $this->set_anz_warteliste();

        $this->spielenliste = $this->set_spielenliste();
        $this->anz_spielenliste = $this->set_anz_spielenliste();

        $this->freie_plaetze = $this->set_freie_plaetze();
        $this->freie_plaetze_status = $this->set_freie_plaetze_status();

        return $this;
    }

    /**
     * Ein Team zum Turnier aufSetzListe
     *
     * Bei Anmeldung auf die Warteliste sollte $pos als die jeweilige Wartelistenposition übergeben werden
     * Könnnte man das auch mit nl_anmelden für nichtligateams zusammenlegen? 
     * PeKA: Das NL-Team muss angelegt werden, bevor es in der Turnier gesetzt wird. Dann sollte das gehen.
     *
     * @param int $team_id
     * @param string $liste
     * @param int $pos
     */
    public function set_team(int $team_id, string $liste, int $pos = 0): void
    {
        // Update der Wartelistepositionen
        if ($liste === 'warte') {
            $sql = "
                    UPDATE turniere_liste 
                    SET position_warteliste = position_warteliste + 1 
                    WHERE turnier_id = ? 
                    AND liste = 'warte' 
                    AND position_warteliste >= ?";
            db::$db->query($sql, $this->turnier_id, $pos)->log();
            if (db::$db->affected_rows() > 0) {
                $this->set_log("Warteliste aktualisiert");
            }
        }
        $sql = "
                INSERT INTO turniere_liste (turnier_id, team_id, liste, position_warteliste) 
                VALUES (?, ?, ?, ?)
                ";
        db::$db->query($sql, $this->turnier_id, $team_id, $liste, $pos)->log();
        $this->set_log(
            "Anmeldung:\r\n" . Team::id_to_name($team_id) . " ($liste)"
                . (($liste === 'warte') ? "\r\nWartepos: $pos" : '')
                . "\r\nTeamb.: " . Tabelle::get_team_block($team_id) . " | Turnierb. " . $this->tblock
        );
    }

    /**
     * Meldet ein Nichtligateam an
     *
     * Existiert bereits ein Nichtligateam mit gleichem Namen in der Datenbank, so wird dieses angemeldet es wird also
     * kein neues Nichtligateam erstellt
     *
     *
     * @param $teamname
     * @param $liste
     * @param int $pos
     */
    public function set_nl_team($teamname, $liste, int $pos = 0): void
    {
        // Nichtligateams haben einen Stern hinter dem Namen
        $teamname .= "*";

        if (Team::name_to_id($teamname) === NULL) {
            $sql = "
                    INSERT INTO teams_liga (teamname, ligateam) 
                    VALUES (?, 'Nein')
                    ";
            db::$db->query($sql, $teamname)->log();
            $nl_team_id = db::$db->get_last_insert_id();
        } else {
            $nl_team_id = Team::name_to_id($teamname);
        }

        $this->set_team($nl_team_id, $liste, $pos);
    }

    /**
     * Team via Freilos aufSetzListe
     *
     * @param int $team_id
     */
    public function freilos(int $team_id): void
    {
        // Freilos abziehen
        $team = new Team($team_id);
        $freilose = $team->get_freilose();
        $team->set_freilose($freilose - 1);

        // Auf die Spielenliste setzen
        $sql = "
                INSERT INTO turniere_liste (turnier_id, team_id, liste, freilos_gesetzt) 
                VALUES (?, ?, 'spiele', 'Ja')
                ";
        db::$db->query($sql, $this->turnier_id, $team_id)->log();

        $this->set_log(
            "Freilos:\r\n" . Team::id_to_name($team_id) . " (spiele)"
                . "\r\nTeamb.: " . Tabelle::get_team_block($team_id) . " | Turnierb. " . $this->tblock
        );
    }

    /**
     * Team wird von einem Turnier abgemeldet
     * @param int $team_id
     */
    public function set_abmeldung(int $team_id): void
    {
        $sql = "
                DELETE FROM turniere_liste 
                WHERE turnier_id = ?
                AND team_id = ?
                ";
        db::$db->query($sql, $this->turnier_id, $team_id)->log();
        if (db::$db->affected_rows() > 0) {
            $this->set_log("Abmeldung:\r\n" . Team::id_to_name($team_id));
        }
    }

    /**
     * Sucht alle Wartelisteneinträge und sortiert diese der größe ihrer Position auf der Warteliste. Anschließend
     * werden die Wartelistenpostionen von 1 auf wieder vergeben
     *
     * Bsp: Position auf der Warteliste: 2 4 5 wird zu 1 2 3
     *
     */
    public function warteliste_aktualisieren(): void
    {
        // Warteliste holen
        $sql = "
                SELECT * 
                FROM turniere_liste 
                WHERE turnier_id = ? 
                AND liste = 'warte' 
                ORDER BY position_warteliste
                ";
        $liste = db::$db->query($sql, $this->turnier_id)->fetch('team_id');

        // Warteliste korrigieren
        $pos = $affected_rows = 0;
        foreach ($liste as $team) {
            $sql = "
                    UPDATE turniere_liste 
                    SET position_warteliste = ?
                    WHERE turnier_id = ?
                    AND liste = 'warte'
                    AND team_id = ?;
                    ";
            db::$db->query($sql,  ++$pos, $this->turnier_id,$team['team_id'])->log();
            $affected_rows += db::$db->affected_rows();
            $logs[] = $pos . ". " . Team::id_to_name($team['team_id']);
        }
        if ($affected_rows > 0) {
            $this->set_log("Warteliste aktualisiert:\r\n" . implode("\r\n", $logs ?? []));
        }
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
     * Ändert die Liste auf der sich ein Team befindet (Warte-, Melde- oder Spielen-Liste)
     *
     * @param int $team_id
     * @param string $liste
     * @param int $pos
     */
    public function set_liste(int $team_id, string $liste, int $pos = 0): void
    {
        $sql = "
                UPDATE turniere_liste 
                SET liste = ?, position_warteliste = ? 
                WHERE turnier_id = ? 
                AND team_id = ?
                ";
        db::$db->query($sql, [$liste, $pos, $this->turnier_id, $team_id]);
        $this->set_log("Listenwechsel:\r\n"
            . Team::id_to_name($team_id) . " ($liste)"
            . (($liste === 'warte') ? "\r\nWartepos: $pos" : ''));
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
     * Löscht das Turnier aus der DB und vermerkt das Turnier in der Tabelle gelöschte TUrniere
     *
     * Grund des Löschens
     * @param string $grund
     */
    public function delete(string $grund = ''): void
    {
        // Datenbank Backup
        db::sql_backup();

        // Turnier in der Datenbank vermerken
        $sql = "
                INSERT INTO turniere_geloescht (turnier_id, datum, ort, grund, saison) 
                VALUES (?, ?, ?, ?, ?)
                ";
        $params = [$this->turnier_id, $this->datum, $this->ort, $grund, $this->saison];
        db::$db->query($sql, $params)->log();

        // Turnier aus der Datenbank löschen
        $sql = "
                DELETE FROM turniere_liga 
                WHERE turnier_id = $this->turnier_id
                ";
        db::$db->query($sql)->log();
        $this->set_log("Turnier wurde gelöscht.");
        // Spieltage neu sortieren
        Ligabot::set_spieltage();
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
     * Ermittelt, ob ein Team bei diesem Turnier ein Freilos setzten darf
     * @param int $team_id
     * @return bool
     */
    public function is_spielberechtigt_freilos(int $team_id): bool
    {
        $team_block = Tabelle::get_team_block($team_id);
        $turnier_block = $this->tblock;
        $turnier_art = $this->art;

        if ($team_block === null) {
            return true;
        }

        // Check ob es sich um ein Ligaturnier handelt
        if (in_array($turnier_art, Config::TURNIER_ARTEN)) {

            // Finde Index des Blocks im Block-Array
            $pos_turnier = array_search($turnier_block, Config::BLOCK_ALL, true);
            $team_block = str_split($team_block);

            // Prüfe, ob sich der Teamblock im Array dahinter und somit unter dem Turnierblock befindet
            for ($i = $pos_turnier; $i <= (count(Config::BLOCK_ALL) - 1); $i++) {
                foreach ($team_block as $buchstabe) {
                    $turnier_block = str_split(Config::BLOCK_ALL[$i]);
                    if (in_array($buchstabe, $turnier_block)) return true;
                }
            }
        }

        return false;
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
     * Ermittelt, ob sich ein Team für das Turnier überhaupt aufSetzListe kann
     * 
     * @return bool
     */
    public function is_anmeldung_moeglich()
    {
        if ($this->phase === 'spielplan') {
            Html::error("Das Turnier ist in der Spielplanphase. Eine Anmeldung ist nicht mehr möglich.");
            return false;
        }

        if ($this->phase == 'ergebnis') {
            Html::error("Das Turnier ist in der Ergebnisphase. Eine Anmeldung ist nicht mehr möglich.");
            return false;
        }

        return true;
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

    /**
     * Ermittelt, ob ein Turnier nach oben erweiterbar ist
     * 
     * @return bool
     */
    public function is_erweiterbar_blockhoch()
    {
        if (
            $this->phase === 'melde'
            && strlen($this->tblock) < 3
            && $this->tblock !== 'AB'
            && $this->tblock !== 'A'
            && ($this->art === 'I' || $this->art === 'II')
        ) {
            return true;
        } else {
            return false;
        };
    }

    /**
     * Ermittelt, ob ein Turnier auf ABCDEF zu öffnen ist
     */
    public function is_erweiterbar_blockfrei()
    {
        if (
            $this->phase === 'melde'
            && ($this->art === 'I' || $this->art === 'II')
        ) {
            return true;
        } else {
            return false;
        };
    }

    /**
     * Ermittelt, ob die Anzahl der Plätze eines Turniers geändert werden kann
     */
    public function is_erweiterbar_plaetze()
    {
        return $this->phase == 'melde';
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

    /**
     * Füllt freie Plätze auf der Spielen-Liste von der Warteliste aus wieder auf,
     * wenn der Teamblock des Wartelisteneintrags zum Turnier passt,
     * wenn das Turnier nicht in der offenen Phase ist,
     * wenn das Turnier noch freie Plätze hat.
     *
     * @param bool $send_mail
     */
    public function spieleliste_auffuellen(bool $send_mail = true): void //TODO Keine NLs nachrücken, wenn < 4 Ligateams, aber dann wieder NL mitrücken wenn ok
    {
        $freie_plaetze = $this->freie_plaetze;
        $log = false;

        if ($this->phase === 'melde' && $freie_plaetze > 0) {
            $liste = $this->get_warteliste(); // Order by Warteliste weshalb die Teams in der foreach schleife in der Richtigen reihenfolge behandelt werden

            foreach ($liste as $team) {
                if ($freie_plaetze > 0) {
                    if ($this->is_spielberechtigt($team->id)) {
                        // Prüfen, ob das Team schon bei einem anderen Turnier auf der Spielen-Liste stehen
                        if ($this->is_doppelmeldung($team->id)) {
                            $this->set_abmeldung($team->id);
                        } else { 
                            $this->set_liste($team->id, 'spiele');
                            if ($send_mail) {
                                MailBot::mail_warte_zu_spiele($this, $team->id);
                            }
                            --$freie_plaetze;
                            $log = true;
                        }
                    }
                }
            }

            if ($log) {
                $this->set_log("Spielen-Liste aufgefüllt");
            }
            $this->warteliste_aktualisieren();
        }
    }
}
