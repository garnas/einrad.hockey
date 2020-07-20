<?php
class Form {
  
  //Fehlermeldungem werden in einer $_SESSION Variable gespeichert
  public static function error($string)
  {
    //Falls $_SESSION noch nicht gesetzt wurde, wird sie als array deklariert
    if (!isset($_SESSION['e_messages'])){
      $_SESSION['e_messages'] = array();
    }
    //argument wird dem array $_SESSION['e_messages'] hinzugefügt
    array_push($_SESSION['e_messages'], $string);
  }

  //Analog zur error funktion
  public static function affirm($string)
  {
    if (!isset($_SESSION['a_messages'])){
      $_SESSION['a_messages'] = array();
    }
    array_push($_SESSION['a_messages'], $string);
  }

  //Analog zur error funktion
  public static function attention($string)
  {
    if (!isset($_SESSION['w_messages'])){
      $_SESSION['w_messages'] = array();
    }
    array_push($_SESSION['w_messages'], $string);
  }
  //Errors werden ins Html-Dokument geschrieben
  public static function schreibe_errors()
  {
    if (isset($_SESSION['e_messages'])){
      foreach ($_SESSION['e_messages'] as $message){
        echo "<div class='w3-card w3-panel w3-leftbar w3-border-red w3-pale-red'>
                <h3>Fehler</h3>
                <p>$message</p>
              </div>";
      }
      unset($_SESSION['e_messages']); //Nachdem die Fehlermeldungen dargestellt worden sind, wird dass Array geleert
    }
  }

  //Confirmations werden ins Html-Dokument geschrieben
  public static function schreibe_affirms()
  {
    if (isset($_SESSION['a_messages'])){
      foreach ($_SESSION['a_messages'] as $message){
        echo "<div class='w3-card w3-panel w3-leftbar w3-border-green w3-pale-green'>
                <h3>Info</h3>
                <p>$message</p>
              </div>";
      }
      unset($_SESSION['a_messages']); //Nachdem die Meldungen dargestellt worden sind, wird dass Array geleert
    }
  }

  //Attentions werden ins Html-Dokument geschrieben
  public static function schreibe_attentions()
  {
    if (isset($_SESSION['w_messages'])){
      foreach ($_SESSION['w_messages'] as $message){
        echo "<div class='w3-card w3-panel w3-leftbar w3-border-yellow w3-pale-yellow'>
                <h3>Hinweis</h3>
                <p>$message</p>
              </div>";
      }
      unset($_SESSION['w_messages']); //Nachdem die Meldungen dargestellt worden sind, wird dass Array geleert
    }
  }

  //Erstellt eine HTML-Datalist aller Ligateams.
  public static function link($link, $bezeichnung = '')
  {
    if (empty($bezeichnung)){$bezeichnung = $link;}
    return "<a href='$link' class='no w3-text-primary w3-hover-text-secondary'>$bezeichnung</a>";
  }

  //Erstellt eine HTML-Datalist aller Ligateams.
  public static function datalist_teams()
  {
    $return = "<datalist id='teams'>";
    $liste = Team::list_of_all_teams();
    foreach ($liste as $teamname){
        $return .= "<option value='$teamname'>";
    }
    $return .= "</datalist>";
    return $return;
  }
  
  //Erststellt anklickbare Email-Adressen
  public static function mailto($email, $name = '')
  {
    if (is_array($email)){
      $email = implode(',',$email);
    }
    if (empty($name)){
      $name = $email;
    }
    return "<a href='mailto:$email' class='no w3-text-blue w3-hover-text-secondary' style='white-space: nowrap;'><i class='material-icons'>mail</i> $name</a>";
  }

  //Funktion für Saisonumstellung auf eine Saison über zwei Jahre
  //1 = Saison 1995
  public static function get_saison_string($saison = Config::SAISON)
  {
    //Sollte zum Beispiel ein String übergeben werden, dann wird genau dieser String auch wieder rausgeworfen.
    if (!is_numeric($saison)){
        return $saison;
    }
    if ($saison == 25){
      return "2020 (Corona-Saison)";
    }
    if ($saison > 25){
        $saison_jahr = 1994 + $saison;
        $saison_jahr_next = $saison_jahr + 1;
        return substr($saison_jahr, -2) . "/" . substr($saison_jahr_next,-2);
    }
        return 1995 + $saison;
  }
}