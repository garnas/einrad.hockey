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
    function __construct(string $host, string $user, string $password, string $database)
    {
        $this->link = new mysqli($host, $user, $password, $database);
        if ($this->link->connect_errno) {
            Form::log(Config::LOG_DB, "ERROR Verbindung: " . mysqli_connect_error());
            die("Verbindung zur Datenbank nicht möglich.");
        }
        $this->link->set_charset("utf-8");
    }

    /**
     * Führt eine SQL-Query aus
     *
     * @param string $sql Query mit ? als einzufügende Variablen
     * @param mixed ...$params Parameter in Reihenfolge der ?, entweder als Array oder als mehrere Argumente
     * @return $this
     */
    function query(string $sql, mixed ...$params): dbWrapper
    {
        // Reset Optionen
        $this->escape_result = false;
        unset($this->result, $this->stmt);
        unset($this->sql, $this->params); // Nur für Logs

        // Prepare
        if (!$this->stmt = $this->link->prepare($sql)){
            Form::log(Config::LOG_DB, "ERROR " . dbi::escape($this->link->error));
//            die(dbi::escape($this->link->error));
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
                Form::log(Config::LOG_DB, "ERROR Falsche Anzahl an Parametern für Mysqli-Prepare");
//                die("Falsche Anzahl an Parametern für Mysqli-Prepare");
            }
            $params = dbi::trim_params($params);
            $this->bind($params);
        }

        // Ausführen
        if (!$this->stmt->execute()){
            Form::log(Config::LOG_DB, "ERROR " . dbi::escape($this->stmt->error));
//            die(dbi::escape($this->stmt->error));
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
        array_unshift($args_ref, $types);
        call_user_func_array([$this->stmt, 'bind_param'], $args_ref);
    }

    /**
     * Gibt das erste Element der ersten Reihe aus.
     *
     * @return mixed
     */
    function fetch_one(): mixed
    {
        if ($this->escape_result) return dbi::escape($this->result->fetch_array()[0] ?? null);
        return $this->result->fetch_array()[0] ?? null;
    }

    /**
     * Gibt die erste Reihe als Array aus
     *
     * @return array
     */
    function fetch_row(): array
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
    function list(string $spalte, string $key = ''): array
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
    function fetch(string $key = ''): array
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
    function num_rows(): int
    {
        return $this->result->num_rows;
    }

    /**
     * Wie viele Reihen wurden verändert?
     *
     * @return int
     */
    function affected_rows(): int
    {
        return $this->stmt->affected_rows;
    }

    /**
     * Die zuletzt eingefügte ID (auto increment)
     *
     * @return int
     */
    function get_last_insert_id(): int
    {
        return dbi::$db->link->insert_id;
    }

    /**
     * Abfrageergebnis wird escaped, um es im HTML-Dokument anzuzeigen
     * Muss vor Fetch stehen!
     *
     * @return $this
     */
    function esc(): dbWrapper
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
    function log(bool $anonym = false): dbWrapper
    {
        // Wer?
        $autoren = [
            $_SESSION['logins']['team']['name'] ?? '',
            $_SESSION['la_login_name'] ?? '',
            $_SESSION['ligabot'] ?? ''
        ];
        $autoren = implode(" | ", array_filter($autoren));

        // Welche Query?
        $sql = trim(preg_replace("/(^\h+|\h+$)/m", '', $this->sql)); // Schönere Formatierung

        // Welche Werte?
        if (!empty($this->params)){
            $params = ($anonym) ? "\nAnonyme Query" :  "\n?: " . implode("\n?: ", $this->params);
        }

        // Log-Text
        $log = $autoren . "\n" . $sql . ($params ?? '') . "\n";
        Form::log(Config::LOG_DB, $log);
        return $this;
    }

    /**
     * Beendet die Verbindung bei der Löschung des Objektes
     */
    function __destruct()
    {
        $this->link->close();
    }
}