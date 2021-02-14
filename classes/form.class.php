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
     * @param string $string Text
     * @param string $caption Überschrift
     * @param bool $esc
     */
    public static function error(string $string, string $caption = "Fehler", bool $esc = true)
    {
        $_SESSION['messages'][] = ['type' => 'error', 'text' => $string, 'caption' => $caption, 'escape' => $esc];
    }

    /**
     * Infomeldungem werden in einer $_SESSION Variable gespeichert und beim nächsten Aufruf der HTML-Navigation
     * angezeigt
     *
     * Text der Infomeldung
     * @param string $string Text
     * @param string $caption Überschrift
     * @param bool $esc
     */
    public static function info(string $string, string $caption = "Info", bool $esc = true)
    {
        $_SESSION['messages'][] = ['type' => 'info', 'text' => $string, 'caption' => $caption, 'escape' => $esc];
    }

    /**
     * Hinweismeldungen werden in einer $_SESSION Variable gespeichert und beim nächsten Aufruf der HTML-Navigation
     * angezeigt
     *
     * @param string $string Text
     * @param string $caption Überschrift
     * @param bool $esc
     */
    public static function notice(string $string, string $caption = "Hinweis", bool $esc = true)
    {
        $_SESSION['messages'][] = ['type' => 'notice', 'text' => $string, 'caption' => $caption, 'escape' => $esc];
    }

    /**
     * Fehler-, Info-, oder Hinweis-Kasten wird ins Html-Dokument geschrieben
     *
     * @param string $type error || info || notice
     * @param string $message Text
     * @param string|null $caption Überschrift
     * @param bool $esc Standardmäßig wird der String gegen XSS escaped
     */
    public static function message(string $type, string $message, string|null $caption = null, bool $esc = true)
    {
        if (is_null($caption)) $caption = match ($type) {
            'error' => 'Fehler',
            'info' => 'Info',
            'notice' => 'Hinweise'
        };

        $color = match ($type) {
            'error' => 'w3-border-red w3-pale-red',
            'info' => 'w3-border-green w3-pale-green',
            'notice' => 'w3-border-yellow w3-pale-yellow'
        };

        $caption = ($esc) ? dbi::escape($caption) : $caption;
        $message = ($esc) ? dbi::escape($message) : $message;

        echo "
        <div class='w3-card w3-panel w3-leftbar $color'>
            <h3>$caption</h3>
            <div class='w3-section'>$message</div>
        </div>
        ";
    }

    /**
     * Meldungen aus $_SESSION von Form::error etc. werden ins Html-Dokument geschrieben
     */
    public static function print_messages()
    {
        // messages Schreiben
        if (!isset($_SESSION['messages'])) return;
        foreach ($_SESSION['messages'] as $message) {
            self::message($message['type'], $message['text'], $message['caption'], $message['escape']);
        }
        unset($_SESSION['messages']);
    }

    /**
     * Erstellt einen HTML-Link.
     *
     * @param string $link
     * @param string $bezeichnung
     * @param bool $extern
     * @param string $icon Material-Icon
     * @return string
     */
    public static function link(string $link, string $bezeichnung = '', bool $extern = false, string $icon = ''): string
    {
        if (empty($link)) return ''; // Für Schleifen
        $new_tab = ($extern) ? 'target="_blank" rel="noopener noreferrer"' : '';
        $bezeichnung = (!empty($icon)) ? self::icon($icon) . ' ' . $bezeichnung : $bezeichnung;
        $bezeichnung = empty($bezeichnung) ? $link : $bezeichnung;
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
        $liste = Team::get_liste();
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
        if (empty ($email)) {
            return '';
        }
        if (is_array($email)) {
            $email = implode(',', $email);
        }
        if ($name === NULL) {
            $name = $email;
        }
        return "<a href='mailto:$email' class='no w3-text-primary w3-hover-text-secondary' style='white-space: nowrap;'><i class='material-icons'>mail</i> $name</a>";
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

    public static function countdown($date, $id = 'countdown')
    { //TODO Return als String
        ?>
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
        <script>countdown('<?=date("Y-m-d\TH:i:s", strtotime($date))?>', '<?=$id?>')</script>
        <?php
    }

    public static function log($file_name, $line)
    {
        $path = '../../system/logs/';
        //SQL-Logdatei erstellen/beschreiben
        $log_file = fopen($path . $file_name, "a");
        $line = date('[Y-M-d H:i:s e]: ') . $line . "\n";
        fwrite($log_file, $line);
        fclose($log_file);
    }

    /**
     * Fügt einen Confetti-Effekt hinzu.
     * Muss nach dem HTML-Header-Include aufgerufen werden!
     *
     * @param int $timeout Wie lange soll der Effekt in ms anhalten? (0: unendlich lange)
     * @param int $min Anzahl der Konfettis liegt zufällig zwischen $min und $max
     * @param int $max
     */
    public static function show_confetti(int $min = 40, int $max = 90, $timeout = 0)
    {
        echo "
            <script src = '../javascript/confetti/confetti.js'></script>
            <script>confetti.start($timeout, $min, $max)</script>
            ";
    }

    /**
     * Fügt ein Material-Icon hinzu
     * @param string $icon Icon-Bezeichnung
     * @param int $vertical_align
     * @param int $font_size
     * @param string $tag
     * @return string
     */
    public static function icon(string $icon, int $vertical_align = 0, int $font_size = 0, string $tag = 'p'): string
    {
        $style = '';
        if ($tag != 'p') {
            $style = match ($tag) {
                'h1', 'h2' => 'style="font-size: 31px; vertical-align: -19%;"',
                'h3' => 'style="vertical-align: -16%;"',
                default => ''
            };
        } elseif (!(empty($vertical_align) && empty($font_size))) {
            $style = "style='"
                . (empty($vertical_align) ? "" : "vertical-align: -" . $vertical_align . "%;")
                . (empty($font_size) ? "" : "font-size: " . $font_size . "px;")
                . "'";
        }
        return "<span class='material-icons' " . ($style ?? '') . ">$icon</span>";
    }
}