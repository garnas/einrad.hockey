<?php

/**
 * Datenbankklasse
 *
 * Datenbank-Zugangsdaten können in config.class.php geändert werden!
 *
 * $sql = Sql-Befehl als String
 */
class db
{

    public static string $log_file = "log_db.log";
    public static mysqli $link; //Verbindung zur Datenbank

    /**
     * db constructor.
     *
     * Verbindung wird bei der Erstellung des Objektes geöffnet. Das erste db-Objekt wird in first.logic.php erstellt.
     *
     * Datenbankname
     * @param string $db
     */
    function __construct($db = Config::DATABASE)
    {
        self::$link = new mysqli(Config::HOST_NAME, Config::USER_NAME, Config::PASSWORD, $db);
        if (self::$link->connect_errno) {
            Form::log(self::$log_file, "Verbindung zum MySQL Server fehlgeschlagen: " . mysqli_connect_error());
            die('<h2>Verbindung zum MySQL Server fehlgeschlagen: ' . mysqli_connect_error() . '<br><br>Wende dich bitte an <span style="color:red;">' . Config::TECHNIKMAIL . '</span> wenn dieser Fehler auch in den nächsten Stunden noch besteht.</h2>');
        }
    }

    /**
     * Beendet die Verbindung bei der automtischen Zerstörung des Objektes
     */
    function __destruct()
    {
        self::$link->close();
    }


    /**
     * Sanitizing eines gesamten Arrays
     * In der Regel $_POST / $_GET in first.logic.php
     *
     * Quelle: https://www.php.net/manual/de/mysqli.real-escape-string.php
     *
     * @param $input
     * @return mixed
     */
    public static function sanitize($input): mixed
    {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = self::sanitize($value); //Rekursion
            }
        } else {
            $input = trim($input);
            $input = is_numeric($input) ? $input : self::$link->real_escape_string($input);
        }
        return $input;
    }

    /**
     * Verhindert XSS durch das einbringen html-Entities
     *
     * @param mixed $input
     * @return mixed
     */
    public static function escape(mixed $input): mixed
    {
        if (empty($input)) {
            return $input;
        }
        if (is_array($input)) {
            $output = [];
            foreach ($input as $key => $value) {
                $key = self::escape($key);
                $value = self::escape($value);
                $output[$key] = $value; //Rekursion
            }
        } else {
            $output = htmlspecialchars($input);
        }
        return $output;
    }

    /**
     * Nächste ID, die vergeben wird, finden
     *
     * auto_increment wert einer Sql-Tabelle erkennen. Alle IDs werden über auto_increment erstellt
     *
     * @param $tabelle //Tabellenname
     * @return int
     */
    public static function get_auto_increment($tabelle): int
    {
        $sql = "  SELECT AUTO_INCREMENT
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '" . Config::DATABASE . "'  
            AND   TABLE_NAME   = '$tabelle';";
        $auto_incr = self::readdb($sql);
        $auto_incr = mysqli_fetch_assoc($auto_incr);
        $auto_incr = $auto_incr["AUTO_INCREMENT"];
        return $auto_incr;
    }

    /**
     * Funktion zum Lesen der Datenbank
     *
     * @param string $sql
     * @return mysqli_result|bool
     */
    public static function read(string $sql): mysqli_result|bool
    {
        #$before = microtime(true);
        if (mysqli_connect_errno()) {
            Form::log(self::$log_file, "Lesen der Datenbank fehlgeschlagen: " . mysqli_connect_error());
            die('<h2>Verbindung zum MySQL Server fehlgeschlagen: ' . mysqli_connect_error() . '<br><br>Wende dich bitte an <span style="color:red;">' . Config::TECHNIKMAIL . '</span> wenn dieser Fehler auch in den nächsten Stunden noch besteht.</h2>');
        }

        #$after = microtime(true);
        #$backtrace = array_reverse(array_column(debug_backtrace(), 'function'));
        #array_pop($backtrace);
        #Form::log('db_last.log', implode(" | ", $backtrace) . " -- ". round($after - $before, 4) . " s " . preg_replace("/\h+/", " ", $sql));
        return self::$link->query($sql);
    }

    /**
     * Funktion zum schreiben in die Datenbank
     * 
     * @param string $sql
     */
    public static function write(string $sql, $anonym = false)
    {
        //SQL-Logdatei erstellen/beschreiben
        $autor_string = implode(" | ", array_filter([$_SESSION['teamname'] ?? '', $_SESSION['la_login_name'] ?? '', $_SESSION['ligabot'] ?? '']));
        $sql = trim(preg_replace("/(^\h+|\h+$)/m",'',$sql)); // Schönere Formatierung
        $log = $autor_string . "\n" . (($anonym) ? 'Anonyme Query' : $sql);
        Form::log(self::$log_file, $log);

        //Keine Verbindung zum SQL-Server möglich
        if (mysqli_connect_errno()) {
            $error_text = 'Verbindung zum MySQL Server fehlgeschlagen: ' . mysqli_connect_error();
            Form::log(self::$log_file, $error_text);
            die('<h2>Verbindung zum MySQL Server fehlgeschlagen: ' . mysqli_connect_error() . '<br><br>Wende dich bitte an <span style="color:red;">' . Config::TECHNIKMAIL . '</span> wenn dieser Fehler auch in den nächsten Stunden noch besteht.</h2>');
        }

        // Beschreiben der Datenbank nicht möglich? $sql wird im if schon ausgeführt
        if (!self::$link->query($sql)) {
            $error_text = 'Fehlgeschlagen: ' . self::$link->error;
            Form::log(self::$log_file, $error_text);
            Form::error("Fehler beim Beschreiben der Datenbank. " . Form::mailto(Config::TECHNIKMAIL));
            //Debug Form::error($sql);
            die();
        }
  }

    /**
     * Sichert die Datenbank
     *
     * Name der Sicherung
     * @return string
     */
    public static function db_sichern(): string
    {
        $dbname = Config::DATABASE;
        $dbuser = Config::USER_NAME;
        $dbpassword = Config::PASSWORD;
        $dbhost = Config::HOST_NAME;
        $dumpfile = "../../system/backups/" . $dbname . "." . date("Y-m-d_H-i-s") . ".sql"; //Dateiname der Sicherungskopie
        exec("mysqldump --user=$dbuser --password=$dbpassword --host=$dbhost $dbname > $dumpfile");
        Form::affirm("Datenbank wurde gesichert als " . date("Y-m-d_H-i-s") . ".sql im Ordner system/backup/");
        return $dumpfile;
    }

    /**
     * Schreibt alle deklarierten Variablen unter die Navigation in HTML //true=1 false=0
     * Schreibt auch das Dokument und Zeile der Variablen aus
     * z.B. db::debug(get_defined_vars(), true);
     *
     * Zu debuggende Variable
     * @param string $input
     *
     * Sollen Typen angezeigt werden?
     * @param bool $types
     */
    public static function debug($input = NULL, $types = false)
    {
        if ($input === NULL) {
            $input = $GLOBALS;
        }
        $backtrace = debug_backtrace();
        // Show Types?
        if ($types) {
            ob_start();
            var_dump($input);
            $string = ob_get_clean();
        } else {
            $string = print_r($input, true);
        }
        Form::affirm('<p>File: ' . $backtrace[0]['file'] . '<br>Line: ' . $backtrace[0]['line'] . '</p><pre>' . $string . '</pre>');
    }
}