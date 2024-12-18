<?php

class ndbwrapper
{

    private PDO $pdo;
    private false|PDOStatement $stmt;
    private bool $escape_result = false;

    /**
     * Nur für Logs verwendet
     */
    public static int $query_count = 0;
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
        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $user, $password, $options);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Führt eine SQL-Query aus
     *
     * @param string $sql Query mit ? als einzufügende Variablen
     * @param mixed ...$params Parameter in Reihenfolge der ?, entweder als Array oder als mehrere Argumente
     * @return $this
     */
    public function query(string $sql, mixed ...$params): ndbWrapper
    {
        // Reset Optionen
        $this->escape_result = false;
        unset($this->result, $this->stmt, $this->sql, $this->params);

        // Prepare
        $this->stmt = $this->pdo->prepare($sql);

        // Parameter in ein Array parsen, falls erster Parameter ein Array ist
        if (!empty($params)) {

            if (is_array($params[array_key_first($params)])) {
                $params = $params[array_key_first($params)]; // Params wurden als Array übergeben
            }

            $params = db::trim_params($params);
        }

        // Ausführen mit den Parametern
        $this->stmt->execute($params);

        // Für Logs
        $this->sql = $sql;
        $this->params = $params;
        self::$query_count++;

        return $this;
    }

    public function stmt(): bool|PDOStatement
    {
        return $this->stmt;
    }

    /**
     * Gibt das erste Element der ersten Reihe aus.
     *
     * @return mixed
     */
    public function fetch_one(): mixed
    {
        if ($this->escape_result) return db::escape($this->stmt->fetch(PDO::FETCH_NUM)[0] ?? NULL);
        return $this->stmt->fetch(PDO::FETCH_NUM)[0] ?? NULL;
    }

    public function fetch_object(String $class, array $args = []): object|null
    {
        return $this->stmt->fetchObject($class, $args) ?: NULL;
    }

    public function fetch_objects(string $class, ?String $key = NULL, array $constructor_args = []): array
    {
        // Ansonsten kann das stmt bei weiteren Querys im Constructor überschrieben werden.
        // Die while-Schleife läuft dann nicht durch.
        $safe_stmt = $this->stmt;

        while ($object = $safe_stmt->fetchObject($class, $constructor_args)) {
            is_null($key) ? $objects[] = $object : $objects[$object->$key] = $object;
        }

        return $objects ?? [];
    }

    /**
     * Gibt die erste Reihe als Array aus
     *
     * @return array
     */
    public function fetch_row(): array
    {
        $return = $this->stmt->fetch(PDO::FETCH_ASSOC);
        if ($return === false) {
            return [];
        }

        if ($this->escape_result) {
            return db::escape($return);
        }

        return $return;
    }

    /**
     * Gibt eine Spalte als Liste zurück
     *
     * @param string $spalte
     * @param string $key (optional) String eines Spaltennamens, welcher als Key des Arrays verwendet werden soll.
     * @return array
     */
    public function list(string $spalte, string $key = ''): array //TODO: PDO::FETCH_KEY_PAIR ?
    {
        if (empty($key)) {
            while ($x = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $return[] = $x[$spalte];
            }
        } else {
            while ($x = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $return[$x[$key]] = $x[$spalte];
            }
        }
        return ($this->escape_result) ? db::escape($return ?? []) : $return ?? [];
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
            while ($x = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $return[] = $x;
            }
        } else {
            while ($x = $this->stmt->fetch(PDO::FETCH_ASSOC)) {
                $return[$x[$key]] = $x;
            }
        }
        return ($this->escape_result) ? db::escape($return ?? []) : $return ?? [];
    }

    /**
     * Wie viele Reihen werden zurückgegeben?
     *
     * @return int
     */
    public function num_rows(): int // TODO: same as affected_rows
    {
        return $this->stmt->rowCount();
    }

    /**
     * Wie viele Reihen wurden verändert?
     *
     * @return int
     */
    public function affected_rows(): int
    {
        return $this->stmt->rowCount();
    }

    /**
     * Die zuletzt eingefügte ID (auto increment)
     *
     * @return int
     */
    public function get_last_insert_id(): int
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Abfrageergebnis wird escaped, um es im HTML-Dokument anzuzeigen
     * Muss vor Fetch stehen!
     *
     * @return ndbWrapper
     */
    public function esc(): ndbWrapper
    {
        $this->escape_result = true;
        return $this;
    }

    /**
     * Log für die Query in Config::LOG_DB
     *
     * @param bool $anonym Anonyme Params (zB. für Abstimmungen)
     * @return $this
     */
    public function log(bool $anonym = false): ndbWrapper
    {
        // Query formatieren
        $sql = trim(preg_replace("/(^\h+|\h+$)/m", '', $this->sql)); // Schönere Formatierung

        // Parameter formatieren
        if (!empty($this->params)) {
            $params = ($anonym) ? "\nAnonyme Query" : "\n?: " . implode("\n?: ", $this->params);
        }

        // Log-Text
        $log = $sql . ($params ?? '');
        Helper::log(Config::LOG_DB, $log);
        return $this;
    }

}