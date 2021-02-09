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
    private static string $log_file = "log_db.log";
    private false|mysqli $link;
    private false|mysqli_stmt $stmt;
    private false|mysqli_result $result;
    private bool $escape_result = false;
    private bool $write_logs = false;
    private bool $anonyme_logs = false;

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
    function __construct($host = Config::HOST_NAME,
                         $user = Config::USER_NAME,
                         $password = Config::PASSWORD,
                         $database = Config::DATABASE)
    {
        $this->link = new mysqli($host, $user, $password, $database);
        if ($this->link->connect_errno) {
            Form::log(self::$log_file, "ERROR Verbindung: " . mysqli_connect_error());
            die("Die Webseite der Einradhockeyliga ist zurzeit offline.");
        }
    }

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

    function query(string $sql, mixed ...$params): dbWrapper
    {
        // Reset Optionen
        $this->escape_result = false;
        unset($this->sql, $this->params); // Nur für Logs

        // Prepare
        !$this->stmt = $this->link->prepare($sql);

        // Parameter übergeben
        if (!empty($params)) {
            if (is_array($params[array_key_first($params)])) {
                $params = $params[array_key_first($params)];
            }
            $params = dbi::trim_params($params);
            $this->bind($params);
        }

        // Ausführen
        $this->stmt->execute();
        $this->result = $this->stmt->get_result();

        // Für Logs
        $this->sql = $sql;
        $this->params = $params;
        $this->query_count++;

        return $this;
    }

    function fetch_row(): null|array
    {
        if ($this->escape_result) return dbi::escape($this->result->fetch_assoc());
        return $this->result->fetch_assoc() ?? [];
    }

    function fetch_one(): mixed
    {
        if ($this->escape_result) return dbi::escape($this->result->fetch_array()[0] ?? null);
        return $this->result->fetch_array()[0] ?? null;
    }

    function fetch($key = null): null|array
    {
        if (is_null($key)) {
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

    function num_rows(): int
    {
        return $this->result->num_rows;
    }

    function esc(): dbWrapper
    {
        $this->escape_result = true;
        return $this;
    }

    private function get_type($var): string
    {
        if (is_string($var)) return 's';
        if (is_int($var)) return 'i';
        if (is_float($var)) return 'd';
        return 'b';
    }

    function log(bool $anonym = false): dbWrapper
    {
        // Wer?
        $autoren = [
            $_SESSION['teamname'] ?? '',
            $_SESSION['la_login_name'] ?? '',
            $_SESSION['ligabot'] ?? ''
        ];
        $autoren = implode(" | ", array_filter($autoren));

        // Welche Query?
        $sql = trim(preg_replace("/(^\h+|\h+$)/m", '', $this->sql)); // Schönere Formatierung

        // Welche Werte?
        if (!empty($this->params)){
            $params = ($anonym) ? "\nAnonyme Query" :  "\n?: " . implode("\n?: ", $this->params ?? []);
        }


        // Log-Text
        $log = $autoren . "\n" . $sql . $params;
        Form::log(self::$log_file, $log);
        return $this;
    }

    private function log_query($sql, $params)
    {

    }

    /**
     * Beendet die Verbindung bei der automatischen Zerstörung des Objektes
     */
    function __destruct()
    {
        $this->link->close();
    }
}