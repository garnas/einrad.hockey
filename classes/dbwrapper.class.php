<?php

/**
 * Datenbankklasse
 *
 * Datenbank-Zugangsdaten können in config.class.php geändert werden!
 *
 * $sql = Sql-Befehl als String
 */
class dbWrapper
{

    private false|mysqli $link;
    private false|mysqli_stmt $stmt;
    private false|mysqli_result $result;
    private bool $escape_result = false;

    /**
     * Nur für Logs verwendet
     */
    public int $query_count = 0;
    private string $sql;
    private mixed $params;

    /**
     * Klasse für Datenbankabfragen mit mysqli Prepared-Statements
     *
     * Wird nur in Verbindung mit der static Class dbi.class.php verwendet. Oder als neues Objekt erstellt, um auf eine
     * andere Datenbank zuzugreifen.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     */
    public function __construct(string $host, string $user, string $password, string $database)
    {
        $this->link = new mysqli($host, $user, $password, $database);
        if ($this->link->connect_errno) {
            Helper::log(Config::LOG_DB, "ERROR Verbindung: " . mysqli_connect_error());
            die("Verbindung zur Datenbank nicht möglich.");
        }
        $this->link->set_charset("utf-8mb4");
    }

    /**
     * Führt eine SQL-Query aus
     *
     * @param string $sql Query mit ? als einzufügende Variablen
     * @param mixed ...$params Parameter in Reihenfolge der ?, entweder als Array oder als mehrere Argumente
     * @return $this
     */
    public function query(string $sql, mixed ...$params): dbWrapper
    {
        // Reset Optionen
        $this->escape_result = false;
        unset($this->result, $this->stmt, $this->sql, $this->params);

        // Prepare
        if (!$this->stmt = $this->link->prepare($sql)){
            Helper::log(Config::LOG_DB, "ERROR " . dbi::escape($this->link->error));
        }
        // Parameter übergeben
        if ($this->stmt->param_count > 0) { // Alternativ if (!empty($params))
            if (is_array($params[array_key_first($params)])) {
                $params = $params[array_key_first($params)];
            }
            if ($this->stmt->param_count != count($params)){
                $this->sql = $sql;
                $this->params = $params;
                $this->log();
                Helper::log(Config::LOG_DB, "ERROR Falsche Anzahl an Parametern für Mysqli-Prepare");
            }
            $params = dbi::trim_params($params);
            $this->bind($params);
        }

        // Ausführen
        if (!$this->stmt->execute()){
            Helper::log(Config::LOG_DB, "ERROR " . dbi::escape($this->stmt->error));
        }
        $this->result = $this->stmt->get_result();

        // Für Logs
        $this->sql = $sql;
        $this->params = $params;
        $this->query_count++;

        return $this;
    }

    /**
     * Fügt die Variablen in die Fragezeichen der Query ein.
     *
     * @param $params
     */
    function bind($params)
    {
        $args_ref = [];
        $types = '';
        foreach ($params as $key => &$param) {
            $types .= $this->get_type($params[$key]);
            $args_ref[] = &$param;
        }
        unset($param);

        array_unshift($args_ref, $types);
        call_user_func_array([$this->stmt, 'bind_param'], $args_ref);
    }

    /**
     * Gibt das erste Element der ersten Reihe aus.
     *
     * @return mixed
     */
    public function fetch_one(): mixed
    {
        if ($this->escape_result) return dbi::escape($this->result->fetch_array()[0] ?? null);
        return $this->result->fetch_array()[0] ?? null;
    }

    /**
     * Gibt die erste Reihe als Array aus
     *
     * @return array
     */
    public function fetch_row(): array
    {
        if ($this->escape_result) return dbi::escape($this->result->fetch_assoc() ?? []);
        return $this->result->fetch_assoc() ?? [];
    }

    /**
     * Gibt eine Spalte als Liste zurück
     *
     * @param string $spalte
     * @param string $key (optional) String eines Spaltennamens, welcher als Key des Arrays verwendet werden soll.
     * @return array
     */
    public function list(string $spalte, string $key = ''): array
    {
        if (empty($key)) {
            while ($x = $this->result->fetch_assoc()) {
                $return[] = $x[$spalte];
            }
        } else {
            while ($x = $this->result->fetch_assoc()) {
                $return[$x[$key]] = $x[$spalte];
            }
        }
        return ($this->escape_result) ? dbi::escape($return ?? []) : $return ?? [];
    }

    /**
     * Gibt alle Reihen als Array aus.
     *
     * @param string $key (optional) String eines Spaltennamens, welcher als Key des Arrays verwendet werden soll.
     * @return array
     */
    public function fetch(string $key = ''): array
    {
        if (empty($key)) {
            while ($x = $this->result->fetch_assoc()) {
                $return[] = $x;
            }
        } else {
            while ($x = $this->result->fetch_assoc()) {
                $return[$x[$key]] = $x;
            }
        }
        return ($this->escape_result) ? dbi::escape($return ?? []) : $return ?? [];
    }

    /**
     * Wie viele Reihen werden zurückgegeben?
     *
     * @return int
     */
    public function num_rows(): int
    {
        return $this->result->num_rows;
    }

    /**
     * Wie viele Reihen wurden verändert?
     *
     * @return int
     */
    public function affected_rows(): int
    {
        return $this->stmt->affected_rows;
    }

    /**
     * Die zuletzt eingefügte ID (auto increment)
     *
     * @return int
     */
    public function get_last_insert_id(): int
    {
        return dbi::$db->link->insert_id;
    }

    /**
     * Abfrageergebnis wird escaped, um es im HTML-Dokument anzuzeigen
     * Muss vor Fetch stehen!
     *
     * @return $this
     */
    public function esc(): dbWrapper
    {
        $this->escape_result = true;
        return $this;
    }

    /**
     * Hilfsfunktion für Type-Hinting
     *
     * @param mixed $var
     * @return string
     */
    private function get_type(mixed $var): string
    {
        if (is_string($var)) return 's';
        if (is_int($var)) return 'i';
        if (is_float($var)) return 'd';
        return 'b';
    }

    /**
     * Log für die Query in Config::LOG_DB
     *
     * @param bool $anonym Anonyme Params (zB. für Abstimmungen)
     * @return $this
     */
    public function log(bool $anonym = false): dbWrapper
    {
        // Query formatieren
        $sql = trim(preg_replace("/(^\h+|\h+$)/m", '', $this->sql)); // Schönere Formatierung

        // Parameter formatieren
        if (!empty($this->params)){
            $params = ($anonym) ? "\nAnonyme Query" :  "\n?: " . implode("\n?: ", $this->params);
        }

        // Log-Text
        $log = $sql . ($params ?? '');
        Helper::log(Config::LOG_DB, $log);
        return $this;
    }

    /**
     * Beendet die Verbindung bei der Löschung des Objektes
     */
    public function __destruct()
    {
        $this->link->close();
    }
}