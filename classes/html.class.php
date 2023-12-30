<?php

/**
 * Class Form
 *
 * Für die Erstellung von Format-Elementen
 */
class Html
{
    public static string $titel = 'Deutsche Einradhockeyliga';
    public static string $content = 'Jeder Einradhockeybegeisterte soll in der Deutschen Einradhockeyliga die Möglichkeit haben, sein Hobby in '
    . 'einem sportlichen Rahmen auszuüben. Die Einradhockeyliga hat maßgeblich zur Verbreitung von Einradhockey '
    . 'beigetragen und ist in ihrer Art und Konstanz weltweit einzigartig.';
    /**
     * HTML-Anzeige
     */
    public static string $page_width = "980px";

    /**
     * Get Pfad fürs Hintergrundbild der Navigation
     * Nach einiger Zeit ein neues Hintergrundbild in der Navigation anzeigen
     *
     * Quelle: https://stackoverflow.com/questions/1761252/how-to-get-random-image-from-directory-using-php
     */
    public static function get_hintergrund_bild(): string
    {
        $_SESSION['bild_navigation']['zeit'] = ($_SESSION['bild_navigation']['zeit'] ?? time());
        if (
            !isset($_SESSION['bild_navigation']['path'])
            || (time() - $_SESSION['bild_navigation']['zeit']) > 600
        ){
            $imagesDir = Env::BASE_PATH . '/public/bilder/hintergrund/';
            $images = glob($imagesDir . '*.{jpg,JPG,jpeg,png,gif}', GLOB_BRACE);
            $randomImage = $images[array_rand($images)];
            $_SESSION['bild_navigation']['path'] = Env::BASE_URL . '/bilder/hintergrund/' . basename($randomImage);
            $_SESSION['bild_navigation']['zeit'] = time();
        }
        return $_SESSION['bild_navigation']['path'];
    }
    /**
     * Fehlermeldungem werden in einer $_SESSION Variable gespeichert und beim nächsten Aufruf der HTML-Navigation
     * angezeigt
     *
     * Text der Fehlermeldung
     * @param string $string Text
     * @param string $caption Überschrift
     * @param bool $esc false: Inhalt wird nicht escaped
     */
    public static function error(string $string, string $caption = "Fehler", bool $esc = true): void
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
     * @param bool $esc false: Inhalt wird nicht escaped
     */
    public static function info(string $string, string $caption = "Info", bool $esc = true): void
    {
        $_SESSION['messages'][] = ['type' => 'info', 'text' => $string, 'caption' => $caption, 'escape' => $esc];
    }

    /**
     * Hinweismeldungen werden in einer $_SESSION Variable gespeichert und beim nächsten Aufruf der HTML-Navigation
     * angezeigt
     *
     * @param string $string Text
     * @param string $caption Überschrift
     * @param bool $esc false: Inhalt wird nicht escaped
     */
    public static function notice(string $string, string $caption = "Hinweis", bool $esc = true): void
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
    public static function message(string $type, string $message, string|null $caption = '', bool $esc = true): void
    {
        if ($caption === '') {
            $caption = match ($type) {
                'error' => 'Fehler',
                'info' => 'Info',
                'notice' => 'Hinweis'
            };
        }

        $color = match ($type) {
            'error' => 'w3-border-red w3-pale-red',
            'info' => 'w3-border-green w3-pale-green',
            'notice' => 'w3-border-yellow w3-pale-yellow'
        };

        $caption = ($esc) ? db::escape($caption) : $caption;
        $caption = is_null($caption) ? '' : "<h3>$caption</h3>";
        $message = ($esc) ? db::escape($message) : $message;

        echo "
        <div class='w3-card w3-panel w3-leftbar $color'>
            $caption
            <div class='w3-section'>$message</div>
        </div>
        ";
    }

    /**
     * Meldungen aus $_SESSION von Form::error etc. werden ins Html-Dokument geschrieben
     */
    public static function print_messages(): void
    {
        // messages Schreiben
        if (!isset($_SESSION['messages'])) {
            return;
        }
        foreach ($_SESSION['messages'] as $message) {
            self::message($message['type'], $message['text'], $message['caption'], $message['escape']);
        }
        unset($_SESSION['messages']);
    }

    /**
     * Erstellt einen HTML-Link.
     *
     * @param string|null $link
     * @param string|null $bezeichnung
     * @param bool $extern
     * @param string $icon Material-Icon
     * @return string
     */
    public static function link(?string $link, ?string $bezeichnung = '', bool $extern = false, string $icon = ''): string
    {
        if (empty($link)) {
            return '';
        } // Für Schleifen

        $new_tab = ($extern) ? 'target="_blank" rel="noopener noreferrer"' : '';
        $bezeichnung = (!empty($icon)) ? self::icon($icon) . ' ' . $bezeichnung : $bezeichnung;
        $bezeichnung = empty($bezeichnung) ? $link : $bezeichnung;

        return
            "<a href='$link' 
               class='no w3-text-primary w3-hover-text-secondary' 
               style='white-space: nowrap;' 
               $new_tab>
               $bezeichnung
            </a>";
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

    public static function datalist_turnier_ausrichter(string $field, int $team_id): string
    {
        $whitelist = ["organisator", "handy"];
        if (!in_array($field, $whitelist)) {
            trigger_error("Whitelist-Error Datalist Turnier Ausrichter $field", E_USER_ERROR);
        }
        $sql = "
            SELECT $field
            FROM turniere_details
            INNER JOIN turniere_liga on turniere_details.turnier_id = turniere_liga.turnier_id
            WHERE ausrichter = ? AND saison >= ? AND $field != ''
            GROUP BY $field
            ORDER BY COUNT($field) DESC;
        ";
        $liste = Db::$db->query($sql, $team_id, Config::SAISON - 3)->list($field);

        $return = "<datalist id='list_$field'>";
        foreach ($liste as $value) {
            $return .= "<option value='$value'>";
        }
        $return .= "</datalist>";
        return $return;
    }

    public static function datalist_turnier(string $field): string
    {
        $whitelist = ["hallenname", "strasse", "plz", "ort", "haltestellen"];
        if (!in_array($field, $whitelist)) {
            trigger_error("Whitelist-Error Datalist Turnier Allgemein $field", E_USER_ERROR);
        }

        $sql = "
            SELECT TRIM($field) AS $field
            FROM turniere_liga 
            INNER JOIN turniere_details on turniere_liga.turnier_id = turniere_details.turnier_id
            WHERE TRIM($field) != '' AND TRIM($field) != '00000'
            GROUP BY TRIM($field)
            ORDER BY COUNT($field) DESC;
        ";
        $liste = Db::$db->query($sql)->list($field);
        $return = "<datalist id='list_$field'>";
        foreach ($liste as $value) {
            $return .= "<option value='$value'>";
        }
        $return .= "</datalist>";
        return $return;
    }

    public static function turnier_organisator_javascript_array($team_id): false|string
    {
        $sql = "
            SELECT TRIM(organisator) AS organisator,
                   TRIM(handy) AS handy, d1.turnier_id
            FROM turniere_details as d1
            INNER JOIN turniere_liga as tl on d1.turnier_id = tl.turnier_id
            WHERE organisator != ''
              AND tl.ausrichter = ?
              AND tl.saison >= ?
              AND d1.turnier_id =   (select max(d2.turnier_id)
                                  from turniere_details as d2
                                  inner join turniere_liga as tl2 on d2.turnier_id = tl2.turnier_id
                                  where TRIM(d1.organisator) = TRIM(d2.organisator) and tl2.ausrichter = ?)
            GROUP BY TRIM(organisator)
        ";
        $result = Db::$db->query($sql, $team_id, Config::SAISON - 3, $team_id)->fetch("organisator");
        return json_encode($result);
    }

    public static function turnier_adressen_javascript_array(): false|string
    {
        $sql = "
            SELECT TRIM(hallenname) AS hallenname,
                   TRIM(strasse) AS strasse, 
                   TRIM(plz) as plz, 
                   TRIM(ort) as ort, 
                   TRIM(haltestellen) as haltestellen
            FROM turniere_details as d1
            WHERE hallenname != '' AND turnier_id =   (select max(d2.turnier_id)
                                                       from turniere_details as d2
                                                       where TRIM(d1.hallenname) = TRIM(d2.hallenname))
            GROUP BY TRIM(hallenname)
        ";
        $result = Db::$db->query($sql)->fetch("hallenname");
        return json_encode($result);
    }

    /**
     * Erststellt anklickbare Email-Adressen
     *
     * @param string|array|null $email Wenn $email ein Array ist, wird es in einen mit "," getrennten String umgewandelt
     *                            Wenn $email leer ist, so wird ein leerer String zurückgegeben
     * @param string|null $name Wenn $name nicht übergeben wird ist, dann wird $email als Bezeichner übernommen
     * @return string
     */
    public static function mailto(null|string|array $email, null|string $name = NULL): string
    {
        if (empty ($email)) {
            return '';
        }

        $email = is_array($email) ? implode(',', $email) : $email;

        $email = e($email);
        $name = e($name);
        return "<a href='mailto:$email' class='no w3-text-primary w3-hover-text-secondary' style='white-space: nowrap;'>"
            . self::icon("mail") . ' ' . ($name ?? $email)
            . "</a>";
    }

    /**
     * Wandelt eine Saisonnummer in einen Jahreszahl-Saisonstring um
     *
     * @param int|string $saison
     * 0 = Saison 1995
     * @return string
     */
    public static function get_saison_string(int|string $saison = Config::SAISON): string
    {
        // Sollte keine Saisonzahl übergeben werden, dann wird der Input zurückgegeben.
        if (!is_numeric($saison)) {
            return $saison;
        }
        if ($saison === 25) {
            return "2020 (Corona-Saison)";
        }
        if ($saison > 25) {
            $saison_jahr = 1994 + $saison;
            $saison_jahr_next = $saison_jahr + 1;
            return substr($saison_jahr, -2) . "/" . substr($saison_jahr_next, -2);
        }
        return (string) (1995 + $saison);
    }

    /**
     * Fügt einen Countdown ein
     *
     * @param int $date Zeit als Unix-Time
     * @param string $id Welche ID das Countdownelement haben soll
     */
    public static function countdown(int $date, string $id = 'countdown'): void
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
        <script>countdown('<?= $date ?>', '<?= $id ?>')</script>
        <?php
    }

    /**
     * Fügt einen Confetti-Effekt hinzu.
     * Muss nach dem HTML-Header-Include aufgerufen werden!
     *
     * @param int $timeout Wie lange soll der Effekt in ms anhalten? (0: unendlich lange)
     * @param int $min Anzahl der Konfettis liegt zufällig zwischen $min und $max
     * @param int $max
     */
    public static function set_confetti(int $min = 40, int $max = 120, $timeout = 3000): void
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
     * @param string $class
     * @return string
     */
    public static function icon(string $icon, int $vertical_align = 0, int $font_size = 0, string $tag = 'p',
                                string $class = ''): string
    {
        $style = '';
        if ($tag !== 'p') {
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
        return "<span class='material-icons $class' " . ($style ?? '') . ">$icon</span>";
    }

    /**
     * Gibt die zwei farbigen Trikotpunkte aus
     *
     * @param string|null $color_1
     * @param string|null $color_2
     * @return string Html-Code
     */
    public static function trikot_punkt(null|string $color_1 = null, null|string $color_2 = null): string
    {
        if(!empty($color_1)) {
            $punkt_1 = "
            <span class = 'w3-card-4'
                style = 'height:14px;width:14px; background-color:$color_1;border-radius:50%;display:inline-block;'>
            </span>";
        }
        if(!empty($color_2)) {
            $punkt_2 = "
            <span class = 'w3-card-4'
                style = 'height:14px;width:14px; background-color:$color_2;border-radius:50%;display:inline-block;'>
            </span>";
        }
        return ($punkt_1 ?? '') . ($punkt_2 ?? '');
    }
    
    public static function include_widget_bot($server = '494602271447842856', $channel = '984536643107180594') {
        echo "
        <widgetbot
                server='$server'
                channel='$channel'
                width='100%'
                height='350'
        >
        </widgetbot>
        <script src='https://cdn.jsdelivr.net/npm/@widgetbot/html-embed'></script>
        ";
    }


}