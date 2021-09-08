<?php

class db
{
    public static null|ndbWrapper $db;

    /**
     * Stellt die Verbindung zur Datenbank her
     *
     * Hiermit kann von überall her auf den dbWrapper zugegriffen werden ohne das Datenbank-Klassenobjekt
     * immer übergeben zu müssen.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $database
     */
    public static function initialize(string $host = Env::HOST_NAME,
                                      string $user = Env::USER_NAME,
                                      string $password = Env::PASSWORD,
                                      string $database = Env::DATABASE): void
    {
        self::$db = new ndbWrapper($host, $user, $password, $database);
    }

    /**
     * Terminiert die Datenbankverbindung und ermöglicht eine neue Initialisierung zu einer anderen Datenbank
     */
    public static function terminate(): void
    {
        self::$db = NULL;
    }

    /**
     * Escaped mit htmlspecialchars Strings um XSS zu verhindern.
     * Auch rekursiv für ganze Arrays.
     *
     * @param mixed $input
     * @return mixed
     */
    public static function escape(mixed $input): mixed
    {
        if (is_array($input)) {
            foreach ($input as $key => $value) { // Rekursion
                $key = self::escape($key);
                $value = self::escape($value);
                $output[$key] = $value;
            }
        } else {
            $output = (is_string($input))
                ? htmlspecialchars($input, ENT_QUOTES, 'UTF-8', false)
                : $input;
        }
        return $output ?? $input;
    }

    /**
     * Validiert ob eine Spalte einer Tabelle wirklich die Spalte einer Tabelle ist
     *
     * @param string $table
     * @param string $column
     * @return string
     */
    public static function escape_column(string $table, string $column): string
    {
        $sql = "
                SELECT TABLE_NAME 
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_TYPE = 'BASE TABLE' 
                AND TABLE_SCHEMA = ?
                ";
        $tables = self::$db->query($sql, ENV::DATABASE)->list('TABLE_NAME');
        if (!in_array($table, $tables, true)) {
            trigger_error("Ungültiger Tabellenname $table", E_USER_ERROR);
        }

        // Validieren, ob der Spaltenname ein echter Spaltenname ist
        $sql = "SHOW FIELDS FROM $table";
        $columns = self::$db->query($sql)->list('Field');
        if (!in_array($column, $columns, true)) {
            trigger_error("Ungültiger Spaltenname $column", E_USER_ERROR);
        }
        return "`" . $column . "`";
    }

    /**
     * Entfernt voran- oder hintenstehende Leerzeichen für Parameter von Prerpared-Statements
     *
     * @param mixed $params
     * @return mixed
     */
    public static function trim_params(mixed $params): mixed
    {
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                if (is_string($params)) {
                    $params[$key] = trim($param);
                }
            }
        } else if (is_string($params)) {
            $params = trim($params);
        }
        return $params;
    }

    /**
     * Schreibt alle deklarierten Variablen unter die Navigation in HTML //true=1 false=0
     * Schreibt auch das Dokument und Zeile der Variablen aus
     * z.B. db::debug(get_defined_vars(), true); oder db::debug($GLOBALS);
     *
     * @param mixed $input Zu debuggende Variable
     * @param bool $types Sollen Typen angezeigt werden?
     */
    public static function debug(mixed $input, bool $types = false): void
    {
        $input = self::escape($input);
        // Show Types?
        if ($types) {
            ob_start();
            var_dump($input);
            $string = ob_get_clean();
        } else {
            $string = print_r($input, true);
        }
        $backtrace = debug_backtrace();
        Html::info('<p>File: ' . $backtrace[0]['file']
            . '<br>Line: ' . $backtrace[0]['line']
            . '</p><pre>' . $string . '</pre>',
            'DEBUG',
            false);
    }

    /**
     * Sichert die Datenbank
     *
     * Name der Sicherung
     * @return string
     */
    public static function sql_backup(): string
    {
        // Dateiname der Sicherungskopie
        $dumpfile = "../../system/backups/" . Env::DATABASE . "." . date("Y-m-d_H-i-s") . ".sql";

        exec("mysqldump --user=" . Env::USER_NAME
            . " --password=" . Env::PASSWORD
            . " --host=" . Env::HOST_NAME
            . " " . Env::DATABASE . " > " . $dumpfile);

        Html::info("Datenbank wurde gesichert als " . date("Y-m-d_H-i-s") . ".sql in system/backup/");
        return $dumpfile;
    }
}