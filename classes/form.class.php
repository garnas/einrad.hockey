<?php

/**
 * Class Form
 *
 * Für die Erstellung von Format-Elementen
 */
class Form
{

    /**
     * Fehlermeldungem werden in einer $_SESSION Variable gespeichert und beim nächsten Aufruf der HTML-Navigation
     * angezeigt
     *
     * Text der Fehlermeldung
     * @param $string
     */
    public static string $confetti = '';
    
    //Fehlermeldungem werden in einer $_SESSION Variable gespeichert
    public static function error($string)
    {
        //Falls $_SESSION noch nicht gesetzt wurde, wird sie als array deklariert
        if (!isset($_SESSION['e_messages'])) {
            $_SESSION['e_messages'] = [];
        }
        //argument wird dem array $_SESSION['e_messages'] hinzugefügt
        array_push($_SESSION['e_messages'], $string);
    }

    //Analog zur error funktion

    /**
     * Infomeldungem werden in einer $_SESSION Variable gespeichert und beim nächsten Aufruf der HTML-Navigation
     * angezeigt
     *
     * Text der Infomeldung
     * @param $string
     */
    public static function affirm($string)
    {
        if (!isset($_SESSION['a_messages'])) {
            $_SESSION['a_messages'] = [];
        }
        array_push($_SESSION['a_messages'], $string);
    }

    /**
     * Hinweismeldungen werden in einer $_SESSION Variable gespeichert und beim nächsten Aufruf der HTML-Navigation
     * angezeigt
     *
     * Text der Hinweismeldung
     * @param $string
     */
    public static function attention($string)
    {
        if (!isset($_SESSION['w_messages'])) {
            $_SESSION['w_messages'] = [];
        }
        array_push($_SESSION['w_messages'], $string);
    }

    /**
     * Hinweis-Kasten wird ins Html-Dokument geschrieben
     *
     * Text
     * @param $message
     * Überschrift
     * @param string $caption
     */
    public static function schreibe_attention($message, $caption = 'Hinweis')
    { ?>
        <div class='w3-card w3-panel w3-leftbar w3-border-yellow w3-pale-yellow'>
            <h3><?= $caption ?></h3>
            <p><?= $message ?></p>
        </div>
    <?php }

    /**
     * Fehler-Kasten wird ins Html-Dokument geschrieben
     *
     * Text
     * @param $message
     * Überschrift
     * @param string $caption
     */
    public static function schreibe_error($message, $caption = 'Fehler')
    {
        ?>
        <div class='w3-card w3-panel w3-leftbar w3-border-red w3-pale-red'>
            <h3><?= $caption ?></h3>
            <p><?= $message ?></p>
        </div>
        <?php
    }

    /**
     * Info-Kasten wird ins Html-Dokument geschrieben
     *
     * Text
     * @param $message
     * Überschrift
     * @param string $caption
     */
    public static function schreibe_affirm($message, $caption = 'Info')
    {
        ?>
        <div class='w3-card w3-panel w3-leftbar w3-border-green w3-pale-green'>
            <h3><?= $caption ?></h3>
            <p><?= $message ?></p>
        </div>
        <?php
    }

    /**
     * Meldungen aus $_SESSION von Form::error etc. werden ins Html-Dokument geschrieben
     */
    public static function schreibe_meldungen()
    {
        // Hinweise
        if (isset($_SESSION['w_messages'])) {
            foreach ($_SESSION['w_messages'] as $message) {
                self::schreibe_attention($message);
            }
            unset($_SESSION['w_messages']);
        }
        // Fehler
        if (isset($_SESSION['e_messages'])) {
            foreach ($_SESSION['e_messages'] as $message) {
                self::schreibe_error($message);
            }
            unset($_SESSION['e_messages']);
        }
        // Infos
        if (isset($_SESSION['a_messages'])) {
            foreach ($_SESSION['a_messages'] as $message) {
                self::schreibe_affirm($message);
            }
            unset($_SESSION['a_messages']);
        }
    }

    /**
     * Erstellt einen HTML-Link.
     *
     * @param string $link
     * @param string $bezeichnung
     * @param bool $extern
     * @return string
     */
    public static function link(string $link, string $bezeichnung = '', bool $extern = false): string
    {
        if (empty($link)){
            return '';
        }
        if (empty($bezeichnung)) {
            $bezeichnung = $link;
        }
        if ($extern) {
            $new_tab = 'target="_blank" rel="noopener noreferrer"';
        } else {
            $new_tab = '';
        }
        return "<a href='$link' class='no w3-text-primary w3-hover-text-secondary' style='white-space: nowrap;' $new_tab>$bezeichnung</a>";
    }

    /**
     * Erstellt eine HTML-Datalist aller Ligateams.
     *
     * @return string
     */
    public static function datalist_teams(): string
    {
        $return = "<datalist id='teams'>";
        $liste = Team::get_ligateams_name();
        foreach ($liste as $teamname) {
            $return .= "<option value='$teamname'>";
        }
        $return .= "</datalist>";
        return $return;
    }

    /**
     * Erststellt anklickbare Email-Adressen
     *
     * @param string|array $email Wenn $email ein Array ist, wird es in einen mit "," getrennten String umgewandelt
     *                            Wenn $email leer ist, so wird ein leerer String zurückgegeben
     * @param string|null $name Wenn $name nicht übergeben wird ist, dann wird $email als Bezeichner übernommen
     * @return string
     */
    public static function mailto(string|array $email, string $name = NULL): string
    {
        if (empty ($email)){
            return '';
        }
        if (is_array($email)) {
            $email = implode(',', $email);
        }
        if ($name === NULL) {
            $name = $email;
        }
        return "<a href='mailto:$email' class='no w3-text-blue w3-hover-text-secondary' style='white-space: nowrap;'><i class='material-icons'>mail</i> $name</a>";
    }

    /**
     * Wandelt eine Saisonnummer in einen Jahreszahl-Saisonstring um
     *
     * @param int $saison
     * 0 = Saison 1995
     * @return string
     */
    public static function get_saison_string($saison = Config::SAISON): string
    {
        // Sollte keine Saisonzahl übergeben werden, dann wird der Input zurückgegeben.
        if (!is_numeric($saison)) {
            return $saison;
        }
        if ($saison == 25) {
            return "2020 (Corona-Saison)";
        }
        if ($saison > 25) {
            $saison_jahr = 1994 + $saison;
            $saison_jahr_next = $saison_jahr + 1;
            return substr($saison_jahr, -2) . "/" . substr($saison_jahr_next, -2);
        }
        return (string)(1995 + $saison);
    }

    /**
     * Fügt einen Countdown ein
     *
     * mit strtotime() lesbares Datum
     * @param string $date
     *
     * HTML-ID welcher der Countdown haben soll (falls multiple Countdowns eignefügt werden)
     * @param string $id
     */
    public static function countdown(string $date, $id = 'countdown')
    {
        ?>
        <script>countdown('<?=date("Y-m-d\TH:i:s", strtotime($date))?>', '<?=$id?>')</script>
        <div id='countdown' class="w3-xlarge w3-text-primary" style='white-space: nowrap;'>
                <span class="w3-center w3-margin-right" style="display: inline-block">
                    <span id='countdown_days'>--</span>
                    <span class="w3-small w3-text-grey" style="display: block">Tage</span>
                </span>
            <span class="w3-center w3-margin-right" style="display: inline-block">
                    <span id='countdown_hours'>--</span>
                    <span class="w3-small w3-text-grey" style="display: block">Stunden</span>
                </span>
            <span class="w3-center w3-margin-right" style="display: inline-block">
                    <span id='countdown_minutes'>--</span>
                    <span class="w3-small w3-text-grey" style="display: block">Minuten</span>
                </span>
            <span class="w3-center" style="display: inline-block">
                    <span id='countdown_seconds'>--</span>
                    <span class="w3-small w3-text-grey" style="display: block">Sekunden</span>
                </span>
        </div>
        <?php
    }

    /**
     * Schreibt einen Log-Eintrag in die Log-Datei
     * Existiert die Log-Datei nicht, so wird sie erstellt.
     *
     * Name der Logdatei
     * @param string $file_name
     * Was in die Logdatei geschrieben werden soll
     * @param string $line
     */
    public static function log(string $file_name, string $line)
    {
        $path = Config::BASE_PATH . '/system/logs/';
        //SQL-Logdatei erstellen/beschreiben
        $log_file = fopen($path . $file_name, "a");
        $line = date('[Y-M-d H:i:s e]: ') . $line . "\n";
        fwrite($log_file, $line);
        fclose($log_file);
    }
    
    /** Fügt einen Confetti-Effekt hinzu.
     *
     * Muss vor dem HTML-Code aufgerufen werden.
     *
     * Wie lange soll der Effekt in ms anhalten?
     * Keine Zeitbegrenzung bei $timeout = 0
     * @param int $timeout
     *
     * Anzahl der Konfettis liegt zufällig zwischen $min und $max
     * @param int $min
     * @param int $max
     */
    public static function set_confetti(int $min = 40, int $max = 90, $timeout = 0){
        self::$confetti =   "
                            <script src = '../javascript/confetti/confetti.js'></script>
                            <script>confetti.start($timeout, $min, $max)</script>
                            ";
    }
}