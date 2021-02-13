<?php

class dbi
{
    public static NULL|dbWrapper $db;

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
    public static function initialize(string $host = Config::HOST_NAME,
                                      string $user = Config::USER_NAME,
                                      string $password = Config::PASSWORD,
                                      string $database = Config::DATABASE)
    {
        self::$db = new dbWrapper($host, $user, $password, $database);
    }

    /**
     * Terminiert die Datenbankverbindung und ermöglicht eine neue Initialisierung zu einer anderen Datenbank
     */
    public static function terminate(){
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
     * Entfernt voran- oder hintenstehende Leerzeichen für Parameter von Prerpared-Statements
     *
     * @param mixed $params
     * @return mixed|string
     */
    public static function trim_params(mixed $params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $param) {
                if (is_string($params)) $params[$key] = trim($param);
            }
        } else {
            if (is_string($params)) $params = trim($params);
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
    public static function debug(mixed $input, $types = false)
    {
        // Show Types?
        if ($types) {
            ob_start();
            var_dump($input);
            $string = ob_get_clean();
        } else {
            $string = print_r($input, true);
        }
        $backtrace = debug_backtrace();
        Form::affirm('<p>File: ' . $backtrace[0]['file'] . '<br>Line: ' . $backtrace[0]['line'] . '</p><pre>' . $string . '</pre>');
    }

    /**
     * Sichert die Datenbank
     *
     * Name der Sicherung
     * @return string
     */
    public static function sql_backup(): string
    {
        $dumpfile = "../../system/backups/" . Config::DATABASE . "." . date("Y-m-d_H-i-s") . ".sql"; //Dateiname der Sicherungskopie
        exec("mysqldump --user=" . Config::USER_NAME
            . " --password=" . Config::PASSWORD
            . " --host=" . Config::HOST_NAME
            . " " . Config::DATABASE  . " > " . $dumpfile);
        Form::affirm("Datenbank wurde gesichert als " . date("Y-m-d_H-i-s") . ".sql im Ordner system/backup/");
        return $dumpfile;
    }

    /**
     * Static Class, Erstellen eines Objektes soll nicht möglich sein.
     */
    private function __construct(){}
}