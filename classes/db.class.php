<?php
//Datenbank-Zugangsdaten können in config.class.php geändert werden!

//$sql = Sql-Befehl als Text
class db {
  
  public static $link; //Verbindung zur Datenbank
  public static $log_file = "log_db.log";
  
  //Verbindung wird bei der Erstellung des Objektes geöffnet. Das erste db-Objekt wird in first.logic.php erstellt.
  function __construct($db = Config::DATABASE)
  {
    self::$link = new mysqli(Config::HOST_NAME, Config::USER_NAME, Config::PASSWORD, $db);
    if (self::$link -> connect_errno) {
      Form::log(self::$log_file,"Verbindung zum MySQL Server fehlgeschlagen: ".mysqli_connect_error());
      die('<h2>Verbindung zum MySQL Server fehlgeschlagen: '.mysqli_connect_error().'<br><br>Wende dich bitte an <span style="color:red;">' . Config::TECHNIKMAIL . '</span> wenn dieser Fehler auch in den nächsten Stunden noch besteht.</h2>');
    }
  }

  /*Verbindung wird bei der Zerstörung des Objektes geschlossen
  Sinnvoll, da __destruct automatisch aufgerufen wird, wenn auf das Objekt nicht mehr referenziert wird.
  Dadurch werden DB-Verbindungen nur solange wie nötig aufrecht erhalten.*/
  function __destruct()
  {
    self::$link->close();
  }

  /*Sanitizing eines gesamten Arrays - In der Regel $_POST / $_GET in first.logic.php
  Siehe https://www.php.net/manual/de/mysqli.real-escape-string.php*/
  public static function sanitize($input)
  {
    if (is_array($input)){
      foreach ($input as $key => $value){
          $input[$key] = self::sanitize($value); //Rekursion
      }
    }else{
      $input = trim($input);
      $input = self::$link->real_escape_string($input);
    }
    return $input;
  }

  //Verhindert XSS durch das einbringen html-Entities
  public static function escape($input)
  {
    if (empty($input)){
      return $input;
    }
    if (is_array($input)){
      foreach ($input as $key => $value){
          $key = self::escape($key);
          $value = self::escape($value);
          $output[$key] = $value; //Rekursion
      }
    }else{
      $output = htmlspecialchars($input);
    }
    return $output;
  }

  /*auto_increment wert einer Sql-Tabelle erkennen. Alle IDs werden über auto_increment erstellt
  $tabelle ist der name der Tabelle in der SQL datenbank*/
  public static function get_auto_increment ($tabelle)
  {
    $sql="  SELECT AUTO_INCREMENT
            FROM  INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = '" . Config::DATABASE . "'  
            AND   TABLE_NAME   = '$tabelle';";
    $auto_incr = self::readdb($sql);
    $auto_incr = mysqli_fetch_assoc($auto_incr);
    $auto_incr = $auto_incr["AUTO_INCREMENT"];
    return $auto_incr;
  }

  //funktion zum lesen der sql datenbank, sie gibt ein mysqli-objekt zurück
  //dieses mysqli-objekt muss immer in assoziatives array umgewandelt werden
  public static function readdb($sql)
  {
    if (mysqli_connect_errno()) {
      Form::log(self::$log_file,"Lesen der Datenbank fehlgeschlagen: ".mysqli_connect_error());
      die('<h2>Verbindung zum MySQL Server fehlgeschlagen: '.mysqli_connect_error().'<br><br>Wende dich bitte an <span style="color:red;">' . Config::TECHNIKMAIL . '</span> wenn dieser Fehler auch in den nächsten Stunden noch besteht.</h2>');
    }
    return self::$link->query($sql);
  }

  //funktion zum schreiben in die sql datenbank
  public static function writedb($sql)
  {
    //SQL-Logdatei erstellen/beschreiben
    $autor_string = implode(" | ", array_filter([$_SESSION['teamname'] ?? '', $_SESSION['la_login_name'] ?? '', $_SESSION['ligabot'] ?? '']));
    $log = $autor_string . ":\n" . trim($sql);
    Form::log(self::$log_file, $log);

    //Keine Verbindung zum SQL-Server möglich
    if (mysqli_connect_errno()) {
      $error_text = 'Verbindung zum MySQL Server fehlgeschlagen: ' . mysqli_connect_error();
      Form::log(self::$log_file, $error_text);
      die('<h2>Verbindung zum MySQL Server fehlgeschlagen: ' . mysqli_connect_error() . '<br><br>Wende dich bitte an <span style="color:red;">' . Config::TECHNIKMAIL . '</span> wenn dieser Fehler auch in den nächsten Stunden noch besteht.</h2>');
    }

    //Beschreiben der Datenbank nicht möglich
    if (!self::$link->query($sql) === TRUE) {
        $error_text = 'Fehlgeschlagen: ' . self::$link->error;
        Form::log(self::$log_file, $error_text);
        Form::error("Fehler beim Beschreiben der Datenbank. " . Form::mailto(Config::TECHNIKMAIL));
        //Debug Form::error($sql);
        die();
    }
  }
  
  public static function db_sichern()
  {
    $dbname = Config::DATABASE;
    $dbuser = Config::USER_NAME;
    $dbpassword = Config::PASSWORD;
    $dbhost = Config::HOST_NAME;
    $dumpfile = "../../system/backups/" . $dbname . "." . date("Y-m-d_H-i-s") . ".sql"; //Dateiname der Sicherungskopie
    exec("mysqldump --user=$dbuser --password=$dbpassword --host=$dbhost $dbname > $dumpfile");
    Form::affirm("Datenbank wurde gesichert als " . date("Y-m-d_H-i-s") .".sql im Ordner system/backup/");
    return $dumpfile;
  }

  //Schreibt alle deklarierten Variablen ins Dokument //true=1 false=0
  //Gibt auch das Dokument und Zeile der Variablen aus
  public static function debug($input = "all")
  {
    if ($input === "all"){
      $input = $GLOBALS;
    }
    $backtrace = debug_backtrace();
    Form::affirm('<p>File: ' . $backtrace[0]['file'] . '<br>Line: ' . $backtrace[0]['line'] . '</p><pre>' . print_r($input, true) . '</pre>');
  }
}