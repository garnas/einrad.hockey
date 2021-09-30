<?php # -*- php -*-

class SchiriTest
{

    # Anzahl der Fragen in den einzelnen Kategorien festlegen:
    #  0 -> beliebige Kategorie
    #  1 -> Vor dem Spiel/Rund ums Spiel
    #  2 -> Schiedsrichterverhalten
    #  3 -> Handzeichen
    #  4 -> Penaltyschießen
    #  5 -> Vorfahrt
    #  6 -> Übertriebene Härte
    #  7 -> Eingriff ins Spiel
    #  8 -> Sonstige Fouls
    #  9 -> Torschüsse
    # 10 -> Zeitstrafen/Unsportlichkeiten
    # 11 -> Strafen
    #                             0  1  2  3  4  5  6  7  8  9 10 11
    public const anzahl_L = array(1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    public const anzahl_J = array(0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1);
    public const anzahl_B = array(0, 2, 3, 1, 1, 3, 3, 3, 6, 4, 1, 3);
    public const anzahl_F = array(0, 2, 3, 1, 1, 3, 3, 3, 6, 4, 1, 3);
    public const lev_infos = array(
        'L'=>array(
            'name'        => 'Lehrgang',
            'anzahl'      => self::anzahl_L,
            'timelimit'   => 10,  # in Minuten
            'richtig_min' => 1),  # Minimum zum Bestehen
        'J'=>array(
            'name'        => 'Junior',
            'anzahl'      => self::anzahl_J,
            'timelimit'   => 45,  # in Minuten
            'richtig_min' => 9),  # Minimum zum Bestehen
        'B'=>array(
            'name'        => 'Basis',
            'anzahl'      => self::anzahl_B,
            'timelimit'   => 45,  # in Minuten
            'richtig_min' => 25), # Minimum zum Bestehen
        'F'=>array(
            'name'        => 'Fortgeschrittene',
            'anzahl'      => self::anzahl_F,
            'timelimit'   => 30,  # in Minuten
            'richtig_min' => 25)  # Minimum zum Bestehen
    );

    #-------------------------------------------------------------------------

    # Get eine bestimmte Anzahl an zufälligen Fragen einer Kategorie für den Schiritest
    # aus der Datenbank.
    # @param $LJBF      # Lehrgang, Junior, Basis oder Fortgeschrittene?
    # @param $kategorie # Kategorie aus welcher die Fragen geholt werden sollen
    # @param $anzahl    # Anzahl der Fragen die geholt werden sollen
    # @param $frage_id  # ID einer bestimmten Frage, die geholt werden soll
    # @return array     # Fragen die zurückgegeben werden
    public static function get_fragen(string $LJBF, int $kategorie,
                                      int $anzahl, int $frage_id = 0): array
    {
        if ($kategorie === 0) {
            $sql = "
                SELECT *
                FROM schiri_test
                WHERE INSTR(LJBF, ?) > 0 AND bestaetigt = 'ja'
                ORDER BY RAND()
                LIMIT ?
                ";
            $result = db::$db->query($sql, $LJBF, $anzahl)->fetch();
        } else {
            $sql = "
                SELECT *
                FROM schiri_test
                WHERE INSTR(LJBF, ?) > 0 AND bestaetigt = 'ja' AND kategorie = ?
                ORDER BY RAND()
                LIMIT ?
                ";
            $result = db::$db->query($sql, $LJBF, $kategorie, $anzahl)->fetch();
        }
        if ($frage_id > 0) {
            $sql = "
                SELECT *
                FROM schiri_test
                WHERE frage_id = ?
                LIMIT ?
                ";
            $result = db::$db->query($sql, $frage_id, $anzahl)->fetch();
        }
        foreach ($result as $row) {
            # String in ein Array parsen:
            $row['richtig'] = preg_split('/[\s#\s]+/', $row['richtig']);
            for ($index = 1; $index <= 6; $index++) { # Index für die Antwortmöglichkeiten
                if (!empty($row['antwort_' . $index])) {
                    # zum Array "row" hinzufügen:
                    $row['antworten'][$index] = $row['antwort_' . $index];
                    unset($row['antwort_' . $index]); # wird nicht mehr gebraucht
                }
            }
            $fragen[$row['frage_id']] = $row;
        }

        return $fragen ?? []; # Rückgabe muss Array sein (leer falls $fragen undefiniert)
    }

    #-------------------------------------------------------------------------

    # offiziellen, personalisierten Test aus md5sum erzeugen:
    public static function offizieller_test(string $md5sum): array
    {
        $sql = "
            SELECT *
            FROM schiri_ergebnis
            WHERE md5sum = ?
            ";
        $result = db::$db->query($sql, $md5sum)->fetch();
        if (count($result)==1) {
            $pruefling = $result[0]['spieler_name'];
            $level = $result[0]['test_level'];
            $fragen_IDs = str_getcsv($result[0]['gestellte_fragen']);
            $fragen = [];
            foreach ($fragen_IDs as $frage_ID) {
                $fragen += self::get_fragen('DUMMY', 0, 1, $frage_ID);
            }
            $neu = ($result[0]['t_gestartet']==NULL);
        } else {
            exit('<H1>Ungültige URL (falsche md5sum)</H1>');
        }
        return [$pruefling, $level, $fragen, $neu];
    }
    
    #-------------------------------------------------------------------------

    # Frage anzeigen:
    public static function frage_anzeigen(int $frage_id, int $index, array $frage): void
    {
        if ($index>0) {
            $fragennummer = 'Frage Nr. ' . $index ;
        } else {
            $fragennummer = '';
        }
        echo '<h3 class="w3-topbar">' . $fragennummer .
            '<span style="float:right;color:gray;font-size:60%">(ID: ' .
            $frage_id . ')</span></h3>';
        echo '<h4>' . $frage['frage'] . '</h4>';
        if (!empty($frage['name_video'])) { # Video zur Frage:
            echo '<div style="max-width: 500px">';
            echo '<video class="w3-image w3-card" src="videos/' . $frage['name_video'] . '"';
            echo '       controls playsinline> Video zur Frage </video></div>';
        }
        if (!empty($frage['name_bild'])) { # Bild zur Frage:
            echo '<div style="max-width: 500px">';
            echo '<img alt="Bild zur Frage" class="w3-image w3-card"';
            echo '     src="bilder/' . $frage['name_bild'] . '"></div>';
        }
    }

    #-------------------------------------------------------------------------

    # Antwortmöglichkeiten anzeigen:
    public static function antworten_anzeigen(int $frage_id, array $frage): void
    {
        foreach ($frage['antworten'] as $index => $antwort) { ?>
            <p>
                <input name="abgabe[<?= $frage_id ?>][<?= $index ?>]"
                       value="<?= $index ?>"
                       id="<?= $frage_id . '*' . $index ?>"
                       type="checkbox"
                       class="w3-check"
                       style="cursor: pointer;">
                <label for="<?= $frage_id . '*' . $index ?>"
                       class="w3-hover-text-primary" style="cursor: pointer;">
                    <?= $antwort ?>
                </label>
            </p>
        <?php } # end foreach antworten
    }

    #-------------------------------------------------------------------------

    # Stimmen die gegebenen Antworten als Array mit den richtigen
    # Antworten aus der DB überein?
    public static function auswertung_anzeigen(int $frage_id, array $frage): void
    {
        $richtig = self::get_richtig($frage_id);
        foreach ($frage['antworten'] as $index => $antwort) {
            $antwort_user = isset($_POST['abgabe'][$frage_id][$index]);
            if ($antwort_user) { # diese Antwort angeklickt?
                echo '<p><i class="material-icons">check_circle_outline</i>';
            } else {
                echo '<p><i class="material-icons">radio_button_unchecked</i>';
            }
            $antwort_richtig = in_array($index, $richtig);
            if ($antwort_user xor $antwort_richtig) { # richtig beantwortet?
                echo '<span class="w3-text-red">';
                echo '<i class="material-icons">thumb_down</i></span>';
            } else {
                echo '<span class="w3-text-green">';
                echo '<i class="material-icons">thumb_up</i></span>';
            }
            if ($antwort_richtig) { # ist diese Antwort richtig?
                echo '<b>' . $antwort . '</b></p>';
            } else {
                echo '<span class="w3-text-grey"><s><i>' . $antwort .
                    '</i></s></span></p>';
            }
        }
        $antworten_user = $_POST['abgabe'][$frage_id] ?? []; # leer, wenn keine Antwort
        if (self::validate_frage($frage_id, $antworten_user)) {
            echo '<h3 class="w3-border-bottom w3-text-green">' .
                Html::icon("thumb_up", class:"md-36") .
                'Alles korrekt beantwortet!</h3>';
        } else {
            echo '<h3 class="w3-border-bottom w3-text-red">' .
                Html::icon("thumb_down", class: "md-36") .
                'Da war etwas falsch!</h3>';
        }
        echo '<p><b>Erklärung: </b>' . $frage['erklaerung'] . '</p>';
        if (!empty($frage['erklaerung_video'])) { # Video zur Frage:
            echo '<div style="max-width: 500px"><video class="w3-image w3-card"' .
                ' src="videos/' . $frage['erklaerung_video'] . '" controls playsinline>' .
                '</video></div>';
        }
        if (!empty($frage['erklaerung_bild'])) { # Bild zur Frage:
            echo '<div style="max-width: 500px">' .
                '<img alt="Bild zur Frage" class="w3-image w3-card"' .
                ' src="bilder/' . $frage['erklaerung_bild'] . '"></div>';
        }

        $regelnr = $frage['regelnr'];
        if (empty($regelnr)) {
            Html::message('notice', '(Keine Regelnummer für diese Frage)');
        } else {
            foreach (preg_split('/[\s#\s]+/', $regelnr) as $regelnr1) {
                [$nr, $part, $titel, $text] = self::get_regel($regelnr1);
                if (empty($nr)) {
                    Html::message('error',
                        'Regel |' . $regelnr1 . '| nicht in der Datenbank.');
                } else {
                    Html::message('info',
                        $text, 'Offizielle Regel ' . $nr . ": " . $titel, esc: false);
                }
            }
        }
    }

    #-------------------------------------------------------------------------

    # Was sind die richtigen Antworten?
    #
    # @param int $frage_id
    # @return array
    public static function get_richtig(int $frage_id): array
    {
        # Antworten aus der Datenbank lesen
        $sql = "
            SELECT richtig
            FROM schiri_test
            WHERE frage_id = ?
        ";
        $richtig = db::$db->query($sql, $frage_id)->fetch_one(); # String, # als Trennzeichen
        # Array mit den Nummern der richtigen Antwort:
        $richtig = preg_split('/[\s#\s]+/', $richtig);
        sort($richtig); # Sortieren, damit beide Arrays die gleiche Reihenfolge haben
        return $richtig;
    }

    #-------------------------------------------------------------------------

    # Stimmen die gegebenen Antworten als Array mit den richtigen
    # Antworten aus der DB überein?
    #
    # @param int $frage_id
    # @param array $user_antworten
    # @return bool
    public static function validate_frage(int $frage_id, array $user_antworten): bool
    {
        # Antworten aus der Datenbank lesen
        $sql = "
            SELECT richtig
            FROM schiri_test
            WHERE frage_id = ?
        ";
        # $result = db::readdb($sql);
        # $richtig = mysqli_fetch_assoc($result)['richtig']; # String, # als Trennzeichen
        $richtig = db::$db->query($sql, $frage_id)->fetch_one();
        # Array mit den Nummern der richtigen Antwort:
        $richtig = preg_split('/[\s#\s]+/', $richtig);
        # Vergleich der Arrays $richtig und $antworten
        sort($richtig); # Sortieren, damit beide Arrays die gleiche Reihenfolge haben
        sort($user_antworten);
        return $richtig == $user_antworten;
    }

    #-------------------------------------------------------------------------

    # Lade eine Regel aus der Datenbank:
    public static function get_regel(string $nummer0): array
    {
        $regeln = self::get_regelwerk();
        preg_match('/([0-9.]+)([a-z]*)/', $nummer0, $matches);
        $nummer = $matches[1];
        $part = $matches[2];
        $fulltext = $regeln[$nummer]['regeltext'];
        if (empty($part)) {
            $text = $fulltext;
        } else {
            $text = '...';
            foreach (str_split($part) as $onepart) {
                # U=non-greedy
                # https://www.php.net/manual/de/reference.pcre.pattern.modifiers.php
                preg_match('|<p part="' . $onepart . '">(.*)</p>|U', $fulltext, $textpart);
                $text .= '<br>' . $textpart[1] . '<br>...';
            }
        }
        return [$nummer, $part, $regeln[$nummer]['regeltitel'], $text];
    }

    #-------------------------------------------------------------------------

    # Lade Regelwerk aus der Datenbank:
    public static function get_regelwerk(): array
    {
        $sql = "
            SELECT *
            FROM regelwerk
        ";
        return db::$db->query($sql)->fetch('regelnummer');
    }

    #-------------------------------------------------------------------------

    # Testergebnis melden:
    public static function testergebnis_melden($fragen, $richtig, $abgabe): void
    {
        # Infos aus Datenbank holen:
        $sql = "
            SELECT spieler_name, spieler_email, test_level
            FROM schiri_ergebnis
            WHERE md5sum = ?
            ";
        $result = db::$db->query($sql, $_GET['md5sum'])->fetch();
        $test_level = $result[0]['test_level'];
        $pruefling  = $result[0]['spieler_name'];
        $email      = $result[0]['spieler_email'];
        # Ergebnis in MySQL entragen:
        $zeitstempel = date('Y-m-d H:i:s'); # heutiges Datum + Uhrzeit
        $antworten = json_encode($abgabe); #qqq Formattierung verbessern
        if ($richtig < self::lev_infos[$test_level]['richtig_min']) {
            $bestanden = 'Nein';
        } else {
            $bestanden = 'Ja';
        }
        $sql = "UPDATE schiri_ergebnis SET t_abgegeben = ?,
            gesetzte_antworten = ?, bestanden = ? WHERE md5sum = ?;";
        $params = [$zeitstempel, $antworten, $bestanden, $_GET['md5sum']];
        db::$db->query($sql, $params)->log();
        # Text der Email zusammenstellen:
        $text = "<p>Prüfling: " . $pruefling;
        $text .= "<P>Es wurden " . $richtig . " Fragen richtig beantwortet.";
        $index = 0;
        foreach ($fragen as $frage) {
            $text .= "<P>Frage Nr. " . ++$index . " (ID " . $frage['frage_id'] . "): ";
            $text .= $frage['frage'] . "<br>";
            $text .= "Richtige Antwort: " . implode(",",$frage['richtig']) . "<br>";
            $text .= "Antwort des Prüflings: " . implode(",",$abgabe[$index-1]) . "<br>";
        #echo '<pre>' . $text . '</pre>'; # qqq diese Zeile nur zum debugging aktivieren
        }
        # Email an Prüfling und Schiriausschuss senden:
        $mailer = MailBot::start_mailer();
        $mailer->setFrom(Env::SCHIRIMAIL);
        $mailer->addAddress($email, $pruefling);
        $mailer->addCC(Env::SCHIRIMAIL);
        $mailer->Subject = 'Testergebnis von ' . $pruefling; # Betreff 
        $mailer->Body = $text;
        if (MailBot::send_mail($mailer)) {
            Html::info("Die E-Mail wurde versandt.");
        } else {
            Html::error("FEHLER: E-Mail konnte nicht versendet werden.");
        }
    }

    #-------------------------------------------------------------------------


    ############## Beispiel-Vorschlag von Ansgar ######################
    ############## Nur ein Vorschlag! ######################

    private int $schiri_test_id;
    public array $pruefungs_fragen;
    public nSpieler $spieler;
    public string $test_level;
    public string $gestellte_fragen;
    public bool $error = false;

    public function __construct(){}

    /**
     * @return SchiriTest
     */
    public function set_pruefungs_fragen(): SchiriTest
    {
        $anzahl = self::lev_infos[$this->test_level]['anzahl'];            
        # vorschlag self::get_Fragen zu $this->get_fragen. $test_level
        # als Argument streichen und zu $this->test_level in der funktion 
        $this->pruefungs_fragen =
            self::get_fragen($this->test_level,  1, $anzahl[1])  +
            self::get_fragen($this->test_level,  2, $anzahl[2])  +
            self::get_fragen($this->test_level,  3, $anzahl[3])  +
            self::get_fragen($this->test_level,  4, $anzahl[4])  +
            self::get_fragen($this->test_level,  5, $anzahl[5])  +
            self::get_fragen($this->test_level,  6, $anzahl[6])  +
            self::get_fragen($this->test_level,  7, $anzahl[7])  +
            self::get_fragen($this->test_level,  8, $anzahl[8])  +
            self::get_fragen($this->test_level,  9, $anzahl[9])  +
            self::get_fragen($this->test_level, 10, $anzahl[10]) +
            self::get_fragen($this->test_level, 11, $anzahl[11]); 
        $this->set_gestellte_fragen(); # Ids in CSV-Form speichern
        $this->zeitstempel = date('Y-m-d H:i:s'); # heutiges Datum + Uhrzeit abspeichern
        $this->md5 = md5($this->gestellte_fragen . $this->zeitstempel); # für Test-URL
        return $this;
    }

    /**
     * @param nSpieler $spieler
     * @return SchiriTest
     */
    public function set_spieler(nSpieler $spieler): SchiriTest
    {
        $this->spieler = $spieler;

        return $this;
    }

    /**
     * @param string $test_level
     */
    public function set_level(string $test_level): SchiriTest
    {
        if (in_array($test_level, ['J', 'B', 'F'])) {
            $this->test_level = $test_level;
        } else {
            Html::error("Level nicht gefunden.");
            $this->error = true;
        }
        return $this;
    }

    /**
     * @param string $email
     */
    public function set_email(string $email): SchiriTest
    {
        $this->email = $email;
        return $this;
    }

    public function set_gestellte_fragen (): SchiriTest
    {
        $this->gestellte_fragen = implode(',', array_keys($this->pruefungs_fragen));
        return $this;
    }

    public function create(): bool|SchiriTest
    {
        if ($this->error) {
            return false;
        }

        $sql = "
            INSERT INTO schiri_ergebnis (md5sum, spieler_id, spieler_name, spieler_email, 
            gestellte_fragen, test_level, t_erstellt, saison, schiri_test_version)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, '1')
            ";
        $params = [$this->md5, $this->spieler->id(), $this->spieler->get_name(), $this->email,
            $this->gestellte_fragen, $this->test_level, $this->zeitstempel, Config::SAISON];

        db::$db->query($sql, $params)->log();

        $this->url = Env::BASE_URL . '/schiricenter/schiritest.php?md5sum=' . $this->md5;
        $this->schiri_test_id = db::$db->get_last_insert_id();

        return $this;
    }

    public function mail_on_create() {
        $levelname = self::lev_infos[$this->test_level]['name'];
        $anzahl = array_sum(self::lev_infos[$this->test_level]['anzahl']);
        $richtig_min = self::lev_infos[$this->test_level]['richtig_min'];
        $timelimit = self::lev_infos[$this->test_level]['timelimit'];
        $text = <<<Mail
Hallo {$this->spieler->get_name()},

dein Online Schiritest (Level: $levelname) ist jetzt erstellt
worden. Du kannst ihn hier starten:

$this->url

Sobald du die Webseite aufrufst, hast du $timelimit Minuten Zeit.

Achtung: Webseite nicht neu laden, während der Test ausgefüllt wird!

Von den $anzahl Fragen müssen mindestens $richtig_min richtig
beantwortet werden.

Viele Grüße
Der Schiriausschuss
Mail;
        Html::message('info', '<pre>' . $text . '</pre>',
            'Text der automatischen E-Mail:', esc:false);
        # Todo Ansgar mail versenden
        # Email an Prüfling senden:
        $mailer = MailBot::start_mailer();
        $mailer->setFrom(Env::SCHIRIMAIL); # Absender ist Schiriausschuss
        $mailer->addAddress($this->email, $this->spieler->get_name()); # Empfänger
        $mailer->addCC(Env::SCHIRIMAIL); # cc: an Schiriausschuss
        $mailer->Subject = 'Online Schiritest für ' . $this->spieler->get_name(); # Betreff
        $mailer->Body = $text; # Text der E-Mail
        if (MailBot::send_mail($mailer)) {
            return true; # E-mail erfolgreich versendet
        } else {
            return false; # Fehler beim Versenden der E-Mail
        }
    }

    public function ergebnis_speichern() {
        // todo Update schiritest ergebnis.......
    }

    // Beispiel getter
    public function get(int $schiri_test_id): null|object
    {
        $sql = "SELECT * FROM schiri_ergebnis WHERE schiri_test_id = ?";
        // Dies nimmt alle Spalten und füllt dann alle Attribute, die den gleichen Namen wie eine der Spalten hat.
        // Erst nachdem alle Attribute gesetzt worden sind, wird __construct() ausgeführt
        return db::$db->query($sql, $schiri_test_id)->fetch_object(__CLASS__);
    }
}
